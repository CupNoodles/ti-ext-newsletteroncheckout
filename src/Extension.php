<?php

declare(strict_types=1);

namespace CupNoodles\NewsletterOnCheckout;

use Igniter\Cart\Classes\CheckoutForm;
use Igniter\Cart\Models\Order;
use Igniter\Frontend\Models\MailchimpSettings;
use Igniter\Frontend\Models\Subscriber;
use Igniter\System\Classes\BaseExtension;
use Igniter\User\Facades\Auth;
use Igniter\User\Models\Customer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Override;
use Throwable;
use WeakMap;

/**
 * Adds an opt-in "Subscribe to our newsletter" checkbox to the checkout. Customers who tick it are
 * added to the FrontEnd extension's subscriber list (and Mailchimp, when configured) once their
 * order's payment has gone through.
 */
class Extension extends BaseExtension
{
    public const string FIELD = 'subscribe_to_newsletter';

    /** Opt-ins waiting for their order to be saved */
    protected WeakMap $pendingOptIns;

    #[Override]
    public function boot(): void
    {
        $this->pendingOptIns = new WeakMap;

        Event::listen('checkout.form.extendFieldsBefore', $this->addCheckboxField(...));

        // The order may not have an ID yet, so remember the opt-in on the order instance...
        Event::listen('igniter.checkout.beforeSaveOrder', function(Order $order, array $data): void {
            if (!empty($data[self::FIELD]) && ($email = $this->subscriberEmail($data))) {
                $this->pendingOptIns[$order] = $email;
            }
        });

        // ...then keep it until the payment has gone through, which may be in a later request
        // (eg. after a redirect to the payment provider).
        Event::listen('igniter.checkout.afterSaveOrder', function(Order $order): void {
            $email = $this->pendingOptIns[$order] ?? null;
            unset($this->pendingOptIns[$order]);

            if ($email) {
                Cache::put(self::cacheKey($order), $email, now()->addDays(7));
            } else {
                // Unticked when the customer submitted the order again
                Cache::forget(self::cacheKey($order));
            }
        });

        Event::listen('admin.order.paymentProcessed', function(Order $order): void {
            if ($email = Cache::pull(self::cacheKey($order))) {
                self::subscribe($email, $order->customer);
            }
        });
    }

    protected function addCheckboxField(mixed $form): void
    {
        if (!$form instanceof CheckoutForm || self::customerIsSubscribed(Auth::customer())) {
            return;
        }

        $form->fields[self::FIELD] = [
            'label' => 'lang:cupnoodles.newsletteroncheckout::default.label_subscribe',
            'placeholder' => 'lang:cupnoodles.newsletteroncheckout::default.checkbox_label',
            'tab' => 'terms',
            'type' => 'checkbox',
            'cssClass' => 'col-sm-12',
            'options' => [],
        ];

        $form->config['rules'][self::FIELD] = ['sometimes', 'nullable', 'boolean'];
    }

    protected function subscriberEmail(array $data): ?string
    {
        $email = Auth::customer()?->email ?? ($data['email'] ?? null);

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    public static function customerIsSubscribed(?Customer $customer): bool
    {
        if (!$customer) {
            return false;
        }

        return $customer->newsletter || Subscriber::where('email', $customer->email)->exists();
    }

    /**
     * Never throws: a newsletter problem must not get in the way of an order.
     */
    public static function subscribe(string $email, ?Customer $customer = null): void
    {
        try {
            if ($customer && !$customer->newsletter) {
                $customer->newsletter = true;
                $customer->saveQuietly();
            }

            $listId = MailchimpSettings::isConfigured() ? MailchimpSettings::get('list_id') : null;
            Subscriber::subscribe($email, $listId ?: null);
        } catch (Throwable $e) {
            Log::warning('Newsletter on Checkout: could not subscribe '.$email.': '.$e->getMessage());
        }
    }

    protected static function cacheKey(Order $order): string
    {
        return 'cupnoodles.newsletteroncheckout.order.'.$order->getKey();
    }
}

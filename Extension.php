<?php 

namespace CupNoodles\NewsletterOnCheckout;

use System\Classes\BaseExtension;
use System\Classes\ExtensionManager;
use Igniter\Flame\Exception\ApplicationException;
use Igniter\Frontend\Models\Subscriber;
use Igniter\Frontend\Models\MailchimpSettings;
use Igniter\Frontend\Components\Newsletter;
use Event;
use Mailchimp;


class Extension extends BaseExtension
{
    /**
     * Returns information about this extension.
     *
     * @return array
     */
    public function extensionMeta()
    {
        return [
            'name'        => 'NewsletterOnCheckout',
            'author'      => 'CupNoodles',
            'description' => 'Add an opt-in Newsletter subscription checkbox on checkout screen.',
            'icon'        => 'fas fa-envelope',
            'version'     => '1.0.0'
        ];
    }

    /**
     * Register method, called when the extension is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {

        Event::listen('igniter.checkout.beforeSaveOrder', function($order, $data){

            if(isset($data['subscribe_to_newsletter'])){

                $subscribe = Subscriber::firstOrNew(['email' => $data['email']]);
                $subscribe->fill($data);
                $subscribe->save();
        
                if (!strlen($apiKey = MailChimpSettings::get('api_key')))
                    return;
                
                
                $newsletter = new Newsletter();
                $listId = $newsletter->property('listId', MailChimpSettings::get('list_id'));
                $doubleOptIn = (bool)$newsletter->property('doubleOptIn', TRUE);
                $updateExisting = (bool)$newsletter->property('updateExisting', FALSE);
                $email = ['email' => $subscribe->email];
    

                try {
                    $mailchimp = new Mailchimp($apiKey);
                    $mailchimp->lists->subscribe(
                        $listId, $email, null, 'html', $doubleOptIn, $updateExisting
                    );
                }
                catch (\MailChimp_Error $e) {
                    // presumably, you care more about sales than newsletter subscriptions, so don't throw errors on mailchimp failure
                    //throw new ApplicationException('MailChimp returned the following error: '.$e->getMessage());
                }
        
                Event::fire('igniter.frontend.subscribed', [$subscribe, $data]);
        
            }
        });

    }


    /**
     * Registers any front-end components implemented in this extension.
     *
     * @return array
     */
    public function registerComponents()
    {

    }



}

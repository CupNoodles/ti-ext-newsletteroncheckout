## Subscribe to newsletter button on TI checkout

> **Known issues (TastyIgniter v3 version).** This version is no longer maintained. For TastyIgniter
> v4, use the `master` branch.
>
> - **Mailchimp sign-ups may silently fail.** This version, like TastyIgniter v3's FrontEnd
>   extension, uses Mailchimp's API 2.0 (the `mailchimp/mailchimp` 2.x library). Mailchimp replaced
>   it with API 3.0 in 2015, lists it as deprecated and unsupported, and retired the related Export
>   API 2.0 in June 2023. Mailchimp errors are deliberately ignored so they can't block an order, so
>   if Mailchimp rejects the request you won't see an error. Check your Mailchimp audience for recent
>   checkout sign-ups. Everyone who ticks the box is still saved in TastyIgniter's own subscriber
>   list (the `igniter_frontend_subscribers` table), so you can export them from there and import
>   them into Mailchimp.
> - Customers are subscribed when the order is submitted, before payment, so a customer whose
>   payment then fails is still subscribed.
> - The checkbox has to be added to your theme's checkout form by hand (see Usage).

Suuuuuper simple extension to listen for a 'subscribe_to_newsletter' form input field on checkout.

## Installation

Copy this into your extension folder as `/extensions/cupnoodles/newsletteroncheckout/`. 

## Usage

Simply add a checkbox anywhere in the checkout form with the name 'subscribe_to_newsletter', example below:

```
        <div class="form-group">
            <input type="checkbox" name="subscribe_to_newsletter" id="subscribe_to_newsletter" class="form-control" value="" />
            <label for="subscribe_to_newsletter">@lang('cupnoodles.newsletteroncheckout::default.checkbox_label')</label>
        </div>
```

Obviously, you need to set up your Mailchimp settings before this will take any effect. 
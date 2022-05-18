## Subscribe to newsletter button on TI checkout

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
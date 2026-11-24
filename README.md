# Newsletter on Checkout for TastyIgniter

Adds an opt-in **"Subscribe to our newsletter (opt-out at any time)"** checkbox to the checkout,
next to the terms and conditions. Customers who tick it are added to your newsletter list once their
order has been placed and paid:

- TastyIgniter's own subscriber list, the same one the newsletter subscribe form uses (FrontEnd
  extension)
- your **Mailchimp** audience, if Mailchimp is set up in **System > Settings > Mailchimp Settings**
- for signed-in customers, the **Newsletter** option on their account is turned on too

The checkbox is never ticked by default. Signed-in customers who are already subscribed don't see
it. A problem with Mailchimp never stops an order going through: it's written to the system log
instead.

> Using TastyIgniter v3? Use the [`v3` branch](https://github.com/CupNoodles/ti-ext-newsletteroncheckout/tree/v3),
> but read its known issues first: its Mailchimp connection uses an API Mailchimp no longer
> supports.

## Requirements

- TastyIgniter v4.5 or later, with the Cart, User and FrontEnd extensions (installed by default)
- A theme that uses TastyIgniter's standard checkout form, such as Orange or a child theme of it.
  No theme changes are needed.

## Installation

### From GitHub (git clone)

From your TastyIgniter root folder, clone the extension into
`extensions/cupnoodles/newsletteroncheckout`. The folder name matters: TastyIgniter finds extensions
at `extensions/<vendor>/<name>`.

```bash
git clone https://github.com/CupNoodles/ti-ext-newsletteroncheckout.git extensions/cupnoodles/newsletteroncheckout
php artisan igniter:up
```

To update later:

```bash
cd extensions/cupnoodles/newsletteroncheckout
git pull
cd ../../..
php artisan igniter:up
```

### With Composer, from GitHub

```bash
composer config repositories.cupnoodles-newsletteroncheckout vcs https://github.com/CupNoodles/ti-ext-newsletteroncheckout
composer require cupnoodles/ti-ext-newsletteroncheckout:dev-master
php artisan igniter:up
```

Once a version is tagged, you can require `^4.0` instead of `dev-master`.

### Upgrading from v3

Remove the `subscribe_to_newsletter` checkbox you added to your theme's checkout form: the extension
now adds it itself. Re-enter your Mailchimp API key and audience (list) ID in **System > Settings >
Mailchimp Settings** if they aren't already there.

## Usage

There's nothing to set up beyond Mailchimp (optional). Without Mailchimp, subscribers are only kept
in TastyIgniter's subscriber list.

Customers are subscribed when the order's payment goes through: straight away for cash orders, and
after the payment provider confirms card payments. An order whose payment fails or is abandoned
doesn't subscribe anyone.

To change the checkbox wording, open the admin's **Languages** page (`/admin/languages`), edit your
language and search its translations for `checkbox_label` in the `cupnoodles.newsletteroncheckout`
group.

## License

MIT. See [LICENSE](LICENSE).

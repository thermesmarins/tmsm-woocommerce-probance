### 1.0.2: September 23th, 2024
* Fix deprecated error for php 8.2

### 1.0.1: October 31st, 2018
* Remove rgblank function dependency (Gravity Forms is not required)

### 1.0.0: October 31st, 2018
* Adds a checkbox to subscribe to newsletter on checkout page
* Syncs data only if checkbox is checked by user
* Checkbox is unchecked by default
* If checkbox is unchecked, does not unsubscribe the user if the user is already subscribed
* Gets the billing fields: billing_email, billing_first_name, billing_last_name
* Compatibility with TMSM WooCommerce Billing Fields (billing_birthday, billing_title fields)
* Select the optin field the users will subscribe
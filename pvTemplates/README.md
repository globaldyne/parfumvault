# Perfumer's Vault Templates

## Default Template (`pvDefault.html`)

The `pvDefault.html` file serves as the default template for the application. It is used to render the UI when no custom template is provided.

> **Note:** Currently, only the login screen is supported.

### Placeholders in `pvDefault.html`

The following placeholders are used in the `pvDefault.html` file. These placeholders are dynamically replaced by the application at runtime:

- `{{lang}}`: Specifies the language of the document (e.g., `en` for English).
- `{{theme}}`: Defines the theme of the application (e.g., `light` or `dark`).
- `{{meta_description}}`: Meta description for the page, dynamically generated based on the product and version.
- `{{author}}`: The author of the application, typically set to `perfumersvault`.
- `{{title}}`: The title of the page, dynamically generated based on the product and context.
- `{{favicon_32}}`: Path to the 32x32 favicon image.
- `{{favicon_16}}`: Path to the 16x16 favicon image.
- `{{jquery_js}}`: Path to the jQuery JavaScript file.
- `{{bootstrap_js}}`: Path to the Bootstrap JavaScript file.
- `{{custom_js}}`: Path to the custom JavaScript file for additional functionality.
- `{{sb_admin_css}}`: Path to the SB Admin CSS file.
- `{{bootstrap_css}}`: Path to the Bootstrap CSS file.
- `{{vault_css}}`: Path to the Vault-specific CSS file.
- `{{fontawesome_css}}`: Path to the FontAwesome CSS file for icons.
- `{{body_class}}`: CSS class applied to the `<body>` tag for styling.
- `{{content}}`: The main content of the page, dynamically injected by the application.
- `{{product_url}}`: URL of the product's official website.
- `{{product_name}}`: Name of the product, dynamically set.
- `{{version}}`: Version of the application, including commit information.
- `{{appstore_pv}}`: URL to the Perfumers Vault app on the App Store.
- `{{appstore_pv_img}}`: Path to the image for the Perfumers Vault app link.
- `{{appstore_aroma}}`: URL to the AromaTrack app on the App Store.
- `{{appstore_aroma_img}}`: Path to the image for the AromaTrack app link.
- `{{copyright_year}}`: The current year, dynamically set.

---

## Language File (`lang/en.php`)

The `lang/en.php` file contains all the text strings used in the application. These strings are dynamically loaded and used to populate the UI, making it easier to support multiple languages.

### Structure of the Language File

The language file is a PHP file that returns an associative array. Each key in the array corresponds to a specific text string used in the application.

### Example

Here is an example of the structure of the language file:

```php
<?php
return [
    'forgot_password_title' => 'Forgot Password',
    'email_placeholder' => 'name@example.com',
    'email_label' => 'Email address',
    'password_placeholder' => 'Password',
    'password_label' => 'Password',
    'full_name_placeholder' => 'Full name',
    'full_name_label' => 'Full name',
    'close_button' => 'Close',
    'reset_password_button' => 'Reset Password',
    'continue_with_sso' => 'Continue with SSO',
    'or_separator' => 'or',
    'forgot_password_link' => 'Forgot Password?',
    'create_account_link' => 'Create an Account!',
    'register_user_title' => 'Please register a user',
    'register_button' => 'Register',
    'sign_in_title' => 'Sign In',
    'sign_in_button' => 'Sign In',
];
```

### Adding New Text Strings

To add new text strings:

1. Open the `lang/en.php` file.
2. Add a new key-value pair to the array. The key should be a descriptive identifier, and the value should be the text string.

Example:

```php
'new_feature_title' => 'Welcome to the New Feature',
```

### Using Text Strings in the Application

To use a text string from the language file:

1. Mount the `lang` directory as a Docker volume to ensure your changes persist:
    ```bash
    docker run -v /path/to/your/lang:/app/lang your-docker-image
    ```
2. Create your own `en.php` file in the mounted `lang` directory:
    ```php
    <?php
    return [
         'forgot_password_title' => 'Forgot Password',
         // Add your custom text strings here
    ];
    ```


### Overriding the Default Template

Users can override the default template by mounting the `pvTemplates` directory and adding their own HTML file. To do this:

1. Create a custom HTML file with the desired structure and styling.
2. Mount the `pvTemplates` directory to your environment.
3. Replace or add your custom HTML file in the mounted directory.

The application will prioritize the custom template over the default `pvDefault.html` if it is present in the directory.

### Example

To override the default template, you can add a file named `customTemplate.html` to the `pvTemplates` directory. Ensure the file follows the required structure for placeholders and dynamic content injection.

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Template</title>
    <!-- Add your custom styles and scripts -->
</head>
<body>
    <!-- Custom content -->
</body>
</html>
```

# Perfumer's Vault Templates

## Default Template (`pvDefault.html`)

The `pvDefault.html` file serves as the default template for the application. It is used to render the UI when no custom template is provided.

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

By doing this, you can fully customize the appearance and behavior of the application.

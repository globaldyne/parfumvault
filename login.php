<?php
define('pvault_panel', true);
define('__ROOT__', dirname(__FILE__));

require_once(__ROOT__.'/inc/opendb.php');
require_once(__ROOT__.'/inc/product.php');
require_once(__ROOT__.'/inc/settings.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is already logged in
if (isset($_SESSION['parfumvault'])) {
    header('Location: /index.php');
    exit;
}

require_once(__ROOT__.'/func/template_contents.php');

// Ensure global variables are available
global $product, $ver, $commit, $system_settings;

// Load the template
$template = file_get_contents(__ROOT__ . '/pvTemplates/pvDefault.html');

// Define placeholders and their replacements
$placeholders = [
    '{{lang}}' => 'en',
    '{{theme}}' => 'light',
    '{{meta_description}}' => htmlspecialchars($product . ' - ' . $ver),
    '{{author}}' => 'perfumersvault',
    '{{title}}' => htmlspecialchars($product . ' - Log In or Sign Up'),
    '{{favicon_32}}' => '/img/favicon-32x32.png',
    '{{favicon_16}}' => '/img/favicon-16x16.png',
    '{{jquery_js}}' => '/js/jquery/jquery.min.js',
    '{{bootstrap_js}}' => '/js/bootstrap.bundle.min.js',
    '{{custom_js}}' => '/js/custom.js',
    '{{sb_admin_css}}' => '/css/sb-admin-2.css',
    '{{bootstrap_css}}' => '/css/bootstrap.min.css',
    '{{vault_css}}' => '/css/vault.css',
    '{{fontawesome_css}}' => '/css/fontawesome-free/css/all.min.css',
    '{{body_class}}' => 'bg-gradient-primary',
    '{{content}}' => generateContent($conn),
    '{{product_url}}' => 'https://www.perfumersvault.com',
    '{{product_name}}' => htmlspecialchars($product),
    '{{version}}' => htmlspecialchars($ver . " " . $commit),
    '{{discord_url}}' => 'https://discord.gg/WxNE8kR8ug',
    '{{appstore_pv}}' => 'https://apps.apple.com/us/app/perfumers-vault/id1525381567',
    '{{appstore_pv_img}}' => '/img/appstore/get_pv.png',
    '{{appstore_aroma}}' => 'https://apps.apple.com/us/app/aromatrack/id6742348411',
    '{{appstore_aroma_img}}' => '/img/appstore/get_aroma_track.png',
    '{{copyright_year}}' => date('Y'),
];

// Escape curly braces for preg_replace
$escaped_placeholders = array_map(function ($key) {
    return '/' . preg_quote($key, '/') . '/';
}, array_keys($placeholders));

// Replace placeholders in the template
$output = preg_replace($escaped_placeholders, array_values($placeholders), $template);

// Output the final HTML
echo $output;

?>
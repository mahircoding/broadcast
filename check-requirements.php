<?php

// System Requirements Check for Laravel cPanel Deployment
// Run this file via browser to check if your hosting meets Laravel requirements

echo "<h1>🔍 Laravel System Requirements Check</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

$checks = [];

// PHP Version
$checks['PHP Version'] = [
    'required' => '8.1.0',
    'current' => phpversion(),
    'status' => version_compare(phpversion(), '8.1.0', '>=') ? 'success' : 'error'
];

// Required Extensions
$required_extensions = [
    'openssl', 'pdo', 'mbstring', 'tokenizer', 'xml', 'ctype',
    'json', 'bcmath', 'curl', 'fileinfo', 'gd'
];

foreach ($required_extensions as $ext) {
    $checks["Extension: $ext"] = [
        'required' => 'Yes',
        'current' => extension_loaded($ext) ? 'Loaded' : 'Not Loaded',
        'status' => extension_loaded($ext) ? 'success' : 'error'
    ];
}

// Database Extensions
$db_extensions = ['pdo_mysql', 'mysqli'];
foreach ($db_extensions as $ext) {
    $checks["Database: $ext"] = [
        'required' => 'Yes (one required)',
        'current' => extension_loaded($ext) ? 'Loaded' : 'Not Loaded',
        'status' => extension_loaded($ext) ? 'success' : 'warning'
    ];
}

// Directory Permissions
$writable_dirs = [
    'storage/app',
    'storage/framework',
    'storage/logs',
    'bootstrap/cache'
];

foreach ($writable_dirs as $dir) {
    if (is_dir($dir)) {
        $writable = is_writable($dir);
        $checks["Writable: $dir"] = [
            'required' => '755 or 777',
            'current' => $writable ? 'Writable' : 'Not Writable',
            'status' => $writable ? 'success' : 'error'
        ];
    }
}

// Configuration Checks
$checks['allow_url_fopen'] = [
    'required' => 'On',
    'current' => ini_get('allow_url_fopen') ? 'On' : 'Off',
    'status' => ini_get('allow_url_fopen') ? 'success' : 'warning'
];

$checks['max_execution_time'] = [
    'required' => '300+',
    'current' => ini_get('max_execution_time') . 's',
    'status' => ini_get('max_execution_time') >= 300 ? 'success' : 'warning'
];

$checks['memory_limit'] = [
    'required' => '128M+',
    'current' => ini_get('memory_limit'),
    'status' => 'info'
];

$checks['upload_max_filesize'] = [
    'required' => '2M+',
    'current' => ini_get('upload_max_filesize'),
    'status' => 'info'
];

// Display Results
echo "<table>";
echo "<tr><th>Requirement</th><th>Required</th><th>Current</th><th>Status</th></tr>";

$total = count($checks);
$passed = 0;
$errors = 0;

foreach ($checks as $name => $check) {
    $status_class = $check['status'];
    $status_text = ucfirst($check['status']);

    if ($check['status'] === 'success') $passed++;
    if ($check['status'] === 'error') $errors++;

    echo "<tr>";
    echo "<td>$name</td>";
    echo "<td>{$check['required']}</td>";
    echo "<td>{$check['current']}</td>";
    echo "<td class='$status_class'>$status_text</td>";
    echo "</tr>";
}

echo "</table>";

// Summary
echo "<h2>📊 Summary</h2>";
echo "<p><strong>Total Checks:</strong> $total</p>";
echo "<p><strong>Passed:</strong> <span class='success'>$passed</span></p>";
echo "<p><strong>Errors:</strong> <span class='error'>$errors</span></p>";

if ($errors > 0) {
    echo "<div class='error'>";
    echo "<h3>❌ Action Required</h3>";
    echo "<p>Please fix the errors above before deploying Laravel. Contact your hosting provider if needed.</p>";
    echo "</div>";
} else {
    echo "<div class='success'>";
    echo "<h3>✅ Ready for Laravel!</h3>";
    echo "<p>Your hosting environment meets Laravel requirements.</p>";
    echo "</div>";
}

// Additional Info
echo "<h2>📋 Next Steps</h2>";
echo "<ol>";
echo "<li>Upload Laravel files</li>";
echo "<li>Configure .env file</li>";
echo "<li>Run deployment script</li>";
echo "<li>Test application</li>";
echo "</ol>";

echo "<h2>🔗 Useful Links</h2>";
echo "<ul>";
echo "<li><a href='DEPLOYMENT-GUIDE.md'>Full Deployment Guide</a></li>";
echo "<li><a href='QUICK-FIX.md'>Quick Fix Guide</a></li>";
echo "</ul>";

// Server Info
echo "<h2>🖥️ Server Information</h2>";
echo "<table>";
echo "<tr><td>Server Software</td><td>" . $_SERVER['SERVER_SOFTWARE'] . "</td></tr>";
echo "<tr><td>PHP Version</td><td>" . phpversion() . "</td></tr>";
echo "<tr><td>Server OS</td><td>" . php_uname() . "</td></tr>";
echo "<tr><td>Document Root</td><td>" . $_SERVER['DOCUMENT_ROOT'] . "</td></tr>";
echo "</table>";

?>

<?php
echo "<h1>PHP Environment Diagnostic</h1>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Loaded Configuration File (php.ini):</strong> " . (php_ini_loaded_file() ?: 'None') . "</p>";
echo "<p><strong>PDO Drivers:</strong> " . implode(', ', pdo_drivers()) . "</p>";

if (extension_loaded('pdo_mysql')) {
    echo "<p style='color: green; font-weight: bold;'>✅ PDO MySQL driver is ENABLED.</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ PDO MySQL driver is NOT ENABLED.</p>";
    echo "<p>To fix this:</p>";
    echo "<ol>";
    echo "<li>Open the <strong>php.ini</strong> file listed above.</li>";
    echo "<li>Search for <code>;extension=pdo_mysql</code></li>";
    echo "<li>Remove the semicolon (;) at the beginning of the line.</li>";
    echo "<li>Save the file.</li>";
    echo "<li><strong>Restart your PHP server</strong> in the terminal.</li>";
    echo "</ol>";
}
?>
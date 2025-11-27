<?php
// Configure session path to local tmp directory (Same as index.php)
$sessionPath = __DIR__ . '/../tmp';
if (!file_exists($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}
ini_set('session.save_path', $sessionPath);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);

session_start();

echo "<h1>Session Diagnostic</h1>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Save Path:</strong> " . session_save_path() . "</p>";
echo "<p><strong>Session Status:</strong> " . session_status() . "</p>";

if (!isset($_SESSION['test_count'])) {
    $_SESSION['test_count'] = 0;
    echo "<p>Session variable 'test_count' initialized.</p>";
} else {
    $_SESSION['test_count']++;
    echo "<p>Session variable 'test_count' incremented to: " . $_SESSION['test_count'] . "</p>";
}

echo "<p><a href='test_session.php'>Reload Page</a> to see if count increases.</p>";

if (!is_writable(session_save_path())) {
    echo "<p style='color: red; font-weight: bold;'>❌ Session save path is NOT writable!</p>";
} else {
    echo "<p style='color: green; font-weight: bold;'>✅ Session save path is writable.</p>";
}
?>
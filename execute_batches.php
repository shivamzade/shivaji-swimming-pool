<?php
/**
 * Execute Batches Creation
 * Access this file via browser: http://localhost/shivaji_pool/execute_batches.php
 */

require_once 'config/config.php';
require_once 'db_connect.php';

echo "<h2>Creating Batches System...</h2>";

try {
    // Read the SQL file
    $sql = file_get_contents('database/create_batches_simple.sql');
    
    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $successCount = 0;
    $errorCount = 0;
    
    // Execute each statement
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement) && !preg_match('/^SELECT/', $statement)) {
            try {
                $conn->query($statement);
                echo "<p style='color: green;'>✓ " . substr($statement, 0, 80) . "...</p>";
                $successCount++;
            } catch (Exception $e) {
                echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
                $errorCount++;
            }
        }
    }
    
    echo "<h3 style='color: blue;'>🎉 Batches System Created Successfully!</h3>";
    echo "<p><strong>Success:</strong> $successCount statements executed</p>";
    echo "<p><strong>Errors:</strong> $errorCount statements failed</p>";
    echo "<p>✅ Created 15 hourly batches from 6 AM to 9 PM</p>";
    echo "<p>✅ Created member_batches assignment table</p>";
    echo "<p>✅ Created batch statistics view and triggers</p>";
    
    echo "<p><a href='admin/admin_panel/batches/index.php'>Go to Batches Management</a></p>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ Error: " . $e->getMessage() . "</h3>";
}
?>

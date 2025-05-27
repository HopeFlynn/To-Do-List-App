<?php
// Simple test file to check if everything is working
header('Content-Type: application/json');

echo "<h2>Debug Information</h2>";

// Test 1: Check if PHP is working
echo "<p><strong>✓ PHP is working</strong></p>";

// Test 2: Check database connection
$host = 'localhost';
$dbname = 'todo_app';  // Change this to match your database name
$username = 'root';    // Change if different
$password = '';        // Change if you have a password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p><strong>✓ Database connection successful</strong></p>";
    
    // Test 3: Check if todos table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'todos'");
    if ($stmt->rowCount() > 0) {
        echo "<p><strong>✓ 'todos' table exists</strong></p>";
        
        // Test 4: Check table structure
        $stmt = $pdo->query("DESCRIBE todos");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<p><strong>✓ Table structure:</strong></p>";
        echo "<ul>";
        foreach ($columns as $column) {
            echo "<li>{$column['Field']} - {$column['Type']}</li>";
        }
        echo "</ul>";
        
        // Test 5: Try to insert a test record
        try {
            $stmt = $pdo->prepare("INSERT INTO todos (user_id, text, task_date, completed, created_at) VALUES (?, ?, ?, 0, NOW())");
            $stmt->execute([1, 'Test task from debug', date('Y-m-d')]);
            echo "<p><strong>✓ Test insert successful</strong></p>";
            
            // Test 6: Try to read the test record
            $stmt = $pdo->prepare("SELECT * FROM todos WHERE text = 'Test task from debug'");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                echo "<p><strong>✓ Test read successful:</strong></p>";
                echo "<pre>" . print_r($result, true) . "</pre>";
                
                // Clean up test record
                $stmt = $pdo->prepare("DELETE FROM todos WHERE text = 'Test task from debug'");
                $stmt->execute();
                echo "<p><strong>✓ Test cleanup successful</strong></p>";
            }
        } catch (Exception $e) {
            echo "<p><strong>❌ Insert/Read test failed:</strong> " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<p><strong>❌ 'todos' table does not exist</strong></p>";
        echo "<p>Available tables:</p>";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    }
    
} catch(PDOException $e) {
    echo "<p><strong>❌ Database connection failed:</strong> " . $e->getMessage() . "</p>";
}

// Test 7: Check if we can receive POST data
if ($_POST) {
    echo "<p><strong>✓ POST data received:</strong></p>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
} else {
    echo "<p><strong>ℹ️ No POST data (this is normal for GET requests)</strong></p>";
}

// Test 8: Check current directory and files
echo "<p><strong>Current directory:</strong> " . getcwd() . "</p>";
echo "<p><strong>Files in directory:</strong></p>";
$files = scandir('.');
echo "<ul>";
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo "<li>$file</li>";
    }
}
echo "</ul>";
?>


<?php
// Database connection details
$host = "localhost";
$username = "user27032025";
$password = "password";
$dbname = "php27032025";

try {
    // Connect to the database using PDO
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    echo "Database connection successful!<br>";
    
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
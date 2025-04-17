

<?php
$host = "localhost";
$username = "user27032025";
$password = "password";
$dbname = "php27032025";
 
try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT customer_id, last_name , points FROM customers limit 10";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Set the resulting array to associative
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    foreach ($stmt->fetchAll() as $row) {
        echo "id: " . $row["customer_id"] . " - Name: " . $row["last_name"] . " - Points " . $row["points"] . "<br>";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$conn = null; // Close the PDO connection
?>


?>


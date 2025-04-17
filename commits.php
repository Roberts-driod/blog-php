<?php
// Include class definitions
require_once 'classes.php';

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
    
    // Query to get posts and comments with LEFT JOIN
    $query = "
        SELECT 
            p.post_id, p.title, p.content, p.created_at AS post_created_at,
            c.comment_id, c.post_id AS comment_post_id, c.author, c.comment, c.created_at AS comment_created_at
        FROM 
            posts p
        LEFT JOIN 
            comments c ON p.post_id = c.post_id
        ORDER BY 
            p.created_at DESC, 
            c.created_at ASC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    // Fetch all records as a flat array
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convert flat array to hierarchical structure using Post and Comment classes
    $posts = [];
    $postsMap = []; // Map post_id to array index for quick lookup
    
    foreach ($results as $row) {
        $postId = $row['post_id'];
        
        // Create a new Post object if we haven't seen this post before
        if (!isset($postsMap[$postId])) {
            $post = new Post(
                $postId,
                $row['title'],
                $row['content'],
                $row['post_created_at']
            );
            
            $posts[] = $post;
            $postsMap[$postId] = count($posts) - 1;
        }
        
        // Add comment to the post if comment exists
        if (!empty($row['comment_id'])) {
            $comment = new Comment(
                $row['comment_id'],
                $row['comment_post_id'],
                $row['author'],
                $row['comment'],
                $row['comment_created_at']
            );
            
            $posts[$postsMap[$postId]]->addComment($comment);
        }
    }
    
    // Now $posts contains all posts with their comments as objects
    
} catch (PDOException $e) {
    die("Database operation failed: " . $e->getMessage());
}
?>
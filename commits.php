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
    
    // Check if tables exist, if not create them
    // Create posts table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS posts (
            post_id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "Posts table created successfully!<br>";
    
    // Create comments table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS comments (
            comment_id INT AUTO_INCREMENT PRIMARY KEY,
            post_id INT NOT NULL,
            author VARCHAR(100) NOT NULL,
            comment TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE
        )
    ");
    echo "Comments table created successfully!<br>";
    
    // Check if posts table is empty, if yes insert sample data
    $stmt = $pdo->query("SELECT COUNT(*) FROM posts");
    $postCount = $stmt->fetchColumn();
    
    if ($postCount == 0) {
        // Insert sample posts
        $posts = [
            ['How to Learn PHP', 'PHP is a server-side scripting language designed for web development. Here are some tips to learn it effectively...'],
            ['Web Development in 2025', 'The landscape of web development has changed significantly in the last few years. Let\'s explore current trends...'],
            ['Database Design Best Practices', 'Good database design is crucial for application performance. This post covers normalization, indexing, and more...']
        ];
        
        $postInsert = $pdo->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
        
        foreach ($posts as $post) {
            $postInsert->execute([$post[0], $post[1]]);
            echo "Post '{$post[0]}' inserted!<br>";
        }
        
        // Insert sample comments
        $comments = [
            [1, 'John', 'Great article! I learned a lot.'],
            [1, 'Maria', 'Would love to see more detailed examples.'],
            [2, 'Alex', 'I disagree with point #3, here\'s why...'],
            [2, 'Sarah', 'Excellent overview of the current state of web dev!'],
            [3, 'David', 'This helped me understand normalization better.'],
            [3, 'Lisa', 'Can you talk more about indexing strategies?'],
            [3, 'Mike', 'I implemented these practices and saw immediate performance gains.']
        ];
        
        $commentInsert = $pdo->prepare("INSERT INTO comments (post_id, author, comment) VALUES (?, ?, ?)");
        
        foreach ($comments as $comment) {
            $commentInsert->execute([$comment[0], $comment[1], $comment[2]]);
            echo "Comment by '{$comment[1]}' inserted!<br>";
        }
    } else {
        echo "Sample data already exists in the database.<br>";
    }
    
    echo "Database setup complete!";
    
} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage());
}
?>
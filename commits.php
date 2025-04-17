<?php
// Define Post class
class Post {
    public $id;
    public $title;
    public $content;
    public $created_at;
    public $comments = [];
    
    public function __construct($id, $title, $content, $created_at) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->created_at = $created_at;
    }
    
    public function addComment(Comment $comment) {
        $this->comments[] = $comment;
    }
    
    public function hasComments() {
        return !empty($this->comments);
    }
}

// Define Comment class
class Comment {
    public $id;
    public $post_id;
    public $author;
    public $content;
    public $created_at;
    
    public function __construct($id, $post_id, $author, $content, $created_at) {
        $this->id = $id;
        $this->post_id = $post_id;
        $this->author = $author;
        $this->content = $content;
        $this->created_at = $created_at;
    }
}

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
        }
    }
    
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
    
} catch (PDOException $e) {
    die("Database operation failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts and Comments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .post {
            border: 1px solid #ccc;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
        }
        .post-title {
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        .post-content {
            margin-bottom: 15px;
        }
        .post-meta {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 15px;
        }
        .comments-list {
            list-style-type: none;
            padding-left: 15px;
        }
        .comment {
            border-left: 3px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }
        .comment-content {
            margin-bottom: 5px;
        }
        .comment-meta {
            color: #666;
            font-size: 0.8em;
        }
    </style>
</head>
<body>
    <h1>Posts and Comments</h1>
    
    <?php if (empty($posts)): ?>
        <p>No posts found.</p>
    <?php else: ?>
        <ul class="posts-list">
            <?php foreach ($posts as $post): ?>
                <li class="post">
                    <div class="post-title"><?php echo htmlspecialchars($post->title); ?></div>
                    <div class="post-content"><?php echo htmlspecialchars($post->content); ?></div>
                    <div class="post-meta">Posted on: <?php echo htmlspecialchars($post->created_at); ?></div>
                    
                    <?php if ($post->hasComments()): ?>
                        <h3>Comments:</h3>
                        <ul class="comments-list">
                            <?php foreach ($post->comments as $comment): ?>
                                <li class="comment">
                                    <div class="comment-content"><?php echo htmlspecialchars($comment->content); ?></div>
                                    <div class="comment-meta">
                                        <?php echo htmlspecialchars($comment->author); ?> - 
                                        <?php echo htmlspecialchars($comment->created_at); ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No comments yet.</p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
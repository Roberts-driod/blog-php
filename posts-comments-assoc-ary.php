<?php
// Database connection details
$host = "localhost";
$username = "user27032025";
$password = "password";
$dbname = "php27032025";

try {
    // Step 1: Connect to the database using PDO
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Step 2: Query to get posts and comments with LEFT JOIN
    // Updated to match your actual table structure
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
    
    // Step 3: Fetch all records as a flat array
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Step 4: Convert flat array to hierarchical structure
    $postsWithComments = [];
    $processedPosts = [];
    
    foreach ($results as $row) {
        $postId = $row['post_id'];
        
        // Add post to the array if not already added
        if (!isset($processedPosts[$postId])) {
            $processedPosts[$postId] = true;
            
            $postsWithComments[] = [
                'post' => [
                    'id' => $postId,
                    'title' => $row['title'],
                    'content' => $row['content'],
                    'created_at' => $row['post_created_at']
                ],
                'comments' => []
            ];
            
            // Get the index of the current post
            $currentPostIndex = count($postsWithComments) - 1;
        } else {
            // Find the index of the post in the array
            $currentPostIndex = null;
            foreach ($postsWithComments as $index => $postData) {
                if ($postData['post']['id'] == $postId) {
                    $currentPostIndex = $index;
                    break;
                }
            }
        }
        
        // Add comment to the post if comment exists
        if (!empty($row['comment_id'])) {
            $postsWithComments[$currentPostIndex]['comments'][] = [
                'id' => $row['comment_id'],
                'content' => $row['comment'], // Changed from comment_content to comment
                'author' => $row['author'],
                'created_at' => $row['comment_created_at']
            ];
        }
    }
    
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
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
    
    <?php if (empty($postsWithComments)): ?>
        <p>No posts found.</p>
    <?php else: ?>
        <ul class="posts-list">
            <?php foreach ($postsWithComments as $postData): ?>
                <li class="post">
                    <div class="post-title"><?php echo htmlspecialchars($postData['post']['title']); ?></div>
                    <div class="post-content"><?php echo htmlspecialchars($postData['post']['content']); ?></div>
                    <div class="post-meta">Posted on: <?php echo htmlspecialchars($postData['post']['created_at']); ?></div>
                    
                    <?php if (!empty($postData['comments'])): ?>
                        <h3>Comments:</h3>
                        <ul class="comments-list">
                            <?php foreach ($postData['comments'] as $comment): ?>
                                <li class="comment">
                                    <div class="comment-content"><?php echo htmlspecialchars($comment['content']); ?></div>
                                    <div class="comment-meta">
                                        <?php echo htmlspecialchars($comment['author']); ?> - 
                                        <?php echo htmlspecialchars($comment['created_at']); ?>
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
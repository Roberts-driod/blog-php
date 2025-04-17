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
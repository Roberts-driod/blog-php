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
?>
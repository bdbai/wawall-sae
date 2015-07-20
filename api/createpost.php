<?php
require('./post.php');
$wall = $_POST['wall'];
$title = $_POST['title'];
$content = $_POST['content'];
createPost($wall, $title, $content);
?>
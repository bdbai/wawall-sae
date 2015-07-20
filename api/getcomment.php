<?php
require('get.php');
$post = $_GET['post'];
echo json_encode(array('comment' => getComment($post)));
?>
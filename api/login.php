<?php
require('./post.php');
$email = $_POST['email'];
$pass = $_POST['pass'];
login($email, $pass);
?>
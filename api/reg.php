<?php
require('./post.php');
$email = $_POST['email'];
$pass = $_POST['pass'];
$name = $_POST['name'];
reg($email, $pass, $name);
?>
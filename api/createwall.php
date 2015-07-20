<?php
require('./post.php');
$name = $_POST['name'];
$desc = $_POST['desc'];
$walltype = $_POST['walltype'];
createWall($name, $desc, $walltype);
?>
<?php
require('./post.php');
$id = $loginUser;
if (!$id)
{
	returnError('гКох╣гб╪');
	return;
}
signIn($id);
?>
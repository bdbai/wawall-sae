<?php
require('./post.php');
$id = $loginUser;
if (!$id)
{
	returnError('гКох╣гб╪');
	return;
}
$wall = $_POST['wall_id'];
unlike($wall, $id);
?>
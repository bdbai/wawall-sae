<?php
require('./post.php');
$id = $loginUser;
if (!$id)
{
	returnError('гКох╣гб╪');
	return;
}
$wall = $_POST['wall_id'];
like($wall, $id);
?>
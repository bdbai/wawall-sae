<?php
require('./post.php');
$id = $loginUser;
if (!$id)
{
	returnError('���ȵ�¼');
	return;
}
$wall = $_POST['wall_id'];
like($wall, $id);
?>
<?php
require('./post.php');
$id = $loginUser;
if (!$id)
{
	returnError('���ȵ�¼');
	return;
}
signIn($id);
?>
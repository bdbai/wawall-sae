<?php
require('./get.php');
$walltypes = WaWall::GetWalltypes();
$ret = array();
while (list($key, $value) = each($walltypes))
{
	array_push($ret, array('walltype_id' => $key, 'walltype_name' => $value));
}
echo json_encode($ret);
?>
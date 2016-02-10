<?php
require('./get.php');
$id = $loginUser;
if (!$id)
{
	returnError('请先登录');
	return;
}

$ret = array();
$walls = WaWall::FindWallIdsByUser($id);
if ($walls)
{
while (list($key, $value) = each($walls))
{
	$wall = array();
	$wall['wall_id'] = $value['wall_id'];
	if (isset($_GET['fulldata']))
	{
		$info = WaWall::GetWallInfo($wall['wall_id']);
	$wall['wall_name'] = $info['wall_name'];
	$wall['wall_desc'] = $info['wall_desc'];
	$userInfo = WaUser::GetUserInfo($info['wall_creator']);
	if ($userInfo)
	{
		$wall['wall_creatorname'] = $userInfo['user_name'];
	}
	$wall['wall_type'] = $info['wall_type'];
	$wall['wall_usercount'] = $info['wall_usercount'];
	}
	$wall['relationship'] = 'like';
	array_push($ret, $wall);
}
}
$walls = WaWall::FindWallIdsByCreator($id);
if ($walls)
{
while (list($key, $value) = each($walls))
{
	$wall = array();
	$wall['wall_id'] = $value['wall_id'];
	if (isset($_GET['fulldata']))
	{
		$info = WaWall::GetWallInfo($wall['wall_id']);
	$wall['wall_name'] = $info['wall_name'];
	$wall['wall_desc'] = $info['wall_desc'];
	$userInfo = WaUser::GetUserInfo($info['wall_creator']);
	if ($userInfo)
	{
		$wall['wall_creatorname'] = $userInfo['user_name'];
	}
	$wall['wall_type'] = $info['wall_type'];
	$wall['wall_usercount'] = $info['wall_usercount'];
	}
	$wall['relationship'] = 'own';
	array_push($ret, $wall);
}
}
echo json_encode($ret);
?>

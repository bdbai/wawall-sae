<?php
require('./get.php');
$walltype = $_GET['walltype'];
$limit = $_GET['limit'];
$limit = intval($limit, 20);
$limit = min(array($limit, 500));
$walls = false;
if ($walltype)
{
	$walls = WaWall::FindWallIdsByType($walltype, $limit);
}
else
{
	$walls = WaWall::FindWallIdsByAccess(5, $limit);
}

if (!$walls)
{
	returnError("这里暂时没有墙。");
	return;
}

$ret = array();
while (list($key, $value) = each($walls))
{
	if (intval($key) > $limit)
	{
		break;
	}
	$info = WaWall::GetWallInfo($value['wall_id']);
	if (!$info) continue;
	$wall = array();
	$wall['wall_id'] = $info['wall_id'];
	$wall['wall_name'] = $info['wall_name'];
	$wall['wall_desc'] = $info['wall_desc'];
	$userInfo = WaUser::GetUserInfo($info['wall_creator']);
	if ($userInfo)
	{
		$wall['wall_creatorname'] = $userInfo['user_name'];
	}
	$wall['wall_type'] = $info['wall_type'];
	$wall['wall_usercount'] = $info['wall_usercount'];
	array_push($ret, $wall);
}
if ($ret)
{
	echo json_encode($ret);
}
else
{
	returnError('暂无。');
}
?>
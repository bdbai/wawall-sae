<?php
//require('./init.php');
require('./getwalltypes.php');
global $counter;
initCounter();
var_dump('resetcounter: ' . $counter -> set('cachehit', 0));
echo 'cachehit: ' . $counter -> get('cachehit') . "\r\n";
var_dump('resetcounter: ' . $counter -> set('dbhit', 0));
echo 'dbhit: ' . $counter -> get('dbhit') . "\r\n";	
if (!$counter -> exists('usercount'))
	{
		var_dump($counter -> create('usercount'));
	}
//var_dump('resetcounter: ' . $counter -> set('usercount', 0));
echo $counter -> get('usercount') . "\r\n";
/*
 * Msg debugging
echo "create\r\n";
$id1 = WaMsg::CreateMsg(1, 2, 'aa', false);
$id2 = WaMsg::CreateMsg(3, 2, 'aab', false);
echo "findall and get \r\n";
var_dump(WaMsg::FindMsgIdsByUser(2, 1));
var_dump(WaMsg::GetMsgInfo($id1));
echo "getunread setread and getunread";
var_dump(WaMsg::FindMsgIdsByUserUnread(2));
var_dump(WaMsg::SetMsgRead($id2));
var_dump(WaMsg::FindMsgIdsByUserUnread(2));
echo "delete\r\n";
var_dump(WaMsg::DeleteMsg($id2, 2));
var_dump(WaMsg::DeleteMsg($id2, 3));
var_dump(WaMsg::FindMsgIdsByUserUnread(2));
var_dump(WaMsg::FindMsgIdsByUser(2, 3));

/* WaDialog::FindDialogIdsByUser(1); 
WaDialog::DeleteDialog(1, 2);
WaDialog::FindDialogIdsByUser(1);
*/
/*
 * Wall Debugging
 WaWall::CreateWall(1, 'wnf', 'desb');
$id = WaWall::CreateWall(1, 'wn', 'des');
$info = WaWall::GetWallInfo($id);
//var_dump($info);
$info['wall_joinkey'] = 'fff';
WaWall::SetWallInfo($id, $info);
echo "\r\n\r\n";
var_dump(WaWall::FindWallIdsByCreator(1));
var_dump(WaWall::FindWallIdsByAccess(5, 3));
var_dump(WaWall::FindWallIdsByType(1, 3));
echo "User...\r\n\r\n";
var_dump(WaWall::AddUserToWall(1, $id));
var_dump(WaWall::FindWallIdsByUser(1));
var_dump(WaWall::FindUserIdsByWall($id));
echo "delete\r\n";
var_dump(WaWall::DeleteWall($id));
$info = WaWall::GetWallInfo($id);
var_dump($info);
var_dump(WaWall::FindWallIdsByCreator(1));
var_dump(WaWall::FindWallIdsByAccess(5, 3));
var_dump(WaWall::FindWallIdsByType(1, 3));
*/
//var_dump(WaApp::GetAppRunning());

var_dump('cachehit: ' . $counter -> get('cachehit') . "\r\n");
var_dump('dbhit: ' . $counter -> get('dbhit') . "\r\n");
?>
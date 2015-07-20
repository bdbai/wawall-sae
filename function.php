<?php
ob_start();
require(dirname(__FILE__ ). '/init.php');
function countWalls()
{
	global $counter;
	initCounter();
	if (!$counter -> exists('wallcount'))
	{
		$counter -> create('wallcount');
	}
	$count = $counter -> get('wallcount');
	if ($count)
	{
		return $count;
	}
	return 0;
}
function countUsers()
{
	global $counter;
	initCounter();
	$count = $counter -> get('usercount');
	if (!$counter -> exists('usercount'))
	{
		$counter -> create('usercount');
	}
	if ($count)
	{
		return $count;
	}
	return 0;
}
function returnError($err = '服务器内部错误。')
{
	//ob_clean();
	$errArr = array('error' => $err);
	echo json_encode($errArr);
	die();
}
function returnSucc()
{
	$succ = array('state' => 'success');
	echo json_encode($succ);
}
/* Check Autologin
 */
 $loginUser = null;
 $loginPass = $_COOKIE['loginpass'];
 if ($loginPass)
 {
	 $kvKey = 'loginpass_' . $loginPass;
	 $result = getKv($kvKey);
	 if ($result)
	 {
		 //deleteKv($kvKey);
		 $loginUser = $result[0];
		 /*$kvKey = 'userlogin_' . $loginUser;
		 $userLogins = getKv($kvKey);
		 if ($userLogins)
		 {
			 while (list($key, $value) = each($userLogins))
			 {
				 if ($value[0] = $loginPass)
				 {
					 $timeStamp = microtime(true);
					 $loginPass = sha1($timeStamp);
					 $userLogins[$key] = array($loginPass, $timeStamp);
					 $loginInfo = array($currUser, $timeStamp);
					 $kvKey = 'loginpass_' . $loginPass;
					 setKv($kvKey, $loginInfo);
					 break;
				 }
			 }
			 $kvKey = 'userlogin_' . $loginUser;
			 setKv($kvKey, $userLogins);
			 setcookie('loginpass', $loginPass, strtotime('+1 year'), '/');
		 }*/
	 }
	 else
	 {
		 setcookie('loginpass', 'deleted', time() - 3600, '/', 'wawall.sinaapp.com');
	 }
 }
function login($email, $pass)
{
	try
	{
		$id = WaUser::FindUserIdByEmail($email);
		$info = WaUser::GetUserInfo($id);
	} 
	catch (Exception $e)
	{
		returnError($e -> getMessage());
		return false;
	}
	if (!$id || !$info)
	{
		returnError('您还未注册。');
		return false;
	}
	
	$processedPass = sha1($pass);
	if ($info['user_pass'] != $processedPass)
	{
		returnError('密码好像不正确。');
		return false;
	}
	$timeStamp = microtime(true);
	$loginInfo = array($id, $timeStamp);
	$loginPass = sha1($timeStamp);
	
	$kvKey = 'loginpass_' . $loginPass;
	$result = setKv($kvKey, $loginInfo);
	$kvKey = 'userlogin_' . $id;
	$userLogins = getKv($kvKey);
	if ($userLogins === true || $userLogins === false)
	{
		$userLogins = array();
	}
	array_unshift($userLogins, array($loginPass, $timeStamp));
	$result = setKv($kvKey, $userLogins);
	setcookie('loginpass', $loginPass, strtotime('+1 year'), '/', 'wawall.sinaapp.com');
	returnSucc();
}
function reg($email, $pass, $name)
{
	try
	{
		$id = WaUser::FindUserIdByEmail($email);
	}
	catch (Exception $e)
	{
		returnError($e -> getMessage());
	}
	if ($id)
	{
		returnError('这个邮箱已经注册过了。');
		return;
	}
	
	try
	{
		$id = WaUser::CreateUser($email, sha1($pass), $name, 5);
	}
	catch (Exception $e)
	{
		returnError($e -> getMessage());
		return;
	}
	
	if (!$id)
	{
		returnError('注册失败。');
		return;
	}
	global $counter;
	initCounter();
	$counter -> incr('usercount');
	login($email, $pass);
}
function mini($email)
{
	try
	{
		$user = WaUser::FindUserIdByEmail($email);
	}
	catch (Exception $e)
	{
		returnError($e -> getMessage());
	}
	
	if ($user)
	{
		$ret = array('next' => 'login');
		echo json_encode($ret);
	}
	else
	{
		$ret = array('next' => 'reg');
		echo json_encode($ret);
	}
}
function logout()
{
	$loginPass = $_COOKIE['loginpass'];
	setcookie('loginpass', 'deleted', time() - 3600, '/', 'wawall.sinaapp.com');
	if ($loginPass)
	{
		$kvKey = 'loginpass_' . $loginPass;
		$loginInfo = getKv($kvKey);
		if ($loginInfo)
		{
			$id = $loginInfo[0];
			$kvKey = 'userlogin_' . $id;
			$userLogins = getKv($kvKey);
			while (list($key, $value) = each($userLogins))
			{
				if ($value[0] == $loginPass)
				{
					unset($userLogins[$key]);
					break;
				}
			}
			setKv($kvKey, $userLogins);
			$kvKey = 'loginpass_' . $loginPass;
			deleteKv($kvKey);
		}
	}
}
function signedIn($id)
{
	try
	{
		$userData = WaUser::GetUserData($id);
	}
	catch (Exception $e)
	{
		return true;
	}
	
	if (!$userData)
	{
		returnError();
		return;
	}
	$lastSign = $userData['user_lastsign'];
	//$lastSignTime  = strtotime($lastSign);
	$yesterday = date('Y-m-d');
	
	if ($lastSign == $yesterday)
	{
		return true;
	}
	return false;
}
function signin($id)
{
	if (signedIn($id))
	{
		returnError('您已签到。');
		return;
	}
	try
	{
		$userData = WaUser::GetUserData($id);
	}
	catch (Exception $e)
	{
		returnError($e -> getMessage());
	}
	
	if (!$userData)
	{
		returnError();
		return;
	}
	$lastSign = $userData['user_lastsign'];
	$lastSignTime  = strtotime($lastSign);
	$yesterdayDate = getdate(strtotime('-1 day'));
	$lastSignDate = getdate($lastSignTime);
	
	if ($yesterdayDate['year'] == $lastSignDate['year'] 
	&& $yesterdayDate['yday'] == $lastSignDate['yday'])
	{
		$userData['user_signdays'] ++;
	}
	else
	{
		$userData['user_signdays'] = 1;
	}
	$userData['user_lastsign'] = date('Y-m-d');
	$userData['user_wealth'] += 14;
	$userData['user_exp'] += 5;
	$result = WaUser::SetUserData($id, $userData);
	if ($result)
	{
		$retArr = array('state' => 'success', 'signdays' => $userData['user_signdays'], 'wealth' => $userData['user_wealth'], 'exp' => $userData['user_exp']);
		echo json_encode($retArr);
	}
	else
	{
	throw new Exception($userData['user_exp']);
		returnError();
	}
}
function getSignDays($id)
{
	try
	{
		$userData = WaUser::GetUserData($id);
	}
	catch (Exception $e)
	{
		returnError($e -> getMessage());
	}
	if (!$userData) 
	{
		returnError();
		return;
	}
	return $userData['user_signdays'];
}
function createWall($name, $desc, $walltype)
{
	global $loginUser;
	if (!$loginUser)
	{
		returnError('请先登录。');
		return;
	}
	try
	{
		$userData = WaUser::GetUserData($loginUser);
		if (!$userData) throw new Exception('获取用户失败。');
	}
		catch (Exception $e)
		{
					returnError($e -> getMessage());
		return;
		}
		$wealth = $userData['user_wealth'];
		if ($wealth < 30)
		{
			returnError('墙砖不够。');
			return;
		}
	try
	{
		$id = WaWall::CreateWall($loginUser, $name, $desc, $walltype, 5);
	}
	catch (Exception $e)
	{
		returnError($e -> getMessage());
		return;
	}
		$userData['user_wealth'] -= 30;
		$userData['user_exp'] += 20;
			try
	{
		$ret = WaUser::SetUserData($loginUser, $userData);
		if (!$ret) throw new Exception('扣墙砖失败。');
	}
		catch (Exception $e)
		{
					returnError($e -> getMessage());
		return;
		}
	
	global $counter;
	initCounter();
	$counter -> incr('wallcount');
	$ret = array('state' => 'success', 'wall_id' => $id);
	echo json_encode($ret);
}
function unlike($wall, $user)
{
	try
	{
		$result = WaWall::RemoveUserFromWall($user, $wall);
	}
	catch (Exception $e)
	{
		returnError($e -> getMessage());
		return;
	}
	
	if (!$result) returnError();
	$info = WaWall::GetWallInfo($wall);
	if (!$info) returnError();
	$info['wall_usercount'] --;
	$result = WaWall::SetWallInfo($wall, $info);
	if (!$result) returnError();
	$ret = array('wall_usercount' => $info['wall_usercount']);
	echo json_encode($ret);
}
function like($wall, $user)
{
	$found = false;
	try
	{
		$wallUsers = WaWall::FindUserIdsByWall($wall);
	}
	catch (Exception $e)
	{
		
	}
	if ($wallUsers)
	{
		foreach ($wallUsers as $wallUser)
		{
			if ($wallUser['user_id'] == $user)
			{
				$found = true;
				break;
			}
		}
	}
	$info = WaWall::GetWallInfo($wall);
	if (!$info) returnError('获取墙信息失败');
	if (!$found)
	{
		try
		{
					$result = WaWall::AddUserToWall($user, $wall);
		}
		catch (Exception $e)
		{
			returnError($e -> getMessage());
			return;
		}
	
	if (!$result) returnError('添加失败');
	$info['wall_usercount'] ++;
	$result = WaWall::SetWallInfo($wall, $info);
	if (!$result) returnError('加人失败');
	}
	$ret = array('wall_usercount' => $info['wall_usercount']);
	echo json_encode($ret);
}
function createPost($wall, $title, $content)
{
	global $loginUser;
	if (!$loginUser)
	{
		returnError('请先登录');
		return;
	}
	
	try
	{
		$userData = WaUser::GetUserData($loginUser);
		if (!$userData) throw new Exception('获取用户失败。');
	}
		catch (Exception $e)
		{
					returnError($e -> getMessage());
		return;
		}
		$wealth = $userData['user_wealth'];
		if ($wealth < 1)
		{
			returnError('墙砖不够。');
			return;
		}
		$userData['user_wealth'] -= 1;
		$userData['user_exp'] += 1;
	try
	{
		$ret = WaPost::CreatePost($wall, $title, $content, $loginUser);
		if (!$ret) throw new Exception('创建帖子失败。');
		$ret = WaUser::SetUserData($loginUser, $userData);
		if (!$ret) throw new Exception('扣墙砖失败。');
	}
	catch (Exception $e)
	{
		returnError($e -> getMessage());
		return;
	}
	
	$ret = array('post_id', $ret);
	echo json_encode($ret);
}
function getComment($post) {
	$comments = array();
	try {
		$comments = WaComment::FindCommentByPost($post);
	} catch (Exception $e) {
		returnError($e -> getMessage());
	}
	
	$ret = array();
	while (list($key, $value) = each($comments)) {
		try {
			$commentInfo = WaComment::GetCommentInfo($value['comment_id']);
		} catch (Exception $e) {
			returnError($e -> getMessage());
		}
		array_push($ret, $commentInfo);
	}
	return $ret;
}
?>
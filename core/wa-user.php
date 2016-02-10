<?php
class WaUser
{
	public static function GetUserIds($limit)
	{
		try
		{
			$safeLimit = ensureInt($limit);
		}
		catch (Exception $e)
		{
			throw new Exception('获取用户时出错。');
			return null;
		}
		
		$cache = getCache('users');
		if ($cache == null || count($cache) < $limit)
		{
			$ret = getData('select `user_id` from wa_user order by user_id desc limit ' . $safeLimit);
			setCache('users', $ret);
		}
		else
		{
			$ret = $cache;
		}
		return $ret;
	}
	public static function FindUserIdByEmail($email)
	{
		try
		{
			$safeEmail = ensureString($email, 32);
		}
		catch (Exception $e)
		{
			throw new Exception("通过邮箱查找用户：$email 时出错。");
			return null;
		}
		
		$ret = getData("select `user_id` from wa_user where user_email = '$safeEmail'");
		if ($ret)
		{
			return $ret[0]['user_id'];
		}
		return null;
	}
	public static function FindUserIdByName($name)
	{
		try
		{
			$safeName = ensureString($name, 32);
		}
		catch (Exception $e)
		{
			throw new Exception("通过昵称查找用户：$name 时出错。");
			return null;
		}
		
		$ret = getData("select `user_id` from wa_user where user_name = '$safeName'");
		return $ret;
	}
	public static function CreateUser($email, $pass, $name, $access)
	{
		try
		{
			$safeEmail = ensureString($email, 32);
			$safePass = ensureString($pass, 40);
			$safeName = ensureString($name, 20);
			$safeAccess = ensureInt($access, 2);
		}
		catch (Exception $e)
		{
			throw new Exception('创建用户时出错：' . $e -> getMessage());
			return false;
		}
		
		$sqlInfo = "insert into wa_user (`user_email`, `user_pass`, `user_name`, `user_access`) values ('$safeEmail', '$safePass', '$safeName', $safeAccess);";
		if (!runSql($sqlInfo))
		{
				throw new Exception("创建用户时出错：$name。");
				return null;
		}
		$lastId = lastId();
		if ($lastId < 1)
		{
				throw new Exception("创建用户时出错：$name。");
				return null;
		}
		$sqlData = "insert into wa_userdata (`user_id`, `user_exp`, `user_wealth`, `user_lastsign`) values ($lastId, 50, 6, '" . date('Y-m-d H:i:s', strtotime('-2 days')). "');";
		$sqlProfile ="insert into wa_userprofile (`user_id`) values ($lastId);";
		if (runSql($sqlData) & runSql($sqlProfile))
		{
			$userCache = getCache('users');
			if ($userCache != null)
			{
				array_unshift($userCache, array('user_id' => $lastId));
				setCache('users', $userCache);
				return $lastId;
			}
		}
		else
		{
			throw new Exception("创建用户时出错：$name。");
			return null;
		}
		return $lastId;
	}
	
	public static function GetUserInfo($id)
	{
		try
		{
			$safeId = ensureInt($id, 10);
		}
		catch (Exception $e)
		{
			throw new Exception("查询用户信息时出错：$id");
			return null;
		}
		$cacheKey = 'userinfo_' . $safeId;
		$cache = getCache($cacheKey);
		if ($cache == null)
		{
			$ret = getData('select `user_email`, `user_pass`, `user_name`, `user_passkey`, `user_access`, `user_jointime` from wa_user where user_id = ' . $safeId . ' limit 1');
			if ($ret == null)
			{
				throw new Exception("查询用户信息时出错：$id");
				return null;
			}
			else
			{
				$ret = $ret[0];
				setCache($cacheKey, $ret);
				return $ret;
			}
		} 
		else
			{
			return $cache;
		}
	}
	public static function GetUserData($id)
	{
		try
		{
			$safeId = ensureInt($id, 10);
		}
		catch (Exception $e)
		{
			throw new Exception("查询用户数据时出错：$id");
			return null;
		}
		$cacheKey = 'userdata_' . $safeId;
		$cache = getCache($cacheKey);
		if ($cache == null)
		{
			$ret = getData('select `user_exp`, `user_wealth`, `user_lastsign`, `user_signdays` from wa_userdata where user_id = ' . $safeId . ' limit 1');
			if ($ret == null)
			{
				throw new Exception("查询用户数据时出错：$id");
				return null;
			}
			else
			{
				$ret = $ret[0];
				setCache($cacheKey, $ret);
				return $ret;
			}
		}
		else 
		{
			return $cache;
		}
	}
	public static function GetUserProfile($id)
	{
		try
		{
			$safeId = ensureInt($id, 10);
		}
		catch (Exception $e)
		{
			throw new Exception("查询用户资料时出错：$id");
			return null;
		}
		$cacheKey = 'userprofile_' . $safeId;
		$cache = getCache($cacheKey);
		if ($cache == null)
		{
			$ret = getData('select `user_gender`, `user_birth`, `user_desc`, `user_address` from wa_userprofile where user_id = ' . $safeId . ' limit 1');
			if ($ret == null)
			{
				throw new Exception("查询用户资料时出错：$id");
				return null;
			}
			else
			{
				$ret = $ret[0];
				setCache($cacheKey, $ret);
				return $ret;
			}
		}
		else 
		{
			return $cache;
		}
	}
	
	public static function SetUserInfo($id, $info)
	{
		$safeInfo = array();
		try
		{
			$safeId = ensureInt($id, 10);
			$safeInfo['user_email'] = '\'' . ensureString($info['user_email'], 32) .'\'';
			$safeInfo['user_pass'] = '\'' . ensureString($info['user_pass'], 40) .'\'';
			$safeInfo['user_name'] = '\'' . ensureString($info['user_name'], 20) .'\'';
			$safeKey = $info['user_safekey'];
			if (!$safeKey == null)
			{
				$safeInfo['user_safekey'] = '\'' . ensureString($info['user_safekey'], 40) .'\'';
			}
			$safeInfo['user_access'] = ensureInt($info['user_access'], 2);
			//$safeInfo['user_jointime'] = ensureDate($info['user_jointime']);
		}
		catch (Exception $e)
		{
			throw new Exception('设置用户信息出错：' . $e -> getMessage() . implode(';', $info));
			return false;
		}
		
		$sql = 'update wa_user set';
		while (list($key, $value) = each($safeInfo))
		{
			$sql .= " `$key` = $value,";
		}
		$sql = trim($sql, ',');
		$sql .= ' where user_id = ' . $safeId . ' limit 1;';
		$ret = runSql($sql);
		if ($ret)
		{
			$cacheKey = 'userinfo_' . $safeId;
			setCache($cacheKey, null);
		}
		return $ret;
	}
	public static function SetUserData($id, $data)
	{
		$safeData = array();
		try
		{
			$safeId = ensureInt($id, 10);
			$safeData['user_exp'] = ensureInt($data['user_exp'], 10);
			$safeData['user_wealth'] = ensureInt($data['user_wealth'], 10);
			$safeData['user_lastsign'] = '\'' . ensureDate($data['user_lastsign']) . '\'';
			$safeData['user_signdays'] = ensureInt($data['user_signdays'], 5);
		}
		catch (Exception $e)
		{
			throw new Exception('设置用户数据出错：' . $e -> getMessage() . implode(';', $data));
			return false;
		}
		
		$sql = 'update wa_userdata set';
		while (list($key, $value) = each($safeData))
		{
			$sql .= " `$key` = $value,";
		}
		$sql = trim($sql, ',');
		$sql .= ' where user_id = ' . $safeId . ';';
		$ret = runSql($sql);
		if ($ret)
		{
			$cacheKey = 'userdata_' . $safeId;
			setCache($cacheKey, null);
		}
		return $ret;
	}
	public static function SetUserProfile($id, $profile)
	{
		$safeProfile = array();
		try
		{
			$safeId = ensureInt($id, 10);
			$safeProfile['user_gender'] = ensureInt($profile['user_gender'], 1);
			$safeProfile['user_birth'] = '\'' . ensureDate($profile['user_birth'], true) . '\'';
			$safeProfile['user_desc'] = '\'' . ensureString($profile['user_desc'], 100, false, true) .'\'';
			$safeProfile['user_address'] = '\'' . ensureString($profile['user_address'], 40, false, true) .'\'';
		}
		catch (Exception $e)
		{
			throw new Exception('设置用户资料出错：' . $e -> getMessage() . implode(';', $profile));
			return false;
		}
		
		$sql = 'update wa_userprofile set';
		while (list($key, $value) = each($safeProfile))
		{
			$sql .= " `$key` = $value,";
		}
		$sql = trim($sql, ',');
		$sql .= ' where user_id = ' . $safeId . ';';
		$ret = runSql($sql);
		if ($ret)
		{
			$cacheKey = 'userprofile_' . $safeId;
			setCache($cacheKey, null);
		}
		return $ret;
	}
	
	public static function DeleteUser($id)
	{
		try
		{
			$safeId = ensureInt($id, 10);
		}
		catch (Exception $e)
		{
			throw new Exception('删除用户时出错。');
			return null;
		}
		
		$cacheInfoKey = 'userinfo_' . $safeId;
		$cacheDataKey = 'userdata_' . $safeId;
		$cacheProfileKey = 'userprofile_' . $safeId;
		setCache($cacheInfoKey, null);
		setCache($cacheDataKey, null);
		setCache($cacheProfileKey, null);
		$sqlInfo = "delete from wa_user where user_id= $safeId limit 1;";
		$sqlData = "delete from wa_userdata where user_id= $safeId limit 1;";
		$sqlProfile = "delete from wa_userprofile where user_id= $safeId limit 1;";
		$ret = runSql($sqlInfo) & runSql($sqlData) & runSql($sqlProfile);
		
		$userCache = getCache('users');
		if ($userCache)
		{
			while (list($key, $value) = each($userCache))
			{
				if ($value['user_id'] == $safeId)
				{
					unset($userCache[$key]);
					break;
				}
			}
			setCache('users', $userCache);
		}
		return $ret;
	}
}
?>

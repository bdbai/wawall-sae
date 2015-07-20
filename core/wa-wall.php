<?php
class WaWall
{
	public function GetWalltypes()
	{
		$cacheKey = 'walltype';
		$cache = getCache($cacheKey);
		if ($cache)
		{
			return $cache;
		}
		else
		{
			$sql = 'select `walltype_id`, `walltype_name` from wa_walltype';
			$result = getData($sql);
			if ($result)
			{
				$ret = array();
				while (list($key, $value) = each($result))
				{
					$id = intval($value['walltype_id']);
					$name = $value['walltype_name'];
					$ret[$id] = $name;
				}
				setCache($cacheKey, $ret);
				return $ret;
			}
			return null;
		}
	}
	public function CreateWall($creator, $name, $desc, $type = 1, $access = 5)
	{
		try
		{
			$safeCreator = ensureInt($creator, 10);
			$safeName = '\'' . ensureString($name, 30) . '\'';
			$safeDesc = '\''. ensureString($desc, 300) . '\'';
			$safeType = ensureInt($type, 3);
			$safeAccess = ensureInt($access, 2);
		}
		catch (Exception $e)
		{
			throw new Exception('创建墙失败：' . $e -> getMessage());
			return null;
		}
		
		$sql = "insert into wa_wall (`wall_creator`, `wall_name` , `wall_type`, `wall_desc`, `wall_access`) values($safeCreator, $safeName, $safeType, $safeDesc, $safeAccess);";
		$result = runSql($sql);
		if ($result)
		{
			$ret = lastId();
			$cacheKey = 'creatorwall_' . $safeCreator;
			$cache = getCache($cacheKey);
			if ($cache)
			{
				array_unshift($cache, array('wall_id' => $ret));
				setCache($cacheKey, $cache);
			}
			$cacheKey = 'accesswall_' . $safeAccess;
			$cache = getCache($cacheKey);
			if ($cache)
			{
				array_unshift($cache, array('wall_id' => $ret));
				setCache($cacheKey, $cache);
			}
			$cacheKey = 'typewall_' . $safeType;
			$cache = getCache($cacheKey);
			if ($cache)
			{
				array_unshift($cache, array('wall_id' => $ret));
				setCache($cacheKey, $cache);
			}
			return $ret;
		}
		else
		{
			throw new Exception('创建墙失败。');
			return null;
		}
	}
	
	public function GetWallInfo($id)
	{
		try
		{
			$safeId = ensureInt($id, 10);
		}
		catch (Exception $e)
		{
			throw new Exception('获取墙信息失败：' . $e -> getMessage());
			return null;
		}
		
		$cacheKey = 'wallinfo_' . $safeId;
		$cache = getCache($cacheKey);
		if ($cache)
		{
			return $cache;
		}
		$sql = "select `wall_id`, `wall_creator`, `wall_name`, `wall_type`, `wall_desc`, `wall_access`, `wall_joinkey`, `wall_bgtype`, `wall_usercount` from wa_wall where `wall_id` = $safeId limit 1";
		$ret = getData($sql);
		if ($ret)
		{
			$ret = $ret[0];
			setCache($cacheKey, $ret);
		}
		return $ret;
	}
		
	public function SetWallInfo($id, $info)
	{
		$safeInfo = array();
		try
		{
			$safeId = ensureInt($id, 10);
			$safeInfo['wall_creator'] = ensureInt($info['wall_creator'], 10);
			$safeInfo['wall_name'] = '\'' . ensureString($info['wall_name'], 30) . '\'';
			$safeInfo['wall_type'] = ensureInt($info['wall_type'], 3);
			$safeInfo['wall_desc'] = '\'' . ensureString($info['wall_desc'], 300) . '\'';
			$safeInfo['wall_access'] = ensureInt($info['wall_access'], 2);
			if ($info['wall_joinkey'] != null)
			{
				$safeInfo['wall_joinkey'] =  '\'' . ensureString($info['wall_joinkey'], 40) . '\'';
			}
			if ($info['wall_bgtype'] != null)
			{
				$safeInfo['wall_bgtype'] = ensureInt($info['wall_bgtype'], 1);
			}
			$safeInfo['wall_usercount'] = ensureInt($info['wall_usercount'], 10);
		}
		catch (Exception $e)
		{
			throw new Exception('设置墙信息失败：' . $e -> getMessage());
			return false;
		}
		
		$sql = 'update wa_wall set';
		while (list($key, $value) = each($safeInfo))
		{
			$sql .= " `$key` = $value,";
		}
		$sql = trim($sql, ',');
		$sql .= ' where wall_id = ' . $safeId . ' limit 1;';
		$ret = runSql($sql);
		if ($ret)
		{
			$cacheKey = 'wallinfo_' . $safeId;
			setCache($cacheKey, null);
		}
		return $ret;
	}
	public function FindWallIdsByCreator($creator)
	{
		try
		{
			$safeCreator = ensureInt($creator);
		}
		catch (Exception $e)
		{
			throw new Exception('通过创建者查找墙失败：' . $e -> getMessage());
			return null;
		}
		
		$cacheKey = 'creatorwall_' . $safeCreator;
		$cache = getCache($cacheKey);
		if ($cache)
		{
			return $cache;
		}
		$sql = "select `wall_id` from wa_wall where `wall_creator` = $safeCreator order by `wall_id` desc;";
		$ret = getData($sql);
		if ($ret)
		{
			setCache($cacheKey, $ret);
		}
		return $ret;
	}
	
	public function FindWallIdsByAccess($access, $limit)
	{
		try
		{
			$safeAccess = ensureInt($access, 2);
			$safeLimit = ensureInt($limit);
		}
		catch (Exception $e)
		{
			throw new Exception('通过权限查找墙失败：' . $e -> getMessage());
			return null;
		}
		
		$cacheKey = 'accesswall_' . $safeAccess;
		$cache = getCache($cacheKey);
		if ($cache && ($safeLimit > 0 || count($cache) >= $safeLimit))
		{
			return $cache;
		}
		
		$sql = "select `wall_id` from wa_wall where `wall_access` = $safeAccess order by `wall_id` desc " . ($safeLimit > 0 ? "limit $safeLimit;" : ';');
		$ret = getData($sql);
		if ($ret)
		{
			setCache($cacheKey, $ret);
		}
		return $ret;
	}
	public function FindWallIdsByType($type, $limit)
	{
		try
		{
			$safeType = ensureInt($type, 3);
			$safeLimit = ensureInt($limit);
		}
		catch (Exception $e)
		{
			throw new Exception('通过类型查找墙失败：' . $e -> getMessage());
			return null;
		}
		
		$cacheKey = 'typewall_' . $safeType;
		$cache = getCache($cacheKey);
		if ($cache && ($safeLimit > 0 || count($cache) >= $safeLimit))
		{
			return $cache;
		}
		
		$sql = "select `wall_id` from wa_wall where `wall_type` = $safeType order by `wall_id` desc " . ($safeLimit > 0 ? "limit $safeLimit;" : ';');
		$ret = getData($sql);
		if ($ret)
		{
			setCache($cacheKey, $ret);
		}
		return $ret;
	}
	
	public function AddUserToWall($user, $id)
	{
		try
		{
			$safeUser = ensureInt($user, 10);
			$safeId = ensureInt($id, 10);
		}
		catch (Exception $e)
		{
			throw new Exception('墙添加用户失败：' . $e -> getMessage());
			return null;
		}
		
		$cacheKey = 'userwall_' . $safeUser;
		$cache = getCache($cacheKey);
		if ($cache)
		{
			array_unshift($cache, array('wall_id' => $safeId));
			setCache($cacheKey, $cache);
		}
		$cacheKey = 'walluser_' . $safeId;
		$cache = getCache($cacheKey);
		if ($cache)
		{
			array_unshift($cache, array('user_id' => $safeUser));
			setCache($cacheKey, $cache);
		}
		
		$sql = "insert into wa_walluser (`user_id`, `wall_id`) values ($safeUser, $safeId);";
		$result = runSql($sql);
		if (!$result)
		{
			throw new Exception('墙添加用户失败。');
			return null;
		}
		$lastId = lastId();
		if (!$lastId)
		{
			throw new Exception('墙添加用户失败。');
			return null;
		}
		return $lastId;
	}
	public function RemoveUserFromWall($user, $id)
	{
		try
		{
			$safeUser = ensureInt($user, 10);
			$safeId = ensureInt($id, 10);
		}
		catch (Exception $e)
		{
			throw new Exception('墙删除用户失败：' . $e -> getMessage());
			return null;
		}
		
		$cacheKey = 'userwall_' . $safeUser;
		$cache = getCache($cacheKey);
		if ($cache)
		{
			while(list($key, $value) = each($cache))
			{
				if ($value['wall_id'] = $safeId)
				{
					unset($cache[$key]);
					setCache($cacheKey, $cache);
					break;
				}
			}
		}
		$cacheKey = 'walluser_' . $safeId;
		$cache = getCache($cacheKey);
		if ($cache)
		{
			while(list($key, $value) = each($cache))
			{
				if ($value['user_id'] = $safeUser)
				{
					unset($cache[$key]);
					setCache($cacheKey, $cache);
					break;
				}
			}
		}
		
		$sql = "delete from wa_walluser where `wall_id` = $safeId AND `user_id` = $safeUser;";
		$result = runSql($sql);
		if (!$result)
		{
			throw new Exception('墙删除用户失败。');
			return false;
		}
		return $result;
	}
	public function FindWallIdsByUser($user)
	{
		try
		{
			$safeUser = ensureInt($user, 10);
		}
		catch (Exception $e)
		{
			throw new Exception('通过用户查找墙失败：' . $e -> getMessage());
			return null;
		}
		
		$cacheKey = 'userwall_' . $safeUser;
		$cache = getCache($cacheKey);
		if ($cache)
		{
			return $cache;
		}
		$sql = "select `wall_id` from wa_walluser where `user_id` = $safeUser order by `walluser_id` desc;";
		$ret = getData($sql);
		if ($ret)
		{
			setCache($cacheKey, $ret);
		}
		return $ret;
	}
	public function FindUserIdsByWall($id, $limit = 0)
	{
		try
		{
			$safeId = ensureInt($id, 10);
			$safeLimit = ensureInt($limit);
		}
		catch (Exception $e)
		{
			throw new Exception('通过墙查找用户失败：' . $e -> getMessage());
			return null;
		}
		
		$cacheKey = 'walluser_' . $safeId;
		$cache = getCache($cacheKey);
		if ($cache && ($safeLimit > 0 || count($cache) >= $safeLimit))
		{
			return $cache;
		}
		$sql = "select `user_id` from wa_walluser where `wall_id` = $safeId order by `walluser_id` desc" . ($safeLimit > 0 ? "limit $safeLimit;" : ';');
		$ret = getData($sql);
		if ($ret)
		{
			setCache($cacheKey, $ret);
		}
		return $ret;
	}
	
	public function DeleteWall($id)
	{
		try
		{
			$safeId = ensureInt($id, 10);
		}
		catch (Exception $e)
		{
			throw new Exception('删除墙时出错：' . $e -> getMessage());
			return false;
		}
		
		$wallInfo = self::GetWallInfo($safeId);
		if (!$wallInfo)
		{
			throw new Exception('删除墙时出错。');
			return false;
		}
		$cacheKey = 'creatorwall_' . $wallInfo['wall_creator'];
		$cache = getCache($cacheKey);
		if ($cache)
		{
			while (list($key, $value) = each($cache))
			{
				if ($value['wall_id'] == $safeId)
				{
					unset($cache[$key]);
					setCache($cacheKey, $cache);
					break;
				}
			}
		}
		$cacheKey = 'accesswall_' . $wallInfo['wall_access'];
		$cache = getCache($cacheKey);
		if ($cache)
		{
			while (list($key, $value) = each($cache))
			{
				if ($value['wall_id'] == $safeId)
				{
					unset($cache[$key]);
					setCache($cacheKey, $cache);
					break;
				}
			}
		}
		$cacheKey = 'typewall_' . $wallInfo['wall_type'];
		$cache = getCache($cacheKey);
		if ($cache)
		{
			while (list($key, $value) = each($cache))
			{
				if ($value['wall_id'] == $safeId)
				{
					unset($cache[$key]);
					setCache($cacheKey, $cache);
					break;
				}
			}
		}
		$users = self::FindUserIdsByWall($safeId);
		if ($users)
		{
			while (list($key, $value) = each($users))
			{
				$cacheKey = 'userwall_'. $value['user_id'];
				$cache = getCache($cacheKey);
				if ($cache)
				{
					while (list($userKey, $userValue) = each($cache))
					{
						if ($userValue['wall_id'] == $safeId)
						{
							unset($cache[$userKey]);
							setCache($cacheKey, $cache);
							break;
						}
					}
				}
			}
		}
		$cacheKey = 'walluser_' . $safeId;
		setCache($cacheKey, null);
		$cacheKey = 'wallinfo_' . $safeId;
		setCache($cacheKey, null);
		
		$wallSql = "delete from wa_wall where `wall_id` = $safeId limit 1;";
		$wallUserSql = "delete from wa_walluser where `wall_id` = $safeId;";
		if (runSql($wallSql) && runSql($wallUserSql))
		{
			return true;
		}
		else
		{
			throw new Exception('删除墙时出错。');
		}
	}
}
?>
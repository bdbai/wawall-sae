<?php
class WaDialog
{
	private function findOppositeInDbArray($arr, $opposite)
	{
			foreach ($arr as $dialogOpposite)
			{
				if ($dialogOpposite['user_opposite'] == $opposite)
				{
					return true;
				}
			}
			return false;
	}
	public function FindDialogIdsByUser($user)
	{
		try
		{
			$safeUser = ensureInt($user);
		}
		catch (Exception $e)
		{
			throw new Exception('通过用户查找会话失败：' . $e -> getMessage());
			return null;
		}
		
		$cacheKey = 'dialoguser_' . $safeUser;
		$cache = getCache($cacheKey);
		if ($cache)
		{
			return $cache;
		}
		$sql = "select * from wa_dialog where `user_id` = $safeUser order by `dialog_id` desc;";
		$ret = getData($sql);
		if ($ret)
		{
			setCache($cacheKey, $ret);
			return $ret;
		}
		return null;
	}
	public function CreateDialog($user, $opposite)
	{
		try
		{
			$safeUser = ensureInt($user, 10);
			$safeOpposite = ensureInt($opposite, 10);
		}
		catch (Exception $e)
		{
			throw new Exception('创建消息对话失败：' . $e -> getMessage());
			return false;
		}
		
		$ids = self::FindDialogIdsByUser($safeUser);
		if ($ids)
		{
			while (list($key, $value) = each($ids))
			{
				if ($value['user_opposite'] == $safeOpposite)
				{
					return $value['dialog_id'];
				}
			}
		}
		
		$sql = "insert into wa_dialog (`user_id`, `user_opposite`) values ($safeUser, $safeOpposite)";
		$ret = runSql($sql);
		$lastId = lastId();
		if ($ret && $lastId)
		{
			$cacheKey = "dialoguser_$safeUser";
			$cache = getCache($cacheKey);
			if ($cache)
			{
				array_unshift($cache, array(
					'dialog_id' => $lastId,
					'user_id' => $safeUser,
					'user_opposite' => $safeOpposite
				));
				setCache($cacheKey, $cache);
			}
			return $lastId;
		}
		return null;
	}
	public function DeleteDialog($user, $opposite)
	{
		try
		{
			$safeUser = ensureInt($user, 10);
			$safeOpposite = ensureInt($opposite, 10);
		}
		catch (Exception $e)
		{
			throw new Exception('删除消息对话失败：' . $e -> getMessage());
			return false;
		}
		
		$assocDialogs = self::FindDialogIdsByUser($safeUser);
		$dialogId = false;
		if ($assocDialogs)
		{
			while(list($key, $value) = each($assocDialogs))
			{
				if ($value['user_opposite'] == $safeOpposite)
				{
					$dialogId = $value['dialog_id'];
					unset($assocDialogs[$key]);
					$cacheKey = 'dialoguser_' . $safeUser;
					setCache($cacheKey, $assocDialogs);
					break;
				}
			}
		}
		
		$sql = "delete from wa_dialog where ";
		if ($dialogId)
		{
			$sql .= "`dialog_id` = $dialogId";
		}
		else
		{
			$sql .= "`user_id` = $safeUser AND `user_opposite` = $safeOpposite";
		}
		$sql .= ' limit 1';
		$ret = runSql($sql);
		return $ret;
	}
	/*public function GetDialogInfo($id)
	{
		try
		{
			$safeId = ensureInt($id);
		}
		catch (Exception $e)
		{
			throw new Exception('获取消息对话信息失败：' . $e -> getMessage());
			return null;
		}
		
		$cacheKey = 'dialog'
	}*/
}
class WaMsg
{
	public function CreateMsg($sender, $receiver, $content, $sysmsg = false)
	{
		try
		{
			$safeSender = ensureInt($sender, 10);
			$safeReceiver = ensureInt($receiver, 10);
			$safeContent = '\'' . ensureString($content, 1000) . '\'';
		}
		catch (Exception $e)
		{
			throw new Exception('创建消息失败：' . $e -> getMessage());
			return null;
		}
		if (!$sysmsg)
		{
			WaDialog::CreateDialog($safeSender, $safeReceiver);
		}
		WaDialog::CreateDialog($safeReceiver, $safeSender);
		
		$msgSql = "insert into wa_msg (`msg_sender`, `msg_receiver`, `msg_content`) values ($safeSender, $safeReceiver, $safeContent)";
		$result = runSql($msgSql);
		if (!$result)
		{
			throw new Exception('创建消息失败。');
			return null;
		}
		$lastId = lastId();
		$cacheKey = "userunreadmsg_$safeReceiver";
		$cache  =getCache($cacheKey);
		if ($cache)
		{
			array_unshift($cache, array('msg_id' => $lastId));
			setCache($cacheKey, $cache);
		}
		/*$cacheKey = "usermsg_$safeReceiver";
		$cache  =getCache($cacheKey);
		if ($cache)
		{
			array_unshift($cache, array('msg_id' => $lastId));
			setCache($cacheKey, $cache);
		}
		$cacheKey = 'userunreadmsg_' . $safeReceiver;*/
		$cacheKey = "usermsg_$safeSender_$safeReceiver";
		$cache = getCache($cacheKey);
		if ($cache)
		{
			array_unshift($cache, array(
				'msg_id' => $lastId
			));
			setCache($cacheKey, $cache);
		}
		$cacheKey = "usermsg_$safeReceiver_$safeSender";
		$cache = getCache($cacheKey);
		if ($cache)
		{
			array_unshift($cache, array(
				'msg_id' => $lastId
			));
			setCache($cacheKey, $cache);
		}
		return $lastId;
	}
	
	public function GetMsgInfo($id)
	{
		try
		{
			$safeId = ensureInt($id);
		}
		catch (Exception $e)
		{
			throw new Exception('获取消息出错：' . $e -> getMessage());
			return null;
		}
		
		$cacheKey = 'msginfo_' . $safeId;
		$cache = getCache($cacheKey);
		if (!$cache)
		{
			$sql = "select `msg_id`, `msg_receiver`, `msg_sender`, `msg_time`, `msg_content`, `msg_read`, `sender_deleted`, `receiver_deleted` from wa_msg where `msg_id` = $safeId limit 1";
			$ret = getData($sql);
			if (!$ret)
			{
				throw new Exception('获取消息出错。');
				return null;
			}
			$ret = $ret[0];
			setCache($cacheKey, $ret);
			return $ret;
		}
		else
		{
			return $cache;
		}
	}
	public function FindMsgIdsByUserUnread($user)
	{
		try
		{
			$safeUser = ensureInt($user, 10);
		}
		catch (Exception $e)
		{
			throw new Exception('通过用户查找未读消息时出错：' . $e -> getMessage());
			return null;
		}
		
		$cacheKey = "userunreadmsg_$safeUser";
		$cache = getCache($cacheKey);
		if ($cache)
		{
			return $cache;
		}
		$sql = "select `msg_id` from wa_msg where `msg_receiver` = $safeUser and `msg_read` = 0 and `receiver_deleted` = false order by `msg_id` desc";
		$ret = getData($sql);
		if ($ret != null)
		{
			setCache($cacheKey, $ret);
		}
		return $ret;
	}
	public function FindMsgIdsByUser($id, $opposite, $limit = 0)
	{
		try
		{
			$safeId = ensureInt($id, 10);
			$safeOpposite = ensureInt($opposite, 10);
			$safeLimit = ensureInt($limit);
		}
		catch (Exception $e)
		{
			throw new Exception('通过用户查找来往消息时出错：' . $e -> getMessage());
			return null;
		}
		
		$cacheKey = "usermsg_$safeId_$safeOpposite";
		$cache = getCache($cacheKey);
		if ($cache && $safeLimit > 0 && count($cache) >= $safeLimit)
		{
			return $cache;
		}
		$sql = "select `msg_id` from wa_msg where (`msg_sender` = $safeOpposite AND `msg_receiver` = $safeId AND `receiver_deleted` = false) or (`msg_sender` = $safeId AND `msg_receiver` = $safeOpposite AND `sender_deleted` = false)  " . 'order by `msg_id` desc ' . ($safeLimit > 0 ? "limit $safeLimit;" : ';');
		$ret = getData($sql);
		if ($ret != null)
		{
			setCache($cacheKey, $ret);
		}
		return $ret;
	}
	/*public function FindMsgIdsByUserReceiver($id, $receiver, $limit = 0)
	{
		try
		{
			$safeId = ensureInt($id, 10);
			$safeReceiver = ensureInt($receiver, 10);
			$safeLimit = ensureInt($limit);
		}
		catch (Exception $e)
		{
			throw new Exception('通过用户和收件人查找消息时出错：' . $e -> getMessage());
			return null;
		}
		
		$cacheKey = "usermsg_$safeId_$safeReceiver";
		$cache = getCache($cacheKey);
		if ($cache && ($safeLimit > 0 || count($cache) >= $safeLimit)
		{
			return $cache;
		}
		$sql = "select `msg_id` from wa_msguser where msg_sender = $safeId AND msg_receiver = $safeReceiver " . ($safeLimit > 0 ? 'limit $safeLimit;' : ';') . 'order by `msg_id` desc';
		$ret = getData($sql);
		if ($ret != null)
		{
			setCache($cacheKey, $ret);
		}
		return $ret;
	}*/
	public function SetMsgRead($id)
	{
		try
		{
			$safeId = ensureInt($id, 10);
		}
		catch (Exception $e)
		{
			throw new Exception('设置已读时出错：' . $e -> getMessage());
			return false;
		}
		
		$msgInfo = self::GetMsgInfo($id);
		if ($msgInfo)
		{
			$msgReceiver = $msgInfo['msg_receiver'];
			$cacheKey = 'userunreadmsg_' . $msgReceiver;
			$cache = getCache($cacheKey);
			if ($cache)
			{
				while(list($key, $value) = each($cache))
				{
					if ($value['msg_id'] == $safeId)
					{
						unset($cache[$key]);
						break;
					}
				}
				setCache($cacheKey, $cache);
			}
		}
		$cacheKey = 'msginfo_' . $safeId;
		$cache = getCache($cacheKey);
		if ($cache)
		{
			$cache['msg_read'] = 1;
			setCache($cacheKey, $cache);
		}
		
		$sql = "update wa_msg set `msg_read` = 1 where `msg_id` = $safeId limit 1";
		$ret = runSql($sql);
		if (!$ret)
		{
			throw new Exception('设置已读时出错。');
			return false;
		}
		return true;
	}
	public function DeleteMsg($id, $user)
	{
		try
		{
			$safeId = ensureInt($id, 10);
		}
		catch (Exception $e)
		{
			throw new Exception('删除消息时出错：' . $e -> getMessage());
			return false;
		}
		
		$msgInfo = self::GetMsgInfo($safeId);
		if (!$msgInfo)
		{
			return false;
		}
		if ($msgInfo['msg_sender'] == $user)
		{
			$me = 'sender';
			$opposite = 'receiver';
		}
		else if ($msgInfo['msg_receiver'] == $user)
		{
			$me = 'receiver';
			$opposite = 'sender';
		} 
		else
		{
			throw new Exception('非消息相关用户。');
			return false;
		}
		self::SetMsgRead($msgInfo['msg_id']);
		$cacheKey = "usermsg_";
		$cacheKey .= $msgInfo['msg_' . $me] . '_' . $msgInfo['msg_' . $opposite];
		$cache = getCache($cacheKey);
		if ($cache)
		{
			while(list($key, $value) = each($cache))
			{
				if ($value['msg_id'] = $safeId)
				{
					unset($cache[$key]);
					break;
				}
			}
			setCache($cacheKey, $cache);
		}
		$cacheKey = 'msginfo_' . $safeId;
		if ($msgInfo[$opposite . '_deleted'])
		{
			setCache($cacheKey, null);
			$sql = "delete from wa_msg where `msg_id` = $safeId limit 1;";
			$ret = runSql($sql);
		}
		else
		{
			$cache = getCache($cacheKey);
			if ($cache)
			{
				$cache[$me . '_deleted'] = true;
				setCache($cacheKey, $cache);	
			}
			$sql = "update wa_msg set `{$me}_deleted` = 1 where `msg_id` = $safeId limit 1";
			$ret = runSql($sql);
		}
		return $ret;
	}
	/*public function DeleteMsg($id, $user)
	{
		try
		{
			$safeId = ensureInt($id, 10);
			$safeUser = ensureInt($user, 10);
		}
		catch (Exception $e)
		{
			throw new Exception('删除消息时出错：' $e -> getMessage());
			return false;
		}
		
		$msgInfo = self::GetMsgInfo($id);
		if ($msgInfo)
		{
			if($msgInfo['msg_receiver'] == $safeUser)
			{
				self::SetMsgRead($safeId);
				
			}
		}
	}*/
}
?>
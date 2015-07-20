<?php
class WaPost
{
	function CreatePost($wall, $title, $content, $creator)
	{
		try
		{
			$safeWall = ensureInt($wall, 10);
			$safeTitle = '\'' . ensureString($title, 100, false) . '\'';
			$safeContent = '\'' . ensureString($content, 10000) . '\'';
			$safeCreator = ensureInt($creator, 10);
		}
		catch (Exception $e)
		{
			throw new Exception('创建墙贴时失败：' . $e -> getMessage());
			return null;
		}
		
		$sql = "insert into wa_post (`post_wall`, `post_title`, `post_content`, `post_creator`) values ($safeWall, $safeTitle, $safeContent, $safeCreator)";
		$ret = runSql($sql);
		if (!$ret)
		{
			throw new Exception('创建墙贴时失败。');
			return null;
		}
		$lastId = lastId();
		$cacheKey = 'wallpost_' . $safeWall;
		$cache = getCache($cacheKey);
		if ($cache)
		{
			array_unshift($cache, array('post_id' => $lastId));
		}
		setCache($cacheKey, $cache);
		
		return $lastId;
	}
	
	public function FindPostByWall($wall, $limit = 0)
	{
		try
		{
			$safeWall= ensureInt($wall, 10);
			$safeLimit = ensureInt($limit);
		}
		catch (Exception $e)
		{
			throw new Exception('通过墙查找墙贴失败：' . $e -> getMessage());
			return null;
		}
		
		$cacheKey = 'wallpost_' . $safeWall;
		$cache = getCache($cacheKey);
		if ($cache && $safeLimit > 0 && count($cache) >= $safeLimit)
		{
			return $cache;
		}
		$sql = "select `post_id` from wa_post where `post_wall` = $safeWall " . 'order by `post_id` desc ' . ($safeLimit > 0 ? "limit $safeLimit;" : ';');
		$ret = getData($sql);
		if ($ret != null)
		{
			setCache($cacheKey, $ret);
		}
		return $ret;
	}

	public function GetPostInfo($id)
	{
		try
		{
			$safeId = ensureInt($id, 10);
		}
		catch (Exception $e)
		{
			throw new Exception('获取墙贴信息失败：' . $e -> getMessage());
			return null;
		}
		
		$cacheKey = 'postinfo_' . $safeId;
		$cache = getCache($cacheKey);
		if ($cache == null)
		{
			$sql = "select `post_id`,`post_creator`,  `post_wall`, `post_title`, `post_content`, `post_time` from wa_post where post_id = $safeId limit 1";
			$ret = getData($sql);
			if ($ret == null)
			{
				throw new Exception('获取墙贴信息失败。');
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
	
	public function SetPostInfo($id, $wall, $title, $content)
	{
		try
		{
			$safeId = ensureInt($id, 10);
			$safeWall = ensureInt($wall, 10);
			$safeTitle = '\'' . ensureString($title, 100) . '\'';
			$safeContent = '\'' . ensureString($content, 10000) . '\'';
		}
		catch (Exception $e)
		{
			throw new Exception('设置墙贴信息失败：' . $e -> getMessage());
			return null;
		}
		
		$sql = "update wa_post set `post_wall` = $safeWall, `post_title` = $safeTitle, `post_content` = $safeContent where `post_id` = $safeId limit 1";
		$ret = runSql($sql);
		if (!$ret)
		{
			throw new Exception('设置墙贴信息失败。');
			return false;
		}
		$postInfo = self::GetPostInfo($safeId);
		$cacheKey = 'postinfo_' . $safeId;
		setCache($cacheKey, '');
		return $ret;		
	}
	
	public function DeletePost($id)
	{
		try
		{
			$safeId = ensureInt($id, 10);
		}
		catch (Exception $e)
		{
			throw new Exception('删除墙贴时出错：' . $e -> getMessage());
			return false;
		}
		
		$postInfo = self::GetPostInfo($safeId);
		$cacheKey = 'wallpost_' . $postInfo['post_wall'];
		$cache = getCache($cacheKey);
		if ($postInfo && $cache)
		{
			while (list($key, $value) = each($cache))
			{
				if ($value['post_id'] = $safeId)
				{
					unset($cache[$key]);
					break;
				}
			}
			setCache($cacheKey, $cache);
		}
		$cacheKey = 'postinfo_' . $safeId;
		setCache($cacheKey, null);
		
		$sql = "delete from wa_post where `post_id` = $safeId limit 1";
		$ret = runSql($sql);
		if (!$ret)
		{
			throw new Exception('删除墙贴时出错。');
			return null;
		}
		return $ret;
	}
}
?>
<?php
class WaComment
{
	public static function CreateComment($creator, $post, $content, $replyto)
	{
		try
		{
			$safeCreator = ensureInt($creator, 10);
			$safePost = ensureInt($post, 10);
			$safeContent = '\'' . ensureString($content, 1000) . '\'';
			$safeReplyto = '\'' . ensureString($replyto, 10, false, true) . '\'';
		}
		catch (Exception $e)
		{
			throw new Exception('创建回复时出错：' . $e -> getMessage());
			return null;
		}
		
		$sql = "insert into wa_comment (`comment_creator`, `comment_post`, `comment_content`, `comment_replyto`) values ($safeCreator, $safePost, $safeContent, $safeReplyto);";
		$result = runSql($sql);
		if (!$result)
		{
			throw new Exception('创建回复时出错。');
			return null;
		}
		$cacheKey = 'postcomment_' . $safePost;
		$cache = setCache($cacheKey, null);
		$ret = lastId();
		return $ret;
	}
	public static function FindCommentByPost($post, $limit =0)
	{
		try
		{
			$safePost = ensureInt($post, 10);
			$safeLimit = ensureInt($limit);
		}
		catch (Exception $e)
		{
			throw new Exception('通过墙贴查找评论失败：' . $e -> getMessage());
			return null;
		}
		
		$cacheKey = 'postcomment_' . $safePost;
		$cache = getCache($cacheKey);
		if ($cache && $safeLimit > 0 && count($cache) >= $safeLimit)
		{
			return $cache;
		}
		$sql = "select `comment_id` from wa_comment where `comment_post` = $safePost order by `comment_id` desc " . ($safeLimit > 0 ? "limit $safeLimit;" : ';');
		$ret = getData($sql);
		if ($ret != null)
		{
			setCache($cacheKey, $ret);
		}
		return $ret;
	}
	public static function GetCommentInfo($id)
	{
		try
		{
			$safeId = ensureInt($id);
		}
		catch (Exception $e)
		{
			throw new Exception('查询评论时出错：' . $e -> getMessage());
			return null;
		}
		
		$cacheKey = 'commentinfo_' . $safeId;
		$cache = getCache($cacheKey);
		if ($cache == null)
		{
			$sql = "select `comment_id`, `comment_creator`, `comment_post`, `comment_content`, `comment_time`, `comment_replyto` from wa_comment where comment_id = $safeId";
			$ret = getData($sql);
			if ($ret == null)
			{
				throw new Exception('查询评论时出错。');
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
	public static function DeleteComment($id)
	{
		try
		{
			$safeId = ensureInt($id);
		}
		catch (Exception $e)
		{
			throw new Exception('删除评论时出错：' . $e -> getMessage());
			return null;
		}
		
		$commentInfo = self::GetCommentInfo($id);
		$cacheKey = 'postcomment_' . $commentPost['comment_post`'];
		$cache = getCache($cacheKey);
		if ($commentInfo && $cache)
		{
			while (list($key, $value) = each($cache))
			{
				if ($value['comment_id'] == $safeId)
				{
					unset($cache[$key]);
					break;
				}
			}
			setCache($cacheKey, $cache);
		}
		$cacheKey = 'commentinfo_' . $safeId;
		setCache($cacheKey, null);
		$sql = "delete from wa_comment where comment_id = $safeId limit 1";
		$ret = runSql($sql);
		if (!$ret)
		{
			throw new Exception('删除评论时出错。');
			return null;
		}
		return $ret;
	}
}
?>

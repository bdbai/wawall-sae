<?php
class WaApp
{
	private function getInfo($name)
	{
		try
		{
			$safeInfo = ensureString($name, 11);
		}
		catch (Exception $e)
		{
			throw new Exception('读取应用信息：' . $name . ' 时出错：' . $e -> getMessage());
			return null;
		}
		$cacheKey = 'app_' . $safeInfo;
		$cache = getCache($cacheKey);
		if ($cache == null)
		{
			$result = getData("select `value` from wa_app where `name` = 'app_$name' limit 1");
			if ($result == null)
			{
				throw new Exception('读取应用信息：' . $name . ' 时出错。');
				return null;
			}
			else
			{
				$ret = $result[0]['value'];
				setCache($cacheKey, $ret);
				return $ret;
			}
		}
		else
		{
			return $cache;
		}
	}
	private function setInfo($name, $value)
	{
		try
		{
			$safeName = ensureString($name, 11);
			$safeValue = ensureString($value, 50);
		}
		catch (Exception $e)
		{
			throw new Exception('写入应用信息：$name -> $value 时出错：' . $e -> getMessage());
			return null;
		}
		$cacheKey = 'app_' . $safeName;
		setCache($cacheKey, $safeValue);
		$result = runSql("update wa_app set `value` = '$safeValue' where `name` = 'app_$safeName' limit 1");
		if (!$result)
		{
			throw new Exception('设置应用信息：' . $name . ' -> ' . $value . '时出错。');
		}
		return $result;
	}
	
	public function GetAppName()
	{
		return self::getInfo('name');
	}
	
	public function GetAppDesc()
	{
		return self::getInfo('desc');
	}
	
	public function GetAppRunning()
	{
		return (strtolower(self::getInfo('running')) == 'true');
	}
	
	public function GetAppNotices()
	{
		$cacheKey = 'app_cache';
		$cache = getCache($cacheKey);
		if ($cache == null)
		{
			$ret = getData('select value from wa_app where name = `notice`');
			if ($ret == null)
			{
				throw new Exception('读取应用信息时出错。');
				return null;
			}
			else
			{
				setCache($cacheKey, $ret);
				return $ret;
			}
		}
		else
		{
			return $cache;
		}
	}
	
	public function SetAppName($value)
	{
		return self::setInfo('name', $value);
	}
	
	public function SetAppDesc($value)
	{
		return self::setInfo('desc', $value);
	}
	
	public function SetAppRunning($value)
	{
		$safeValue = $value ? 'true' : 'false';
		if (self::GetAppRunning() == $value)
		{
			return true;
		}
		else
		{
			return self::setInfo('running', $safeValue);
		}
	}
	
	public function SetAppNotices($notices)
	{
		try
		{
			$safeNotices = ensureArray($notices, 0, 50);
		}
		catch (Exception $e)
		{
			throw new Exception('设置公告：' . implode(';', $notices) . '时出错。');
			return false;
		}
		$cacheKey = 'app_cache';
		setCache($cacheKey, $safeNotices);
		$sql = 'delete from wa_app where name = `notice` ; ';
		foreach ($notices as $safeNotice)
		{
			$sql .= 'insert into wa_app (`name`, `value`) values (`notice`, `' . $safeNotice . '`) ; ';
		}
		$result = runSql($sql);
		if (!$result)
		{
			throw new Exception('设置公告：' . implode(';', $notices) . '时出错。');
		}
		return $result;
	}
}

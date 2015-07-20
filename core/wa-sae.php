<?php
	/* SaeCounter
	 */
	$counter;
	function initCounter()
	{
		if($counter == null)
		{
			global $counter;
			$counter = new SaeCounter();
			$counter -> create('dbhit');
			$counter -> create('cachehit');
		}
	}
	
	/* SaeMysql
	 */
	$mysql = new SaeMysql();
	function getData($sql)
	{
		try
		{
			global $counter;
			initCounter();
			$counter -> incr('dbhit');
		} catch (Exception $e) {}
		global $mysql;
		return $mysql -> getData($sql);
	}
	function runSql($sql)
	{
		global $mysql;
		return $mysql -> runSql($sql);
	}
	function lastId()
	{
		global $mysql;
		return $mysql -> lastId();
	}
	function ensureInt($unsafeInt, $length = -1, $cut = false)
	{
		if (!is_numeric($unsafeInt)) 
		{
			throw new Exception('非数值。');
			return null;
		}
		$ret = intval($unsafeInt);
		if ($ret == 0) return 0;
		if ($ret > 0 && $ret <= 1)
		{
			$origLen = 1;
		}
		else
		{
			$origLen = intval(log(abs($ret), 10)) + 1;		
		}
		if ($length <= 0 || $origLen <= $length) return $unsafeInt;
		if ($cut)
		{
			if ($ret < 0) $negative = true;
			$ret = intval($numStr / (10 ^ ($origLen - $length)));
			if ($negative) $ret = -$ret;
			return $ret;
		}
		else
		{
			throw new Exception('数值过长。');
		}
	}
	function ensureString($unsafeString, $length = -1, $cut = false, $allowEmpty = false)
	{
		if ($unsafeString == '')
		{
			if ($allowEmpty)
			{
				return '';
			}
			else
			{
				throw new Exception('空字符串');
			}
		}
		if (!is_string($unsafeString)) 
		{
			throw new Exception('非字符串。');
			return null;
		}
		$ret  = $unsafeString;
		if (get_magic_quotes_gpc())
		{
			$ret = stripslashes($ret);
		}
		global $mysql;
		$ret = $mysql -> escape($ret);
		if ($length >= 0 && strlen($ret) > $length)
		{
			if ($cut)
			{
				$ret = substr($ret, 0, $length);
			}
			else
			{
				throw new Exception('字符串过长。');
				return null;
			}
		}
		return $ret;
	}
	function ensureDate($unsafeDate, $allowEmpty = false)
	{
		if ($allowEmpty && $unsafeDate == null)
		{
			return null;
		}
		if (!strtotime($unsafeDate))
		{
			throw new Exception('日期错误。');
		}
		else
		{
			return $unsafeDate;
		}
	}
	function ensureArray($unsafeArray, $keyLen = -1, $valueLen = -1)
	{
		$ret = array();
		reset($unsafeArray);
		while (list($key, $value) = each($unsafeArray))
		{
			//if (strlen($key) >$keyLen) continue;
			//if (is_string($value) && strlen($value) > $valueLen) continue;
			$safeKey = ensureString($key, $keyLen);
			$safeValue = ensureString($value, $valueLen);
			$ret[$safeKey] = $safeValue;
		}
		return $ret;
	}
	
	/* Sae memcache
	 */
	$memcache;
	function initCache()
	{
		global $memcache;
		if (!$memcache = memcache_init())
		{
			sae_debug('Memcache加载错误。');
		}
	}
	function getCache($key)
	{
		global $memcache;
		initCache();
		if (!$memcache) return null;
		$ret = memcache_get($memcache, $key);
		if ($ret !== null)
		{
			try
			{
				global $counter;
				initCounter();
				$counter -> incr('cachehit');
			} catch (Exception $e) {}
		}
		return $ret;
	}
	
	function setCache($key, $value)
	{
		global $memcache;
		initCache();
		if (!$memcache) return false;
		//$ret = memcache_set($memcache, $key, $value);
		return memcache_set($memcache, $key, $value);
	}
		
	/* SaeKV
	 */
	$kv = new SaeKV();
	$hasKvInited = false;
	function initKv()
	{
		global $kv;
		global $hasKvInited;
		if (!$hasKvInited)
		{
			if ($kv -> init())
			{
				$hasKvInited = true;
			}
			else
			{
				sae_debug('KVDB加载错误。');
				throw new Exception('KVDB加载错误。');
				$hasKvInited = false;
			}
		}
	}
	function getKv($key)
	{
		global $kv;
		global $hasKvInited;
		initKv();
		if (!$hasKvInited) return null;
		return $kv -> get($key);
	}
	function setKv($key, $value)
	{
		global $kv;
		global $hasKvInited;
		initKv();
		if (!$hasKvInited) return false;
		return $kv -> set($key, $value);
	}
	function deleteKv($key)
	{
		global $kv;
		global $hasKvInited;
		initKv();
		if (!$hasKvInited) return false;
		return $kv -> delete($key);
	}
?>
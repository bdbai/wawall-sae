<?php
class ErrorEx
{
	private $error;
	public static function ResetLastError()
	{
		$error = null;
	}
	public static function GetLastError()
	{
		return $error;
	}
	public static function SetLastError($info)
	{
		$error = $info;
		sae_debug($info);
	}
	public static function HasLastError()
	{
		return is_null($error);
	}
}
?>

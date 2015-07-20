<?php
class ErrorEx
{
	private $error;
	public function ResetLastError()
	{
		$error = null;
	}
	public function GetLastError()
	{
		return $error;
	}
	public function SetLastError($info)
	{
		$error = $info;
		sae_debug($info);
	}
	public function HasLastError()
	{
		return is_null($error);
	}
}
?>
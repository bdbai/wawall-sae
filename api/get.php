<?php
require('../function.php');
$running = WaApp::GetAppRunning();
if (!$running)
{
	returnError('暂时不可用，请稍后再试。');
	return;
}
if (strtolower($_SERVER['REQUEST_METHOD']) != 'get')
{
	returnError('请求方式错误。');
	die();
}
?>
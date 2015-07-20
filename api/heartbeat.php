<?php
require('./get.php');

$wall = $_GET['wall'];
$limit = $_GET['limit'];
$lastPost = $_GET['lastpost'];
$limit = intval($limit ? $limit : 20);
$limit = min($limit, 300);
/*try
{
	$posts = WaPost::FindPostByWall($wall, $limit);
	if (!$posts) throw new Exception('暂无新帖。');
}
catch (Exception $e)
{
	returnError($e -> getMessage());
	return;
}*/
$offset = 0;
while ($lastPost) {
try
{
	$posts = WaPost::FindPostByWall($wall, $offset + 20);
	if (!$posts) throw new Exception('暂无新帖。');
}
catch (Exception $e)
{
	returnError($e -> getMessage());
	return;
}
if (count($posts) < $offset + 20) {
	$offset = count($posts); //$offset = 0;
	break;
}
	for ($i = 0; $i < $offset; $i++) {
		next($posts);
	}
	while (list($key, $post) = each($posts)) {
		$offset++;
		if ($post['post_id'] == $lastPost) {
			break 2;
		}
	}
}
$ret = array();
$totalLimit = $offset + $limit;
try
{
	$posts = WaPost::FindPostByWall($wall, $totalLimit);
	if (!$posts) throw new Exception('暂无新帖。');
}
catch (Exception $e)
{
	returnError($e -> getMessage());
	return;
}
	for ($i = 0; $i < $offset; $i++) {
		next($posts);
	}
	while (list($key, $post) = each($posts))
	{
		$post_id = $post['post_id'];
		try
		{
			$postInfo = WaPost::GetPostInfo($post_id);
			if (!$postInfo) throw new Exception('');
		}
		catch (Exception $e)
		{
			continue;
		}
		$post = array();
		$post['post_id'] = $postInfo['post_id'];
		$post['post_title'] = $postInfo['post_title'];
		$post['post_content'] = $postInfo['post_content'];
	
		$creator = $postInfo['post_creator'];
		try
		{
			$creator_info = WaUser::GetUserInfo($creator);
		}
		catch (Exception $e)
		{
			continue;
		}
		$post['post_creator'] = $creator_info['user_name'];
		array_unshift($ret, $post);
		if (--$limit <= 0) {
			break;
		}
	}
echo json_encode(array('post' => $ret));
?>
<?php
require('../function.php');
$appRunnning = WaApp::GetAppRunning();
if (!$appRunnning):
	header('Location: ./error.html');
	die(0);
	else:
	$appName = WaApp::GetAppName();
	$wall = $_GET['wall'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<?php
$err = false;
	try
	{
		$info = WaWall::GetWallInfo($wall);
		if (!$info) throw new Exception('找不到墙。');
	}
	catch (Exception $e)
	{
		$err = $e -> getMessage();
	}
	if ($err === false) : 
?>
<title><?php echo $info['wall_name'] . ' - ' . $appName; ?></title>
<link rel="stylesheet" href="http://lib.sinaapp.com/js/bootstrap/3.0.0/css/bootstrap.min.css" />
<link rel="stylesheet" href="/page/style.css" />
<script type="text/javascript" src="http://lib.sinaapp.com/js/jquery/2.0.3/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="http://lib.sinaapp.com/js/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/page/wall.js"></script>
</head>
<body>
<div id="load-wrap" class="load-cover">
  <div id="wall-loading" class="loading load-cover"> <svg xmlns="http://www.w3.org/2000/svg"> 
    <!-- Created with Method Draw - http://github.com/duopixel/Method-Draw/ -->
    <g>
      <title>稍等一下，马上就好。</title>
      <ellipse ry="50%" rx="50%" id="svg_1" cy="50%" cx="50%" stroke-width="0" stroke="#fff" fill="#ccc"/>
    </g>
    </svg> <span>loading...</span> </div>
</div>
<div id="wall-maincontainer" class="wall-container">
  <div id="wall-side" class="wall-container">
    <?php 
	if ($loginUser)
	{
	  if (!signedIn($loginUser)) : 
	  ?>
    <button id="sign-btn" class="btn btn-warning btn-block">点击签到</button>
    <?php else: ?>
    <button class="btn btn-warning btn-block" disabled="disabled">已签到<?php echo getSignDays($loginUser); ?>天</button>
    <?php endif; 
	}
	?>
    <div class="container">
      <div class="row">
        <div class="col-sm-6">
          <div id="wall-nav-prev" class="btn btn-block btn-success">上一墙贴</div>
        </div>
        <div class="col-sm-6">
          <div id="wall-nav-next" class="btn btn-block  btn-success">下一墙贴</div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div id="wall-zoom-in" class="btn btn-block btn-success">放大</div>
        </div>
        <div class="col-sm-12">
          <div id="wall-zoom-out" class="btn btn-block btn-success">缩小</div>
        </div>
      </div>
    </div>
    <div id="wall-user-panel" class="widget panel panel-primary"> <span class="list-group-item widget-title">
      <?php
	$creator = $info['wall_creator'];
	$creatorInfo = WaUser::GetUserInfo($creator);
	if ($creatorInfo) : 
	$creatorName = $creatorInfo['user_name'];
	?>
      <div class="wall-user">
        <h3>用户列表</h3>
        <h5>墙主：<?php echo $creatorName; ?></h5>
      </div>
      <?php endif; ?>
      </span>
      <?php
	$users = WaWall::FindUserIdsByWall($wall);
	if ($users) : 
	foreach ($users as $user) : 
	$userInfo = WaUser::GetUserInfo($user['user_id']);
	if ($userInfo) : 
	?>
      <span class="wall-user list-group-item">
      <h4 class="list-group-item-text"><?php echo $userInfo['user_name']; ?></h4>
      </span>
      <?php 
	  endif;
	  endforeach; 
	endif; 
	?>
    </div>
    <?php if ($loginUser) : ?>
    <div id="add-post-btn" class="btn btn-primary"> 发新帖——需要1墙砖 </div>
    <div id="add-post-panel" class="panel container">
      <div class="panel-body">
        <form class="form-group" role="form">
          <div class="form-group" id="add-post-title-group">
            <label for="title" class="control-label">标题</label>
            <input type="text" class="form-control" id="title" placeholder="请输入标题" maxlength="100">
          </div>
          <div class="form-group" id="add-post-content-group">
            <label for="content" class="control-label">内容</label>
            <?php /*?><input type="password" class="form-control" id="pass" placeholder="请输入密码"><?php */?>
            <div id="content" contenteditable="true" class="add-post-content-div"> </div>
          </div>
          <div id="add-post-error-group" class="form-group">
            <label id="add-post-error-label" class="control-label error-label"></label>
          </div>
          <div class="form-group">
            <button id="submit-add-post-btn" type="submit" class="btn btn-default">发布</button>
          </div>
        </form>
      </div>
    </div>
    <?php endif; ?>
    <div class="bdsharebuttonbox"><a href="#" class="bds_more" data-cmd="more">分享到：</a><a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间">QQ空间</a><a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博">新浪微博</a><a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博">腾讯微博</a><a href="#" class="bds_renren" data-cmd="renren" title="分享到人人网">人人网</a><a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信">微信</a></div>
    <script>window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"嗨，我在玩<?php echo $appName; ?>，这里的墙好好玩，特别是这个墙：<?php echo $info['wall_name']; ?>。一起来玩吧！","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"16"},"share":{"bdSize":16}};
with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];
</script> 
  </div>
  <div id="post-container" class="wall-container"><div id="post-detail" class="wall-post"></div></div>
</div>
<script type="text/javascript">
init(<?php echo $wall; ?>);
</script>
</body>
<?php else : ?>
</head>
<body>
<script type="text/javascript">
!function()
{
	alert('暂时找不到这堵墙:( 先去大厅逛逛吧。');
	location.href = '/#hall';
	return;
}();
</script>
</body>
<?php endif; ?>
</html>
<?php
endif; //AppRunning
?>

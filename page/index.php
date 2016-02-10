<?php
require('../function.php');
$appRunnning = WaApp::GetAppRunning();
if (!$appRunnning):
	header('Location: ./error.html');
	die(0);
	else:
  header('Content-Type: text/html; charset=UTF-8');
	$appName = WaApp::GetAppName();
	$appDesc = WaApp::GetAppDesc();
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0" />
<title><?php echo "$appName - $appDesc";?></title>
<link rel="stylesheet" href="http://lib.sinaapp.com/js/bootstrap/3.0.0/css/bootstrap.min.css" />
<link rel="stylesheet" href="/page/style.css" />
<?php /*<link rel="stylesheet" href="http://lib.sinaapp.com/js/bootstrap/3.0.0/css/bootstrap-theme.min.css" />*/ ?>
<script type="text/javascript" src="http://lib.sinaapp.com/js/jquery/2.0.3/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="http://lib.sinaapp.com/js/bootstrap/3.0.0/js/bootstrap.min.js"></script>
</head>
<body>
<script type="text/javascript" src="/page/index.js"></script>
<nav id="main-nav" class="navbar navbar-default navbar-inverse navbar-fixed-top" role="navigation">
	<div class="navbar-header"><a class="navbar-brand" href="#home" title="主页"><?php echo $appName; ?></a>
		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-body" aria-expanded="false" aria-controls="navbar"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
	</div>
	<div id="navbar-body" class="navbar-collapse collapse">
		<ul class="nav navbar-nav">
			<li class="divider"></li>
			<li><a href="#hall" title="大厅">大厅</a></li>
			<li><a href="#mywall" title="我的墙">我的墙</a></li>
			<li><a href="#inprogress" title="动态（建设中）" class="unimplemented">动态</a></li>
			<li><a href="#inprogress" title="消息（建设中）" class="unimplemented">消息</a></li>
		</ul>
		<ul class="nav navbar-nav navbar-right">
			<?php
  global $loginUser;
  if ($loginUser) : 
  ?>
			<li> <a href="#inprogress" title="我的资料（建设中）" class="unimplemented" style="text-decoration:none">
				<?php
  $info = WaUser::GetUserInfo($loginUser);
  $data = WaUser::GetUserData($loginUser);
  if ($info) 
  {
	  echo '您好，' . $info['user_name'];
  }
  ?>
				</a></li>
			<?php 
	  if (!signedIn($loginUser)) : 
	  ?>
			<li>
				<button id="sign-btn" class="btn btn-warning navbar-btn">点击签到</button>
			</li>
			<?php else: ?>
			<li>
				<button class="btn btn-warning navbar-btn" disabled="disabled">已签到<?php echo getSignDays($loginUser); ?>天</button>
			</li>
			<?php endif; ?>
			<li><a>经验：<span id="exp"><?php echo $data['user_exp']; ?></span></a></li>
			<li><a>墙砖：<span id="wealth"><?php echo $data['user_wealth']; ?></span></a></li>
			<li><a href="#logout" title="退出">退出</a></li>
			<div id="canlogout" style="display: none;"></div>
			<?php
  else: 
  ?>
			<li><a href="#login" title="登录">登录</a></li>
			<li><a href="#reg" title="注册">注册</a></li>
			<?php endif;?>
		</ul>
	</div>
</nav>
<div class="container"> </div>
<div id="wa-content"> </div>
<div id="loading" class="loading">
	<div id="container"> <svg xmlns="http://www.w3.org/2000/svg"> 
		<!-- Created with Method Draw - http://github.com/duopixel/Method-Draw/ -->
		<g>
			<title>稍等一下，马上就好。</title>
			<ellipse ry="50%" rx="50%" id="svg_1" cy="50%" cx="50%" stroke-width="0" stroke="#fff" fill="#ccc"/>
		</g>
		</svg> <span>loading...</span> </div>
</div>
<div class="clearfix"></div>
<footer class="modal-footer clearfix">
	<div class="navbar-right">
		<p>By bd_bai</p>
		<p> <a href="http://bdbai.22web.org" title="布丁的博客" target="_blank">http://bdbai.22web.org</a></p>
	</div>
	<div class="navbar-left navbar-collapse"><a id="btn-top" class="btn btn-default navbar-btn">返回顶部</a>
		<p class="navbar-text navbar-right">小伙伴：<a href="http://uopera.net/" class="navbar-link" target="_blank">Opera Fans</a></p>
	</div>
</footer>
</body>
<?php
endif; //AppRunning
?>

<?php
require('../function.php');
if ($loginUser != null) :
?>
<div class="container">
  <div class="panel panel-danger">
    <div class="panel-heading"> 已登录 </div>
    <div class="panel-body">
      <p>您已登录，请继续浏览。</p>
      <p> <a class="btn btn-primary" href="#home">返回主页</a> </p>
    </div>
  </div>
  <?php
else:
?>
<div class="container">
  <div class="panel panel-primary">
    <div class="panel-heading">登录</div>
    <div class="panel-body">
      <form class="form-horizontal" role="form">
        <div class="form-group" id="login-email-group">
          <label for="email" class="col-sm-2 control-label">邮箱</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" id="email" placeholder="请输入邮箱" maxlength="32">
          </div>
        </div>
        <div class="form-group" id="login-pass-group">
          <label for="pass" class="col-sm-2 control-label">密码</label>
          <div class="col-sm-9">
            <input type="password" class="form-control" id="pass" placeholder="请输入密码">
          </div>
        </div>
        <div id="login-error-group" class="form-group">
          <label id="login-error-label" class="col-sm-11 col-sm-offset-2 control-label error-label"></label>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10">
            <button id="wa-login-btn" type="submit" class="btn btn-default">登录</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php
endif;
?>
</div>

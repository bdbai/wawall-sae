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
    <div class="panel-heading">注册</div>
    <div class="panel-body">
      <form class="form-horizontal" role="form">
        <div class="form-group" id="reg-email-group">
          <label for="email" class="col-sm-2 control-label">邮箱</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" id="email" placeholder="请输入邮箱" maxlength="32">
          </div>
        </div>
        <div class="form-group" id="reg-pass-group">
          <label for="pass" class="col-sm-2 control-label">密码</label>
          <div class="col-sm-9">
            <input type="password" class="form-control" id="pass" placeholder="请输入密码">
          </div>
        </div>
        <div class="form-group" id="reg-name-group">
          <label for="name" class="col-sm-2 control-label">昵称</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" id="name" placeholder="请输入昵称" maxlength="20">
          </div>
        </div>
        <div id="reg-error-group" class="form-group">
          <label id="reg-error-label" class="col-sm-11 col-sm-offset-2 control-label error-group"></label>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10">
            <button id="wa-reg-btn" type="submit" class="btn btn-default">注册</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php
endif;
?>
</div>

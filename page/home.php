<?php
require('../function.php');
?>

<div class="jumbotron" style="background-image: url(/page/image/wall.jpg);">
  <div class="container">
    <div class="row">
      <div class="col-md-7">
        <p>
        <h1 id="home-title"><?php echo $appName; ?></h1>
        </p>
        <span id="home-intro">
        <p>尝试全新的动态社交方式。</p>
        <p>无论是娱乐还是学习，您都将感受到一种全新的体验。</p>
        </span></div>
      <?php
		  if (!$loginUser) : 
		  ?>
      <div class="col-md-4 col-md-offset-1">
        <div class="login-mini-panel panel panel-success">
          <div class="panel-heading">输入邮箱开始．．．</div>
          <div class="panel-body">
            <form role="form" action="#">
              <div id="mini-email-group" class="form-group">
                <div class="controls">
                  <input id="email" type="text" placeholder="您的邮箱" class="form-control" maxlength="32">
                  <hr class="sm-line"/>
                  <button id="mini-submit-btn" class="btn btn-success btn-block" type="submit">确定</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <?php endif ?>
    </div>
  </div>
</div>
<div class="container">
  <div class="row">
    <div class="col-md-4">
      <h3>好玩</h3>
      <hr />
      <p>您一定从未见过如此新颖的动态呈现方式。幸运的是，只需轻轻一点，这些都不是幻想。</p>
    </div>
    <div class="col-md-4">
      <h3>新鲜</h3>
      <hr />
      <p>随时与朋友保持联络，永不落伍，并引领潮流。</p>
    </div>
    <div class="col-md-4">
      <h3>实用</h3>
      <hr />
      <p>动态墙？便签本？公告栏？挖掘您自己的用法！</p>
    </div>
  </div>
  <hr />
  <div class="row">
    <div class="col-sm-6">
      <div id="wall-count-panel">
        <h4>已有墙：</h4>
        <table class="counter">
          <tr>
            <?php 
			$wallcount = countWalls();
			foreach (str_split($wallcount, 1) as $n) : 
			?>
            <td><?php echo $n; ?></td>
            <?php endforeach; ?>
          </tr>
        </table>
      </div>
    </div>
    <div class="col-sm-6">
      <div id="user-count-panel">
        <h4>已有用户：</h4>
        <table class="counter">
          <tr>
            <?php 
			$wallcount = countUsers();
			foreach (str_split($wallcount, 1) as $n) : 
			?>
            <td><?php echo $n; ?></td>
            <?php endforeach; ?>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>

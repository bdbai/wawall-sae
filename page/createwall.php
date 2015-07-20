<div class="container">
  <?php
require('../function.php');
if ($loginUser == null) :
?>
  <div class="panel panel-danger">
    <div class="panel-heading"> 未登录 </div>
    <div class="panel-body">
      <p>请先登录哦。</p>
      <p> <a class="btn btn-primary" href="#login">登录</a> </p>
    </div>
  </div>
  <?php else: ?>
  <div class="panel panel-primary">
    <div class="panel-heading">创建新墙——需要30墙砖</div>
    <div class="panel-body">
      <form class="form-horizontal" role="form">
        <div class="form-group" id="createwall-name-group">
          <label for="name" class="col-sm-2 control-label">名称</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" id="name" placeholder="请输入名称" maxlength="30" />
          </div>
        </div>
        <div class="form-group" id="createwall-desc-group">
          <label for="desc" class="col-sm-2 control-label">介绍</label>
          <div class="col-sm-9">
            <textarea type="text" class="form-control" id="desc" placeholder="请输入简短的介绍，100字以内。" maxlength="100" rows="3"></textarea>
          </div>
        </div>
        <div class="form-group" id="createwall-walltype-group">
          <label for="walltype" class="col-sm-2 control-label">分类</label>
          <div class="col-sm-9">
            <select id="walltype" class="form-control">
              <?php
			$walltypes = WaWall::GetWalltypes();
			while (list($key, $value) = each($walltypes)) : 
			?>
              <option value="<?php echo $key; ?>">
              <?php echo $value; ?>
              </option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>
        <div id="createwall-error-group" class="form-group">
          <label id="createwall-error-label" class="col-sm-11 col-sm-offset-2 control-label"></label>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-1">
            <button id="wa-createwall-btn" type="submit" class="btn btn-primary">创建</button>
          </div>
          <div class="col-sm-offset-2">
            <button id="wa-createwall-returner" type="button" class="btn btn-default">返回</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>
</div>

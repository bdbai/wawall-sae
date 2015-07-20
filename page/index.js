// index file
/*
 * jQuery hashchange event - v1.3 - 7/21/2010
 * http://benalman.com/projects/jquery-hashchange-plugin/
 * 
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function ($, e, b) { var c = "hashchange", h = document, f, g = $.event.special, i = h.documentMode, d = "on" + c in e && (i === b || i > 7); function a(j) { j = j || location.href; return "#" + j.replace(/^[^#]*#?(.*)$/, "$1") } $.fn[c] = function (j) { return j ? this.bind(c, j) : this.trigger(c) }; $.fn[c].delay = 50; g[c] = $.extend(g[c], { setup: function () { if (d) { return false } $(f.start) }, teardown: function () { if (d) { return false } $(f.stop) } }); f = (function () { var j = {}, p, m = a(), k = function (q) { return q }, l = k, o = k; j.start = function () { p || n() }; j.stop = function () { p && clearTimeout(p); p = b }; function n() { var r = a(), q = o(m); if (r !== m) { l(m = r, q); $(e).trigger(c) } else { if (q !== m) { location.href = location.href.replace(/#.*/, "") + q } } p = setTimeout(n, $.fn[c].delay) } /msie/.test(navigator.userAgent.toLowerCase()) && !d && (function () { var q, r; j.start = function () { if (!q) { r = $.fn[c].src; r = r && r + a(); q = $('<iframe tabindex="-1" title="empty"/>').hide().one("load", function () { r || l(a()); n() }).attr("src", r || "javascript:0").insertAfter("body")[0].contentWindow; h.onpropertychange = function () { try { if (event.propertyName === "title") { q.document.title = h.title } } catch (s) { } } } }; j.stop = k; o = function () { return a(q.location.href) }; l = function (v, s) { var u = q.document, t = $.fn[c].domain; if (v !== s) { u.title = h.title; u.open(); t && u.write('<script>document.domain="' + t + '"<\/script>'); u.close(); q.location.hash = v } } })(); return j })() })(jQuery, this);

/*
 * Coded by bd_bai
 * http://bdbai.22web.org
 */
 //Event Hashchange Binding
 $(window).hashchange(hashRouter); 
 
 function hashRouter()
 {
	 var hash = location.hash;
	 if (hash == '' || hash == '#' || hash.indexOf('#home') == 0)
	 {
		 loadHome();
		 return;
	 }
	 if (hash.indexOf('#login') == 0)
	 {
		 loadLogin();
		 return;
	 }
	 if (hash.indexOf('#reg') == 0)
	 {
		 loadReg();
		 return;
	 }
	 if (hash.indexOf('#mywall') == 0)
	 {
		 loadMyWall();
		 return;
	 }
	 if (hash.indexOf('#logout') == 0)
	 {
		 if (!$('#canlogout')) return;
		 submitLogout();
		 return;
	 }
	 if (hash.indexOf('#inprogress') == 0)
	 {
		 loadInProgress();
		 return;
	 }
	 if (hash.indexOf('#wallhall') == 0)
	 {
		 var type = hash.replace('#wallhall-type-', '');
		 type = parseInt(type);
		 if (isNaN(type)) type = 0;
		 loadHall(type);
	 }
	 if (hash.indexOf('#hall') == 0)
	 {
		 location.hash = '#wallhall-type-0';
		 return;
	 }
	 if (hash.indexOf('#createwall') == 0)
	 {
		 loadCreateWall();
		 return;
	 }
 }
 var contentObj = null;
 var loadingObj = null;
 $(document).ready(function () {
    //Smooth Scrolling
    $('#btn-top').click(function () {
        $('html,body').animate({ scrollTop: 0 }, 500);
    });
	//about loading
	loadingObj = $('#loading');
	contentObj = $('#wa-content');
	loadingObj.hide();
	//btn binding
	$('#sign-btn').click(signIn);
    //Init. animation
    $(window).hashchange();
});
 function loading(callback)
 {
	 contentObj.fadeOut(200, function() {
		 loadingObj.fadeIn(200, callback);
	 });
 }
 function loaded(callback)
 {
	 loadingObj.fadeOut(200, function() {
		 contentObj.fadeIn(200, callback);
	 });
 }
 function loadPage(address, callback)
 {
	 loading(function() {
		 contentObj.load('/page/' + address, null, function() {
		 loaded(callback);
		 });
	 });
 }
 var lastPostTime = 0;
 function postData(address, data)
 {
	 var d = new Date();
	 var now = d.getTime();
	 if ((now - lastPostTime) <= (1000 * 1))
	 {
		 lastPostTime = now;
		 alert('您的动作十分迅速，请缓一缓再试试。');
		 return null;
	 }
	 lastPostTime = now;
	 url = '/api/' + address;
	 return $.post(url, data);
 }
 function getData(address, data)
 {
	 return $.get('/api/' + address, data);
 }
 function signIn()
 {
	 post = postData('signin.php');
	 if (!post)
	 {
		 alert('请求出错，请稍后再试。');
		 return;
	 }
	 post
	 .success(function(data) 
	 {
		 result = eval('(' + data + ')');
		 if (result && result.state == 'success')
		 {
			 signbtnObj = $('#sign-btn');
			 signbtnObj.attr('disabled', 'disabled');
			 signbtnObj.text('已签到' + result.signdays + '天');
			 $('#exp').text(result.exp);
			 $('#wealth').text(result.wealth);
		 }
		 else
		 {
			 alert('签到出错，请稍后再试。');
		 }
	 })
	 .error(function()
	 {
		 alert('请求出错，请稍后再试。');
	 })
 }
 function loadHome()
 {
	 loadPage('home.php', function()
	 {
		 $('#mini-submit-btn').click(submitMini);
	 });
 }
 var miniEmail = '';
 function submitMini(e)
 {
	 e.preventDefault();
	 var err = false;
	 var emailObj = $('#email');
	 emailReg = /^[-_A-Za-z0-9]+@([_A-Za-z0-9]+\.)+[A-Za-z0-9]{2,3}$/;
	 if (emailObj.val().length > 32 || emailObj.val().length < 1 || !emailReg.test(emailObj.val()))
	 {
		 $('#mini-email-group').addClass('has-error');
		 err = true;
	 }
	 else
	 {
		 $('#mini-email-group').removeClass('has-error');
		 miniEmail = emailObj.val();
	 }
	 
	 if (!err)
	 {
		 loading(function()
		 {
			 var post = postData('mini.php', { email : emailObj.val() });
			 if (post)
			 {
				 post
				 .success(loadMini)
				 .error( function() 
				 {
					 alert('请求出错。请稍后再试。');
				 });
			 }
			 else
			 {
				 alert('请求出错。请稍后再试。');
			 }
		 });
	 }
 }
 function loadMini(data)
 {
	 result = eval('(' + data + ')');
	 if (!result) 
	 {
		 alert('请求出错，请稍后再试。');
	 }
	 lastPostTime = 0;
	 if (result.next == 'login')
	 {
		 loadLogin(function()
		 {
			 $('#email').val(miniEmail);
		 });
		 return;
	 }
	 if (result.next == 'reg')
	 {
		 loadReg(function() 
		 {
			 $('#email').val(miniEmail);
		 });
		 return;
	 }
	 alert('请求出错，请稍后再试。');
 }
 function loadLogin(callback)
 {
	 loadPage('login.php', function()
	 {
		 $('#wa-login-btn').click(submitLogin);
		 if (typeof(callback) != 'undefined') callback();
	 });
 }
 function loadReg(callback)
 {
	 loadPage('reg.php', function()
	 {
		 $('#wa-reg-btn').click(submitReg);
		 if (typeof(callback) != 'undefined') callback();
	 });
 }
 function loadCreateWall(callback)
 {
	 loadPage('createwall.php', function()
	 {
		 $('#wa-createwall-btn').click(submitCreateWall);
		 $('#wa-createwall-returner').click(function(e)
		 {
			 e.preventDefault();
			 history.go(-1);
		 });
		 if (typeof(callback) != 'undefined') callback();
	 });
 }
 function loadInProgress()
 {
	 loadPage('inprogress.php');
 }
 function submitLogin(e)
 {
	 e.preventDefault();
	 var err = false;
	 var emailObj = $('#email');
	 var passObj = $('#pass');
	 emailReg = /^[-_A-Za-z0-9]+@([_A-Za-z0-9]+\.)+[A-Za-z0-9]{2,3}$/;
	 if (emailObj.val().length > 32 || emailObj.val().length < 1 || !emailReg.test(emailObj.val()))
	 {
		 $('#login-email-group').addClass('has-error');
		 err = true;
	 }
	 else
	 {
		 $('#login-email-group').removeClass('has-error');
	 }
	 
	 if (passObj.val().length < 6)
	 {
		 $('#login-pass-group').addClass('has-error');
		 err = true;
	 }
	 else
	 {
		 $('#login-pass-group').removeClass('has-error');
	 }
	 
	 if (!err)
	 {
		 var post = postData('login.php', { email: emailObj.val(), pass: passObj.val() });
		 if (post)
		 {
			 post
			 .success(function(data) { 
			 			 var ret = eval('(' + data + ')');
						 if (typeof(ret.error) == 'undefined') 
						 {
							 location.hash = '#home';
							 location.reload();
							 return;
						 }
						 $('#login-error-label').text(ret.error);
						 $('#login-error-group').addClass('has-error');
						 })
			 .error(function() { 
						 var errorLabelObj = $('#login-error-label');
						 errorLabelObj.text(ret.error);
						 errorLabelObj.addClass('has-error');
						 });
		 }
	 }
 }
 function submitReg(e)
 {
	 e.preventDefault();
	 var err = false;
	 var emailObj = $('#email');
	 var passObj = $('#pass');
	 var nameObj = $('#name');
	 emailReg = /^[-_A-Za-z0-9]+@([_A-Za-z0-9]+\.)+[A-Za-z0-9]{2,3}$/;
	 if (emailObj.val().length > 32 || emailObj.val().length < 1 || !emailReg.test(emailObj.val()))
	 {
		 $('#reg-email-group').addClass('has-error');
		 err = true;
	 }
	 else
	 {
		 $('#reg-email-group').removeClass('has-error');
	 }
	 
	 if (passObj.val().length < 6)
	 {
		 $('#reg-pass-group').addClass('has-error');
		 err = true;
	 }
	 else
	 {
		 $('#reg-pass-group').removeClass('has-error');
	 }
	 if (nameObj.val().length < 1)
	 {
		 $('#reg-name-group').addClass('has-error');
		 err = true;
	 }
	 else
	 {
		 $('#reg-name-group').removeClass('has-error');
	 }
	 
	 if (!err)
	 {
		 var post = postData('reg.php', { email: emailObj.val(), pass: passObj.val(), name: nameObj.val() });
		 if (post)
		 {
			 post
			 .success(function(data) { 
			 			 var ret = eval('(' + data + ')');
						 if (typeof(ret.error) == 'undefined') 
						 {
							 location.hash = '#home';
							 location.reload();
							 return;
						 }
						 $('#reg-error-label').text(ret.error);
						 $('#reg-error-group').addClass('has-error');
						 })
			 .error(function() { 
						 var errorLabelObj = $('#reg-error-label');
						 errorLabelObj.text(ret.error);
						 errorLabelObj.addClass('has-error');
						 });
		 }
	 }
 }
 function submitLogout()
 {
	 var post = postData('logout.php');
	 if (post)
	 {
		 post
		 .success(function()
		 {
			 location.href = '#home';
			 location.reload();
		 })
		 .error(function(data)
		 {
			 alert(data);
		 });
	 }
 }
 var highlightWalltype = 0;
 function loadHall(type)
 {
	 highlightWalltype = type;
	 /*var walltypeListObj = $('#walltype-list');
	 if (!walltypeListObj)
	 {
		 
	 }
	 else
	 {*/
		 loading(function() {
			 contentObj.html('<div class="container"><div class="row"><div class="col-sm-8 col-sm-push-4"><p><a href="#createwall" class="btn btn-primary">创建新墙</a></p><div id="wall-list" class="list-group"></div><div id="hall-loading" class="loading"><div id="container"><svg xmlns="http://www.w3.org/2000/svg"><!-- Created with Method Draw - http://github.com/duopixel/Method-Draw/ --><g><title>稍等一下，马上就好。</title><ellipse ry="50%" rx="50%" id="svg_1" cy="50%" cx="50%" stroke-width="0" stroke="#fff" fill="#ccc"/></g></svg> <span>loading...</span> </div></div></div><div id="walltype-list" class="col-sm-4 col-sm-pull-8"><div class="list-group"> <span class="list-group-item active"><h2 id="walltype-title" class="list-group-item-heading">墙分类</h2></span></div></div></div></div>');
			 loadWalltype(function() {
				 loaded(function() {
					 loadWall();
				 });
			 });
		 });
	 //}
 }
 function loadWalltype(callback)
 {
	 var get = getData('getwalltypes.php');
	 if (!get) return;
	 get
	 .success(function(data) 
	 {
		 var result = eval('(' + data + ')');
		 if (!result)
		 {
			 alert(data);
		 }
		 if (result.error)
		 {
			 alert(result.error);
		 }
		 listObj = $('#walltype-list');
		 for (var i = 0; i < result.length; i++)
		 {
			 var wall = result[i];
			 listObj.append('<a class="list-group-item' + 
			 (wall.walltype_id == highlightWalltype ? ' active' : '') +
			 '" href="#wallhall-type-' + 
			 wall.walltype_id + 
			 '"> <span class="list-group-item-text">' + 
			 wall.walltype_name + 
			 '</span></a>');
		 }
		 if (typeof(callback) != 'undefined')
		 {
			 callback();
		 }
	 })
	 /*.error(function(data)
	 {
	 	if (typeof(callback) != 'undefined')
		 {
			 callback();
		 }
	  })*/;
 }
 function getMyWall(callback, fullData)
 {
	 var d = fullData ? ({ fulldata : 1 }) : '';
	 var get = getData('getmywall.php', d);
	 if (!get) return null;
	 get
	 .success(function(data) {
		 if (typeof(callback) != 'undefined')
		 {
			 var ret = eval('(' + data + ')');
			 if (typeof(ret.error) != 'undefined')
			 {
				 ret = null;
			 }
			 callback(ret);
		 }
	 })
	 .error(function()
	 {
	 	location.hash='#home';
	 });
 }
 var fetchedWall = 0;
 function loadWall()
 {
	 getMyWall(function (mywalls)
	 {
	 var get = getData('gethallwall.php', { walltype : highlightWalltype, limit : 20 } );
	 if (!get) return;
	 get
	 .success(function(data) 
	 {
		 var result = eval('(' + data + ')');
		 if (!result)
		 {
			 alert(data);
		 }
		 if (result.error)
		 {
			 alert(result.error);
		 }
		 fetchedWall = 0;
		 listObj = $('#wall-list');
		 var i = 0;
		 for (; i < result.length && i < 20; i++)
		 {
			 var wall = result[i];
			 var relationship = '';
			 for (mw in mywalls)
			 {
				 if (mywalls[mw].wall_id == wall.wall_id)
				 {
					 relationship = mywalls[mw].relationship;
					 break;
				 }
			 }
			 appendWall(wall, listObj, relationship);
		 }
		 fetchedWall = i ? i - 1 : 0;
		 if (i == 20)
		 {
			 listObj.append('<div id="wall-more" class="wall"> 再显示20个 </div>');
			 $('#wall-more').click(function() 
			 {
				 $('#hall-loading').fadeIn(200, getWallAppender(highlightWalltype, result[19]));
			 });
		 }
		 $('#hall-loading').fadeOut(200, function()
		 {
			if (typeof(callback) != 'undefined')
			{
				 callback();
			}
		 });
	 });
	 });
	 /*.error(function(data)
	 {
	 	if (typeof(callback) != 'undefined')
		 {
			 callback();
		 }
	  })*/;
 }
 function getWallAppender(_walltype, _lastItem)
 {
	 var walltype = _walltype;
	 var lastItem = _lastItem;
	 return function(e)
	 {
		 $('#wall-more').hide();
	 getMyWall(function (mywalls)
	 {
	 var get = getData('gethallwall.php', { walltype : highlightWalltype, limit : fetchedWall += 20 } );
	 if (!get) return;
	 get
	 .success(function(data) 
	 {
		 	 var get = getData('gethallwall.php', { walltype : highlightWalltype, limit : fetchedWall } );
	 if (!get) return;
	 get
	 .success(function(data) 
	 {
		 var result = eval('(' + data + ')');
		 if (!result)
		 {
			 alert(data);
		 }
		 if (result.error)
		 {
			 alert(result.error);
		 }
		 var wall = false;
		 while (wall = result.shift())
		 {
			 if (wall.wall_id == lastItem.wall_id)
			 {
				 break;
			 }
		 }
		 listObj = $('#wall-list');
		 var i = 0;
		 for (; i < result.length && i < 20; i++)
		 {
			 var wall = result[i];
			 var relationship = '';
			 for (mw in mywalls)
			 {
				 if (mywalls[mw].wall_id == wall.wall_id)
				 {
					 relationship = mywalls[mw].relationship;
					 break;
				 }
			 }
			 appendWall(wall, listObj, relationship);
		 }
		 if (i > 20)
		 {
			 $('#wall-more').show();
		 }
		 $('#hall-loading').fadeOut(200, function()
		 {
			if (typeof(callback) != 'undefined')
			{
				 callback();
			}
		 });
	 });
	 });
	 });
	 }
 }
 function appendWall(wall, parentObj, relationship)
 {
	 var addBtn = '';
	 switch(relationship)
	 {
		 case 'like':
		 addBtn = '<div id="dislike-btn-'+wall.wall_id+'" class="btn btn-warning">取消关注</div>';
		 break;
		 case 'own':
		 addBtn = '<div class="btn btn-default">进入</div>';
		 break;
		 default:
		 addBtn = '<div id="like-btn-'+wall.wall_id+'" class="btn btn-default">关注</div>';
		 break;
	 }
	 parentObj.append('<a href="/wall/' + 
	 wall.wall_id +
	 '"><div id="wall" class="wall"><h3 class="wall-title">' + 
			 wall.wall_name + 
			 '</h3>' + 
			 '<h6 class="wall-info"><small>' + 
			 (wall.wall_creatorname ? 
			 '由' + wall.wall_creatorname + '创建，' : 
			 '') + 
			 wall.wall_usercount + '参与' + 
			 '</small></h6><hr /><p class="wall-desc">' + 
			 wall.wall_desc +
			 '</p>'+addBtn+'</div></a>'
		  );
	switch(relationship)
	 {
		 case 'like':
		 addBtnObj = $('#dislike-btn-'+wall.wall_id);
		 addBtnObj.click(getWallUnlikeBtnClick(wall.wall_id, addBtnObj));
		 break;
		 case 'own':
		 addBtn = '<div class="btn btn-default">进入</div>';
		 break;
		 default:
		 addBtnObj = $('#like-btn-'+wall.wall_id);
		 addBtnObj.click(getWallLikeBtnClick(wall.wall_id, addBtnObj));
		 break;
	 }
 }
 function submitCreateWall(e)
 {
	 e.preventDefault();
	 var submitObj = $('#wa-createwall-btn');
	 submitObj.attr('disabled', 'disabled');
	 err = false;
	 var nameObj = $('#name');
	 var descObj = $('#desc');
	 var walltypeObj = $('#walltype option:selected');
	 if (nameObj.val().length < 1)
	 {
		 $('#createwall-name-group').addClass('has-error');
		 err = true;
	 }
	 else
	 {
		 $('#reg-name-group').removeClass('has-error');
	 };
	 if (descObj.val().length < 1)
	 {
		 $('#createwall-desc-group').addClass('has-error');
		 err = true;
	 }
	 else
	 {
		 $('#createwall-desc-group').removeClass('has-error');
	 }
	 if (!walltypeObj || walltypeObj.val() < 1 || walltypeObj.val() > 35)
	 {
		 $('#createwall-walltype-group').addClass('has-error');
	 }
	 else
	 {
		 $('#createwall-walltype-group').removeClass('has-error');
	 }
	 
	 if (!err)
	 {
		 var post = postData('createwall.php', { name : nameObj.val(), desc: descObj.val(), walltype : walltypeObj.val() });
		 if (post)
		 {
			 post
			 .success(function(data) { 
			 			 var ret = eval('(' + data + ')');
						 if (!ret.error) 
						 {
							 alert('创建成功。立即进入新墙！');
							 //location.hash = '#wallhall-type-' + walltypeObj.val().toString();
							 history.go(-1);
							 /*$(document).append('<a id="simtrigger" href="/wall/' + nameObj.val() + '" target="_blank" style="display: none;"></a>');
							 $('#simtrigger').trigger(click);*/
							 window.open('/wall/' + ret.wall_id);
							 submitObj.removeAttr('disabled');
							 return;
						 }
						 $('#createwall-error-label').text(ret.error);
						 $('#createwall-error-group').addClass('has-error');
						 })
			 .error(function() { 
						 var errorLabelObj = $('#createwall-error-label');
						 errorLabelObj.text(ret.error);
						 errorLabelObj.addClass('has-error');
						 submitObj.removeAttr('disabled');
						 });
		 }
	 }
	 submitObj.attr('disabled', 'disabled');
 }
 function getWallLikeBtnClick(_wall, _obj)
 {
	 var wall = _wall;
	 var obj = _obj;
	 return function(e)
	 {
		 e.preventDefault();
		 obj.attr('disabled', 'disabled');
		 var post = postData('likewall.php', { wall_id : wall });
		 if (!post) 
		 {
		     obj.removeAttr('disabled');
			 return;
		 }
		 post
		 .error(function(data) {
			 alert(data);
		     obj.removeAttr('disabled');
			 return;
		 })
		 .success(function(data) {
		     obj.removeAttr('disabled');
			 var result = eval('(' + data + ')');
			 if (typeof(result.error) != 'undefined')
			 {
				 alert('关注失败。');
				 return;
			 }
			 obj.text('取消关注');
			 obj.removeClass('btn-default');
			 obj.addClass('btn-warning');
			 obj.unbind('click');
			 obj.click(getWallUnlikeBtnClick(wall, obj));
		 });
	 };
 }
 function getWallUnlikeBtnClick(_wall, _obj)
 {
	 var wall = _wall;
	 var obj = _obj;
	 return function(e)
	 {
		 e.preventDefault();
		 obj.attr('disabled', 'disabled');
		 var post = postData('unlikewall.php', { wall_id : wall });
		 if (!post)
		 {
		     obj.removeAttr('disabled');
			 return;
		 }
		 post
		 .error(function(data) {
		     obj.removeAttr('disabled');
			 alert(data);
			 return;
		 })
		 .success(function(data) {
		     obj.removeAttr('disabled');
			 var result = eval('(' + data + ')');
			 if (typeof(result.error) != 'undefined')
			 {
				 alert('取消关注失败。');
				 return;
			 }
			 obj.text('关注');
			 obj.removeClass('btn-warning');
			 obj.addClass('btn-default');
			 obj.unbind('click');
			 obj.click(getWallLikeBtnClick(wall, obj));
		 });
	 };
 }
 function loadMyWall()
 {
	 loading(function()
	 {
		 
	 contentObj.html('<div id="mywall-container"></div>');
	 var myWallContainerObj = $('#mywall-container');
	 myWallContainerObj.hide();
	 getMyWall(function(walls)
	 {
		 if (typeof(walls) == 'undefined' || !walls)
		 {
			 alert('暂无。');
			 location.hash = '#home';
			 return;
		 }
		 for(var i = 0;i < walls.length;i++)
		 {
			 appendWall(walls[i], myWallContainerObj, walls[i].relationship);
		 }
		 loaded(function() 
		 {
			 myWallContainerObj.fadeIn(100);
		 });
	 }, true);
	 });
 }
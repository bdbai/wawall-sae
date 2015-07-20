// JavaScript Document
 var lastPostTime = 0;
 var postContainerObj;
 var zoomedIn = false;
 $(document).ready(function(e)
 {
	 $('#sign-btn').click(signIn);
	 $('#wall-nav-prev').click(prev);
	 $('#wall-nav-next').click(next);
	 $('#wall-zoom-in').click(function() {
		 getPostClick(nowPostObj)();
	 });
	 $('#wall-zoom-out').click(zoomOut);
	 $('#post-detail').hide();
	 $('#add-post-panel').hide();
	 $('#add-post-btn').click(function()
	 {
		 $('#add-post-panel').slideToggle(500);
	 });
	 $('#submit-add-post-btn').click(submitPost);
	 postContainerObj = $('#post-container');
 });
 function postData(address, data, seqCheck)
 {
	 var d = new Date();
	 var now = d.getTime();
	 if ((now - lastPostTime) <= (1000 * 1) && seqCheck)
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
 var nowPostObj;
 function fadePost(obj)
 {
	 if (typeof(obj) == 'object') {
		 	obj.css('opacity', '0.3');
			obj.css('z-index', '40');
	 }
 }
 function highlightPost(obj)
 {
	 if (typeof(obj) == 'object') {
		 	obj.css('opacity', '0.9');
			obj.css('z-index', '50');
	 }
 }
//http://jsfiddle.net/kixi/ngHD9/
function postScroll(e)
{
    e = e || window.event;
	var goNext;
    if (e.wheelDelta) { 
        //如果是IE/Opera/Chrome浏览器
        //e.wheelDelta;
		goNext = e.wheelDelta < 0;
    } else if (e.detail) { 
        //如果是Firefox浏览器
        //t2.value = e.detail;
		goNext = e.detail < 0;
	}
	var readyPostObj = goNext ? nowPostObj.nextObj : nowPostObj.prevObj;
	if (typeof(readyPostObj) != 'undefined' && readyPostObj)
	{
		fadePost(nowPostObj);
		highlightPost(readyPostObj);
/*	nowPostObj.css('opacity', '0.3');
	readyPostObj.css('opacity', '0.9');
	nowPostObj.css('z-index', '40');
	readyPostObj.css('z-index', '50');*/
	nowPostObj = readyPostObj;
	if (zoomedIn)
	{
		zoomOut(function() 
		{
			zoomIn();
		});
	}
	}
}
function prev()
{
	postScroll({ wheelDelta : 120 });
}
function next()
{
	postScroll({ wheelDelta : -120 });
}
/*
if (kixi.addEventListener) {
    //非firefox
    kixi.addEventListener('mousewheel', scrollFunc, false);
    
    //firefox
    kixi.addEventListener('DOMMouseScroll', scrollFunc, false);
} */
	 var lastObj;
	 var lastBut1Obj;
	 var lastPostId = null;
 function processPosts(posts, limit)
 {
	 if (typeof(limit) == 'undefined')
	 {
		 limit = posts.length;
	 }
	 fadePost(nowPostObj);
	 var moreObj = $('#post-more');
	 if (typeof(moreObj) != 'undefined' && moreObj) {
		 moreObj.remove();
	 }
	 width = postContainerObj.width();
	 height = postContainerObj.height();
	 var firstObj = null;
	 for (var i = Math.min(limit, posts.length) - 1; i >= 0; i--)
	 {
		 lastPostId = posts[i].post_id;
		 postContainerObj.append(
		 '<div id="post-' + 
		 posts[i].post_id + 
		 '" class="wall-post"><h3>' + 
		 posts[i].post_title + 
		 '</h3><h5 class="wall-post-creator">' + 
		 posts[i].post_creator + 
		 '</h5><hr class="cf" /><span class="wall-post-content">' + 
		 posts[i].post_content + 
		 '</span></div>'
		 );
		 var currObj = $('#post-' + posts[i].post_id);
		 currObj.postId = posts[i].post_id;
		 if (!firstObj) firstObj = currObj;
		 currObj.css('left', Math.random() * (width - 175));
		 currObj.css('top', Math.random() * (height - 175));
		 fadePost(currObj);
		 currObj.isMorePost = false;
		 currObj.click(getPostClick(currObj));
		 if (typeof(lastObj) != 'undefined' && lastObj) {
			 lastObj.prevObj = currObj;
			 lastObj.nextObj = lastBut1Obj;
		 }
		 lastBut1Obj = lastObj;
		 lastObj = currObj;
	 }
	 if (posts.length >= 10) {
		 moreObj = $('<div id="post-more" class="wall-post">再显示10个．．．</div>');
		 fadePost(moreObj);
		 moreObj.click(loadMorePost);
		 moreObj.isMorePost = true;
		 postContainerObj.append(moreObj);
		 if (lastObj) {
			 lastObj.prevObj = moreObj;
			 moreObj.nextObj = lastObj;
		 }
	 }
	 if (typeof(lastObj) != 'undefined' && lastObj)
	 {
		 if (typeof(lastBut1Obj) != 'undefined' && lastBut1Obj)
		 {
			 lastObj.nextObj = lastBut1Obj;
		 }
		 nowPostObj = firstObj;
		 highlightPost(firstObj);
	 }
 }
 function getPostClick(_obj)
 {
	 var obj = _obj;
	 return function(e)
	 {
		 fadePost(nowPostObj);
		 // nowPostObj.css('opacity', '0.3');
		 highlightPost(obj);
			 //obj.css('opacity', '0.9');
			 nowPostObj = obj;
			 zoomIn();
	 }
 }
 function zoomIn(callback)
 {
	 if (zoomedIn || typeof(nowPostObj) == 'undefined' || !nowPostObj) return;
	 if (nowPostObj.isMorePost) return;
	 zoomedIn = true;
	 /* postContainerObj.append(
	 '<div id="post-detail" class="wall-post">' + 
	 nowPostObj.html() + 
	 '</div>');*/
	 var detailObj = $('#post-detail');
	 detailObj.hide();
	 var commentObj = $('<div class="comment-panel"></div>');
	 detailObj.html('<div class="post-panel">' + nowPostObj.html() + '</div>');
	 detailObj.append(commentObj);
	 loadComments(nowPostObj.postId, function(comment) {
		 
	 });
	 detailObj.fadeIn(200, callback);
	 detailObj.dblclick(zoomOut);
 }
 function zoomOut(callback)
	 {
	 var detailObj = $('#post-detail');
		 detailObj.fadeOut(300, function() 
		 {
	 zoomedIn = false;
			 if (typeof(callback) == 'function')
			 {
				 callback();
			 }
		 });
	 }
 var wallId;
 function init(wall_id)
 {
	 wallId = wall_id;
	 var loadingObj = $('#load-wrap');
	 var get = getData('heartbeat.php', { wall : wall_id, limit : 10 }, false);
	 if (!get) 
	 {
		 alert('加载失败。');
	 }
	 get
	 .success(function(data)
	 {
		 var result = eval('(' + data + ')');
		 if (typeof(result.error) != 'undefined')
		 {
			 alert(result.error);
loadingObj.fadeOut(500);
			 return;
		 }
		 //postContainerObj.html('');
		 var resultPosts = result.post;
		 processPosts(resultPosts);
		 var pcoObj = postContainerObj[0];
			 if (pcoObj.addEventListener) {
    //非firefox
    pcoObj.addEventListener('mousewheel', postScroll, false);
    
    //firefox
    pcoObj.addEventListener('DOMMouseScroll', postScroll, false);
}
loadingObj.fadeOut(500);
	 })
	 .error(function()
	 {
		 alert('请求出错。');
	 });
 }
 
 function submitPost(e)
 {
	 e.preventDefault();
	 $('#submit-add-post-btn').attr('disabled', 'disabled');
	 var err = false;
	 var titleObj = $('#title');
	 var contentObj = $('#content');
	 
	 if (titleObj.val().length == 0)
	 {
		 $('#add-post-title-group').addClass('has-error');
		 err = true;
	 }
	 else
	 {
		 $('#add-post-title-group').removeClass('has-error');
	 }
	 
	 	 if (contentObj.html().length == 0)
	 {
		 $('#add-post-content-group').addClass('has-error');
		 err = true;
	 }
	 else
	 {
		 $('#add-post-content-group').removeClass('has-error');
	 }
	 
	 if (contentObj.html() > 1000)
	 {
		 alert('内容过多了。');
		 err = true;
	 }
	 
	 if (err)
	 {
		 $('#submit-add-post-btn').removeAttr('disabled');
		 return;
	 }
	 var post = postData('createpost.php', { wall : wallId, title : titleObj.val(), content : contentObj.html() });
	 if (!post) return;
	 post
	 .success(function(data)
	 {
		 var result = eval('(' + data + ')');
		 if (typeof(result.error) != 'undefined')
		 {
			 $('#add-post-error-label').text(result.error);
			 $('#add-post-error-group').addClass('has-error');
		 $('#submit-add-post-btn').removeAttr('disabled');
			 return;
		 }
		 //location.reload();
		 //return;
		 processPosts([ { "post_id" : result.post_id, "post_title" : titleObj.val(), "post_creator" : "我", "post_content" : contentObj.html() } ], 1);
		 titleObj.val('');
		 contentObj.html('');
		 $('#submit-add-post-btn').removeAttr('disabled');
		 $('#add-post-error-label').text('');
	 })
	 .error(function() 
	 {
		 $('#submit-add-post-btn').removeAttr('disabled');
		 alert('发布失败。');
	 });
 }
 function loadMorePost()
 {
	 var moreObj = $('#post-more');
	 if (typeof(moreObj) != 'undefined' && moreObj) {
		 moreObj.remove();
	 }
	 var get = getData('heartbeat.php', { wall : wallId, limit : 10, lastpost : lastPostId }, false);
	 if (!get) 
	 {
		 alert('加载失败。');
	 }
	 get
	 .success(function(data)
	 {
		 var result = eval('(' + data + ')');
		 if (typeof(result.error) != 'undefined')
		 {
			 alert(result.error);
loadingObj.fadeOut(500);
			 return;
		 }
		 var resultPosts = result.post;
		 processPosts(resultPosts);
	 })
	 .error(function()
	 {
		 alert('请求出错。');
	 });
 }
 function heartBeat(callback)
 {
	 
 }
 function loadComments(post, callback) {
	 var get = getData('getcomment.php', { post : post })
	 .success(function(data) {
		 var data = eval('(' + data + ')');
		 if (typeof(data.error) != 'undefined') {
			 alert(data.error);
			 return null;
		 }
		 if (typeof(callback) == 'function') {
			 callback(data.comment);
		 }
	 })
	 .error(function() {
		 alert('获取评论失败。');
	 });
 }
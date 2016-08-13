<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
  <title>页面提示</title>
  
  <style type="text/css">
   *{margin:0px;padding:0px;font-size:12px;font-family:Arial,Verdana;}
   #wrapper{width:450px;height:200px;background:#F5F5F5;position:absolute;top:40%;left:50%;margin-top:-100px;margin-left:-225px;background: url('__PUBLIC__/images/1.jpg');}
   p.msg-title{width:100%;height:30px;line-height:30px;text-align:center;color:#EE7A38;margin-top:40px;font:14px Arial,Verdana;font-weight:bold;}
   p.message{width:100%;height:40px;line-height:40px;text-align:center;color:blue;margin-top:5px;margin-bottom:5px;}
   p.error{width:100%;height:40px;line-height:40px;text-align:center;color:red;margin-top:5px;margin-bottom:5px;}
   p.notice{width:100%;height:25px;line-height:25px;text-align:center;}
  </style>
 </head>

 <body>
  <div id="wrapper">
   <p class="msg-title" style="font-size:20px;">{$msgTitle}</p>
   <present name="message">
    <p class="message" style="font-size:20px;">{$message}</p>
   </present>
   <present name="error">
    <p class="error" style="font-size:20px;">{$error}</p>
   </present>
   <present name="closeWin">
    <p class="notice"><b>系统将在</b> <span style="color:blue;font-weight:bold"><b id="wait"><?php echo($waitSecond); ?></b></span> 秒后自动关闭，如果不想等待,直接点击 <a id="href" href="<?php echo($jumpUrl); ?>">这里</a> 关闭</p>
   </present>
   <notpresent name="closeWin">
    <p class="notice"><b>系统将在</b> <span style="color:blue;font-weight:bold"><b id="wait"><?php echo($waitSecond); ?></b></span> 秒后自动跳转，如果不想等待,直接点击 <a id="href" href="<?php echo($jumpUrl); ?>">这里</a> 关闭</p>
   </notpresent>
  </div>
 </body>
</html>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
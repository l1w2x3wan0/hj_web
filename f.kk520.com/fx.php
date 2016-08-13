<!--http://192.168.1.252:3000/hj_aaa_web/hj_web-->
<!DOCTYPE html>
<html style="height: auto; overflow: auto;">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>皇家AAA</title>
<meta name="MobileOptimized" content="240">
<meta name="apple-touch-fullscreen" content="YES">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=device-width, initial-scale=1.0,  minimum-scale=1.0, maximum-scale=1.0" />
	<link type="text/css" href="css/style.css"rel="stylesheet">
	 <script>
	    function is_weixin(){
			var ua = navigator.userAgent.toLowerCase();
			if(ua.match(/MicroMessenger/i)=="micromessenger") {
				return true;
			} else {
				return false;
			}
          }

		window.onload = function() {
			var tip = document.getElementById('weixin-tip');
			var content = document.getElementById('content');
			if (is_weixin()) {
					tip.style.display = 'block';
					content.style.display = 'none';
			}else{
			tip.style.display = 'none';
			}
		}
	</script>
</head>
<body>
<div class="container">
	<div class="line-middle" id="weixin-tip" style="width: 96%; display: block;margin-right:-5px;text-align:right;position:relative;">
		  <div class="x2"></div>
		  <div class="x10" style="width: 83%;position: relative;float: right;"><img src="images/top5.png" class="img-responsive"></div>
		</div>
	<div class="wrap">
		<div class="header">
			<div class="top">
				<div class="logo">
					<img src="images/logo1.png"/>
				</div>
			</div>
		</div>
		<div class="content">
		<div class="slideshow">
				<div class="main-banner-wrapper"
					style="width: 100%; float:left;height:161px;">
					<div class="b-item">
					</div>
				</div>
			</div>
			<div class="clearBoth"></div>
			<div class="appWrapper"  id="content">
				<div class="appIcon">
					<img width="72px" src="images/appIcon.png" />
				</div>
				<div class="appInfo">
					<div class="appName">皇家AAA</div>
					<div class="appVersion">安卓最新版</div>
				</div>
				<div class="thanks">
					<?php
					$uid = !empty($_GET['uid']) ? $_GET['uid'] : "10000000";
					$code = ($uid * 6 + 20160105) / 3 - 168168 * 9;
					?>
					<div class="icode"><?php echo $code;?></div>
				</div>
				<div class="appDownload">
				<a href="http://down.kk520.com/anzhuo/hj_aaa10000.apk"><img src="http://f.kk520.com/images/download.png"></a>
				</div>
			</div>
		<div class="clearBoth"></div>
		</div>
	</div>
</div>
</body>
</html>

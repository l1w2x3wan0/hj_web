<?php
$act = !empty($_GET['act']) ? $_GET['act'] : "";
if ($act == "post01"){

	$ip = $_GET['ip'];
	$para = array();
	$para['order'] = $_GET['order'];
	$para['money'] = (int)$_GET['money'];
	$para['payment'] = (int)$_GET['payment'];
	$para['goodsId'] = (int)$_GET['goodsId'];
	$para['channel'] = (int)$_GET['channel'];
	$para['status'] = $_GET['status'];
	$para['userId'] = $_GET['userId'];
	$para['userData'] = "";
	$para['payment_account'] = "";
	$para['msg'] = "";
	echo json_encode($para)."||";
	$result = curlPOST2($ip, json_encode($para));
	echo $result; 
	exit;
}else if ($act == "post02"){

	$ip = $_GET['ip'];
	$para = array();
	$para['type'] = 1;
	$para['userId'] = (int)$_GET['user_id'];
	$para['goldnum'] = (int)$_GET['gold'];
	$para['diamond'] = (int)$_GET['diamond'];
	$para['deposit'] = (int)$_GET['deposit'];
	echo json_encode($para)."||";
	$result = curlPOST2($_GET['ip'], json_encode($para));
	echo $result; 
	exit;
}else if ($act == "post03"){

	$ip = $_GET['ip'];
	$para = array();
	$para['type'] = 10;
	$para['userId'] = (int)$_GET['user_id'];
	$para['viplevel'] = (int)$_GET['viplevel'];
	$para['vippoint'] = (int)$_GET['czz'];
	$para['lottery_id'] = (int)$_GET['zeng'];
	$para['nums'] = (int)$_GET['num3'];
	echo json_encode($para)."||";
	$result = curlPOST2($_GET['ip'], json_encode($para));
	echo $result; 
	exit;
}else if ($act == "post04"){

	$ip = $_GET['ip'];
	$para = array();
	$para['type'] = 9;
	$para['userId'] = (int)$_GET['user_id'];
	echo json_encode($para)."||";
	$result = curlPOST2($_GET['ip'], json_encode($para));
	echo $result; 
	exit;
}else if ($act == "post05"){

	$ip = $_GET['ip'];
	$para = array();
	$para['type'] = 3;
	$para['userId'] = (int)$_GET['user_id'];
	$para['pic'] = "tx/weigui";
	echo json_encode($para)."||";
	$result = curlPOST2($_GET['ip'], json_encode($para));
	echo $result; 
	exit;
}else if ($act == "post06"){

	$ip = $_GET['ip'];
	$para = array();
	$para['type'] = 10;
	$para['userId'] = (int)$_GET['user_id'];
	$para['viplevel'] = (int)$_GET['viplevel'];
	echo json_encode($para)."||";
	$result = curlPOST2($_GET['ip'], json_encode($para));
	echo $result; 
	exit;
}else if ($act == "post07"){

	$ip = $_GET['ip'];
	$para = array();
	$para['type'] = 4;
	$para['index'] = 5;
	echo json_encode($para)."||";
	$result = curlPOST2($_GET['ip'], json_encode($para));
	echo $result; 
	exit;
}else if ($act == "post08"){

	$ip = $_GET['ip'];
	$para = array();
	$para['type'] = 4;
	$para['index'] = (int)$_GET['cate'];
	echo json_encode($para)."||";
	$result = curlPOST2($_GET['ip'], json_encode($para));
	echo $result; 
	exit;
}else if ($act == "post09"){

	$ip = $_GET['ip'];
	$para = array();
	$para['type'] = 5;
	$para['starttime'] = (int)strtotime($_GET['startime']);
	$para['endtime'] = (int)strtotime($_GET['endtime']);
	$para['message'] = $_GET['message'];
	echo json_encode($para)."||";
	$result = curlPOST2($_GET['ip'], json_encode($para));
	echo $result; 
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>模拟请求</title>
<script src="jquery.js"></script>
</head>

<body>
<p align="center">&nbsp;</p>
<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#999999">
  <form id="form1" name="form1" method="post" action="">
  <tr>
    <td height="35">&nbsp;</td>
    <td style="padding:5px;">1.订单模拟</td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">请求IP地址： </div></td>
    <td width="73%"><div align="left" style="padding:5px;">
      
        <input type="text" name="ip" id="ip" value="192.168.1.252:9002" />
      
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">商品ID： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <input type="text" name="goodsId" id="goodsId" value="" />
      
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">订单号： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <input type="text" name="order" id="order" value="<?php echo "26".date("mdHis").rand(1000,9999); ?>" />
      
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">订单金额： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <input type="text" name="money" id="money" value="600" />
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">渠道号： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <input type="text" name="channel" id="channel" value="" />
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">支付方式： </div></td>
    <td><div align="left" style="padding:5px;">
      <input name="payment" type="radio" value="101" checked="checked" />
    支付宝
    <input type="radio" name="payment" value="102" />
银行卡
<input type="radio" name="payment" value="103" />
信用卡
<input type="radio" name="payment" value="110" />
微信</div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">支付状态： </div></td>
    <td><div align="left" style="padding:5px;">
      <select name="status" id="status">
        <option value="1" selected="selected">支付成功</option>
        <option value="-2">金额不匹配</option>
        <option value="-1">支付失败</option>
      </select>
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">用户ID： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <input type="text" name="userId" id="userId" value="10321888" />
      
      </div></td>
  </tr>
  
  <tr>
    <td height="35">&nbsp;</td>
    <td><div style="padding:5px;"><input type="button" id="button01" name="Submit" value="请求" /></div></td>
  </tr>
  <tr>
    <td height="35">&nbsp;</td>
    <td><div id="show01" style="padding:5px;"></div></td>
  </tr>
  </form>
</table>
<p align="center">&nbsp;</p>
<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#999999">
  <form id="form2" name="form2" method="post" action="">
  <tr>
    <td height="35">&nbsp;</td>
    <td style="padding:5px;">2.金币添加</td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">请求IP地址： </div></td>
    <td width="73%"><div align="left" style="padding:5px;">
      
        <input type="text" name="ip2" id="ip2" value="192.168.1.252:9002" />
      
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">用户UID： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <input type="text" name="user_id" id="user_id" value="10321888" />
      
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">添加金币数量： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <input type="text" name="gold" id="gold" value="10000" />

      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">添加钻石数量： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <input type="text" name="diamond" id="diamond" value="" />

      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">添加存款数量： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <input type="text" name="deposit" id="deposit" value="" />

      </div></td>
  </tr>
  
  <tr>
    <td height="35">&nbsp;</td>
    <td><div style="padding:5px;"><input type="button" id="button02" name="Submit" value="请求" /></div></td>
  </tr>
  <tr>
    <td height="35">&nbsp;</td>
    <td><div id="show02" style="padding:5px;"></div></td>
  </tr>
  </form>
</table>
<p align="center">&nbsp;</p>
<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#999999">
  <form id="form3" name="form3" method="post" action="">
  <tr>
    <td height="35">&nbsp;</td>
    <td style="padding:5px;">3.VIP增值卡发放</td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">请求IP地址： </div></td>
    <td width="73%"><div align="left" style="padding:5px;">
      
        <input type="text" name="ip3" id="ip3" value="192.168.1.252:9002" />
      
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">用户UID： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <input type="text" name="user_id3" id="user_id3" value="10321888" />
      
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">用户当前VIP等级： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <input type="text" name="viplevel" id="viplevel" value="1" />
      
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">增值卡： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <select name="zeng" id="zeng"><option value="0">请选择</option><option value="12">白钻卡</option><option value="13">绿钻卡</option><option value="14">红钻卡</option><option value="15">紫钻卡</option><option value="16">黑钻卡</option></select>

      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">数量： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <input type="text" name="num3" id="num3" value="" />

      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">成长值： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <input type="text" name="czz" id="czz" value="" />

      </div></td>
  </tr>
  
  <tr>
    <td height="35">&nbsp;</td>
    <td><div style="padding:5px;"><input type="button" id="button03" name="Submit" value="请求" /></div></td>
  </tr>
  <tr>
    <td height="35">&nbsp;</td>
    <td><div id="show03" style="padding:5px;"></div></td>
  </tr>
  </form>
</table>
<p align="center">&nbsp;</p>
<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#999999">
  <form id="form4" name="form4" method="post" action="">
  <tr>
    <td height="35">&nbsp;</td>
    <td style="padding:5px;">4.踢人下线</td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">请求IP地址： </div></td>
    <td width="73%"><div align="left" style="padding:5px;">
      
        <input type="text" name="ip4" id="ip4" value="192.168.1.252:9002" />
      
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">用户UID： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <input type="text" name="user_id4" id="user_id4" value="10321888" />
      
      </div></td>
  </tr>
  
  <tr>
    <td height="35">&nbsp;</td>
    <td><div style="padding:5px;"><input type="button" id="button04" name="Submit" value="请求" /></div></td>
  </tr>
  <tr>
    <td height="35">&nbsp;</td>
    <td><div id="show04" style="padding:5px;"></div></td>
  </tr>
  </form>
</table>
<p align="center">&nbsp;</p>
<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#999999">
  <form id="form5" name="form5" method="post" action="">
  <tr>
    <td height="35">&nbsp;</td>
    <td style="padding:5px;">5.违规头像审核</td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">请求IP地址： </div></td>
    <td width="73%"><div align="left" style="padding:5px;">
      
        <input type="text" name="ip5" id="ip5" value="192.168.1.252:9002" />
      
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">用户UID： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <input type="text" name="user_id5" id="user_id5" value="10321888" />
      
      </div></td>
  </tr>
  
  <tr>
    <td height="35">&nbsp;</td>
    <td><div style="padding:5px;"><input type="button" id="button05" name="Submit" value="请求" /></div></td>
  </tr>
  <tr>
    <td height="35">&nbsp;</td>
    <td><div id="show05" style="padding:5px;"></div></td>
  </tr>
  </form>
</table>
<p align="center">&nbsp;</p>
<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#999999">
  <form id="form6" name="form6" method="post" action="">
  <tr>
    <td height="35">&nbsp;</td>
    <td style="padding:5px;">6.修改会员VIP等级</td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">请求IP地址： </div></td>
    <td width="73%"><div align="left" style="padding:5px;">
      
        <input type="text" name="ip6" id="ip6" value="192.168.1.252:9002" />
      
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">用户UID： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <input type="text" name="user_id6" id="user_id6" value="10321888" />
      
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">用户等级： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <input type="text" name="viplevel6" id="viplevel6" value="" />
      
      </div></td>
  </tr>
  
  <tr>
    <td height="35">&nbsp;</td>
    <td><div style="padding:5px;"><input type="button" id="button06" name="Submit" value="请求" /></div></td>
  </tr>
  <tr>
    <td height="35">&nbsp;</td>
    <td><div id="show06" style="padding:5px;"></div></td>
  </tr>
  </form>
</table>
<p align="center">&nbsp;</p>
<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#999999">
  <form id="form7" name="form7" method="post" action="">
  <tr>
    <td height="35">&nbsp;</td>
    <td style="padding:5px;">7.VIP16上线喇叭开关</td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">请求IP地址： </div></td>
    <td width="73%"><div align="left" style="padding:5px;">
      
        <input type="text" name="ip7" id="ip7" value="192.168.1.252:9002" />
      
      </div></td>
  </tr>
  
  <tr>
    <td height="35">&nbsp;</td>
    <td><div style="padding:5px;"><input type="button" id="button07" name="Submit" value="请求" /></div></td>
  </tr>
  <tr>
    <td height="35">&nbsp;</td>
    <td><div id="show07" style="padding:5px;"></div></td>
  </tr>
  </form>
</table>
<p align="center">&nbsp;</p>
<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#999999">
  <form id="form8" name="form8" method="post" action="">
  <tr>
    <td height="35">&nbsp;</td>
    <td style="padding:5px;">8.重新加载配置</td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">请求IP地址： </div></td>
    <td width="73%"><div align="left" style="padding:5px;">
      
        <input type="text" name="ip8" id="ip8" value="192.168.1.252:9002" />
      
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">修改类型： </div></td>
    <td><div align="left" style="padding:5px;">
      
        <input name="cate" type="radio" value="2" checked="checked" />商品配置 <input name="cate" type="radio" value="5" />常规配置 <input name="cate" type="radio" value="7" />登陆奖励 <input name="cate" type="radio" value="6" />在线宝箱 <input name="cate" type="radio" value="5" />VIP配置  <input name="cate" type="radio" value="9" />任务配置 <input name="cate" type="radio" value="8" />大喇叭配置 <input name="cate" type="radio" value="4" />发牌参数 <input name="cate" type="radio" value="1" />老虎机  <input name="cate" type="radio" value="3" />时时彩 <input name="cate" type="radio" value="10" />大转轮抽奖

      </div></td>
  </tr>
  
  <tr>
    <td height="35">&nbsp;</td>
    <td><div style="padding:5px;"><input type="button" id="button08" name="Submit" value="请求" /></div></td>
  </tr>
  <tr>
    <td height="35">&nbsp;</td>
    <td><div id="show08" style="padding:5px;"></div></td>
  </tr>
  </form>
</table>
<p align="center">&nbsp;</p>
<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#999999">
  <form id="form9" name="form9" method="post" action="">
  <tr>
    <td height="35">&nbsp;</td>
    <td style="padding:5px;">9.系统维护</td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">请求IP地址： </div></td>
    <td width="73%"><div align="left" style="padding:5px;">
      
        <input type="text" name="ip9" id="ip9" value="192.168.1.252:9002" />
      
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">维护开始时间： </div></td>
    <td width="73%"><div align="left" style="padding:5px;">
      
        <input type="text" name="startime" id="startime" value="2016-05-10 15:46:00" />
      
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">维护结束时间： </div></td>
    <td width="73%"><div align="left" style="padding:5px;">
      
        <input type="text" name="endtime" id="endtime" value="2016-05-10 15:50:00" />
      
      </div></td>
  </tr>
  <tr>
    <td width="27%" height="35"><div align="right">维护提示内容： </div></td>
    <td width="73%"><div align="left" style="padding:5px;">
      
        <textarea class="form-control" rows="3" id="message" name="message"></textarea>
      
      </div></td>
  </tr>
  
  <tr>
    <td height="35">&nbsp;</td>
    <td><div style="padding:5px;"><input type="button" id="button09" name="Submit" value="请求" /></div></td>
  </tr>
  <tr>
    <td height="35">&nbsp;</td>
    <td><div id="show09" style="padding:5px;"></div></td>
  </tr>
  </form>
</table>
<p>&nbsp;</p>
<script>
$("#button01").click(function(){ 
	ip = $("#ip").val();
	order = $("#order").val();
	userData = $("#userData").val();
	payment_account = $("#payment_account").val();
	msg = $("#msg").val();
	money = $("#money").val();
	channel = $("#channel").val();
	goodsId = $("#goodsId").val();
	status = $("#status").val();
	userId = $("#userId").val();
	payment = $('input[@name="payment"]:checked').val();

	$.get("response.php", { act: "post01", ip: ip, order: order , money: money , channel: channel , goodsId: goodsId , status: status , userId: userId , payment: payment},
	  function(data){
		result = data.split("||");
		data = "请求数据：" + result[0] + "<br>相应结果：" + result[1];
		$("#show01").html(data);
	});
}); 

$("#button02").click(function(){ 
	ip = $("#ip2").val();
	user_id = $("#user_id").val();
	gold = $("#gold").val();
	diamond = $("#diamond").val();
	deposit = $("#deposit").val();
	$.get("response.php", { act: "post02", ip: ip, user_id: user_id , gold: gold , diamond: diamond , deposit: deposit},
	  function(data){
		result = data.split("||");
		data = "请求数据：" + result[0] + "<br>相应结果：" + result[1];
		$("#show02").html(data);
	});
}); 

$("#button03").click(function(){ 
	ip = $("#ip3").val();
	user_id = $("#user_id3").val();
	viplevel = $("#viplevel").val();
	zeng = $("#zeng").val();
	num3 = $("#num3").val();
	czz = $("#czz").val();
	$.get("response.php", { act: "post03", ip: ip, user_id: user_id , viplevel: viplevel , zeng: zeng , num3: num3 , czz: czz},
	  function(data){
		result = data.split("||");
		data = "请求数据：" + result[0] + "<br>相应结果：" + result[1];
		$("#show03").html(data);
	});
}); 

$("#button04").click(function(){ 
	ip = $("#ip4").val();
	user_id = $("#user_id4").val();
	$.get("response.php", { act: "post04", ip: ip, user_id: user_id },
	  function(data){
		result = data.split("||");
		data = "请求数据：" + result[0] + "<br>相应结果：" + result[1];
		$("#show04").html(data);
	});
}); 

$("#button05").click(function(){ 
	ip = $("#ip5").val();
	user_id = $("#user_id5").val();
	$.get("response.php", { act: "post05", ip: ip, user_id: user_id },
	  function(data){
		result = data.split("||");
		data = "请求数据：" + result[0] + "<br>相应结果：" + result[1];
		$("#show05").html(data);
	});
});

$("#button06").click(function(){ 
	ip = $("#ip6").val();
	user_id = $("#user_id6").val();
	viplevel = $("#viplevel6").val();
	$.get("response.php", { act: "post06", ip: ip, user_id: user_id , viplevel: viplevel },
	  function(data){
		result = data.split("||");
		data = "请求数据：" + result[0] + "<br>相应结果：" + result[1];
		$("#show06").html(data);
	});
});

$("#button07").click(function(){ 
	ip = $("#ip7").val();
	$.get("response.php", { act: "post07", ip: ip, },
	  function(data){
		result = data.split("||");
		data = "请求数据：" + result[0] + "<br>相应结果：" + result[1];
		$("#show07").html(data);
	});
});

$("#button08").click(function(){ 
	ip = $("#ip8").val();
	cate = $('input[@name="cate"]:checked').val();
	$.get("response.php", { act: "post08", ip: ip, cate: cate},
	  function(data){
		result = data.split("||");
		data = "请求数据：" + result[0] + "<br>相应结果：" + result[1];
		$("#show08").html(data);
	});
});

$("#button09").click(function(){ 
	ip = $("#ip9").val();
	startime = $("#startime").val();
	endtime = $("#endtime").val();
	message = $("#message").val();
	$.get("response.php", { act: "post09", ip: ip, startime: startime , endtime: endtime , message: message },
	  function(data){
		result = data.split("||");
		data = "请求数据：" + result[0] + "<br>相应结果：" + result[1];
		$("#show09").html(data);
	});
});
</script>
<?php
function curlPOST2($url, $para) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,$url);
    curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP夿
    //curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
	//curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
    curl_setopt($curl,CURLOPT_POST,true); // post传输数据
    //curl_setopt($curl, CURLOPT_TIMEOUT, 10); // 超时时间
    //echo $para."***"; 
	//print_r($para);
	//$para = http_build_query($para);
    curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
    $responseJson = curl_exec($curl);
	//echo $responseJson."**<br>"; 
    //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    curl_close($curl);

    return $responseJson;
}
?>
</body>
</html>

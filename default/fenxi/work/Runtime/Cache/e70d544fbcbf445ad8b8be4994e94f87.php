<?php if (!defined('THINK_PATH')) exit();?><html lang="zh-CN"><head>
    <meta charset="UTF-8">
    <meta content="点加互动" name="description">
    <meta content="" name="author">
    <meta content="webkit" name="renderer">	<meta content="IE=Edge,chrome=1" http-equiv="X-UA-Compatible">	<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
    <title> 正式运营分析 - 点加互动 </title>
	
	<link href="<?php echo (CSS_PATH); ?>order.css" rel="stylesheet">
    <link href="<?php echo (CSS_PATH); ?>bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo (CSS_PATH); ?>metisMenu.min.css" rel="stylesheet">
    <link href="<?php echo (CSS_PATH); ?>dataTables.bootstrap.css" rel="stylesheet">
    <link href="<?php echo (CSS_PATH); ?>dataTables.responsive.css" rel="stylesheet">
    <link href="<?php echo (CSS_PATH); ?>sb-admin-2.css" rel="stylesheet">
    <link href="<?php echo (CSS_PATH); ?>font-awesome.min.css" rel="stylesheet">


    <script src="<?php echo (JS_PATH); ?>jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo (JS_PATH); ?>bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo (JS_PATH); ?>metisMenu.min.js" type="text/javascript"></script>
    <script src="<?php echo (JS_PATH); ?>jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="<?php echo (JS_PATH); ?>dataTables.bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo (JS_PATH); ?>sb-admin-2.js" type="text/javascript"></script>
	<script src="__PUBLIC__/My97DatePicker/WdatePicker.js" type="text/javascript"></script> 



</head>
<body>

<nav style="margin-bottom: 0" role="navigation" class="navbar navbar-default navbar-static-top">
    <div class="navbar-header">
        <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a href="#" class="navbar-brand">正式运营分析 - 点加互动</a>
    </div>

    <ul class="nav navbar-top-links navbar-right">
        <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                <li><a href="#"><i class="fa fa-user fa-fw"></i> <?php echo $_SESSION['username'];?></a>
                </li>
                <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                </li>
                <li class="divider"></li>
                <li><a href="<?php echo U('Login/logout');?>"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                </li>
            </ul>
            <!-- /.dropdown-user -->
        </li>
        <!-- /.dropdown -->
    </ul>
	
	<div role="navigation" class="navbar-default sidebar">
        <div class="sidebar-nav navbar-collapse">
            <ul id="side-menu" class="nav">
                <li class="sidebar-search">
                    <div class="input-group custom-search-form">
                        <input type="text" placeholder="Search..." class="form-control">
                                <span class="input-group-btn">
                                <button type="button" class="btn btn-default btn-tiny-pad">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                    </div>
                    <!-- /input-group -->
                </li>
				
				<?php if(is_array($show_lanmu)): $i = 0; $__LIST__ = $show_lanmu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sl): $mod = ($i % 2 );++$i;?><li <?php if(($sl["id"]) == $left_css): ?>class="active"<?php endif; ?>>
                    <a href="#"><i class="fa <?php echo ($sl["lanmu_css"]); ?> fa-fw"></i><?php echo ($sl["lanmu_name"]); ?><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse <?php if(($sl["id"]) == $left_css): ?>in<?php endif; ?>">
                        <?php if(is_array($sl["sub"])): $i = 0; $__LIST__ = $sl["sub"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$slsub): $mod = ($i % 2 );++$i;?><li>
                            <a href="<?php echo ($slsub["url"]); ?>"><?php echo ($slsub["lanmu_name"]); ?></a>
                        </li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                
            </ul>
        </div>
        <!-- /.sidebar-collapse -->
    </div>
</nav>	
	<div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h4 class="page-header">运营数据</h4>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
					<div class="panel-heading">
                        <div class="row">
                            <div class="col-sm-4" style="padding-top:10px;"></div>
							<div class="col-sm-8">
                                <form name="form1" class="form-inline pull-right" method="post" action="">
                                    <div class="form-group">
										<input type="text" onClick="WdatePicker()" class="form-control input-sm date" name="beginTime" id="beginTime" placeholder="开始时间" value="<?php echo ($date11); ?>">
                                        -
                                        <input type="text" onClick="WdatePicker()" class="form-control input-sm date" name="endTime" id="endTime" placeholder="结束时间" value="<?php echo ($date12); ?>">&nbsp;<a onclick="selday('1');" href="javascript:;">今日</a>&nbsp;|&nbsp;<a onclick="selday('-1');" href="javascript:;">昨日</a>&nbsp;|&nbsp;<a onclick="selday('7');" href="javascript:;">近7日</a>&nbsp;|&nbsp;<a onclick="selday('30');" href="javascript:;">近30日</a>&nbsp;
                                    </div>
                                    <button class="btn btn-primary btn-sm" type="submit">查询</button>&nbsp;&nbsp;<a href="<?php echo U($By_tpl.'/yunying');?>&act=exceldo1&beginTime=<?php echo ($date11); ?>&endTime=<?php echo ($date12); ?>" target="_blank"><img src="__PUBLIC__/images/daochu1.png" ></a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
				
				<!-- 表格开始 -->
				<div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>日期</th>
									<th>新增用户</th>
									<th>新增付费人数</th>
									<th>新增付费金额</th>
									<th>新增付费率</th>
									<th>用户总量</th>
									<th>DAU</th>
									<th>DAU（老用户）</th>
									<th>有效新增用户</th>
									<th>有效率</th>
									<th>次日留存</th>
									<th>三日留存</th>
									<th>七日留存</th>
									<th>平均在线</th>
									<th>峰值在线</th>
									<th>平均牌局数</th>
									<th>活跃arpu</th>
									<th>日arppu</th>
									<th>新增arpu</th>
									<th>付费人数</th>
									<th>付费金额</th>
									<th>付费率</th>
                                </tr>
                                </thead>
                                <tbody>
                                        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                            <td><?php echo ($vo["data"]); ?></td>
                                            <td><?php echo ($vo["user_add"]); ?></td>
											<td><?php echo ($vo["user_pay_num"]); ?></td>
											<td><?php echo ($vo["user_pay_money"]); ?></td>
											<td><?php echo ($vo["user_pay_lv"]); ?></td>
											<td><?php echo ($vo["user_num"]); ?></td>
											<td><?php echo ($vo["dau"]); ?></td>
											<td><?php echo ($vo["dau_old"]); ?></td>
											<td><?php echo ($vo["user_add_ok"]); ?></td>
											<td><?php echo ($vo["user_ok_lv"]); ?></td>
											<td><?php echo ($vo["liucun1"]); ?></td>
											<td><?php echo ($vo["liucun2"]); ?></td>
											<td><?php echo ($vo["liucun3"]); ?></td>
											<td><?php echo ($vo["online1"]); ?></td>
											<td><?php echo ($vo["online2"]); ?></td>
											<td><?php echo ($vo["paiju"]); ?></td>
											<td><?php echo ($vo["arpu"]); ?></td>
											<td><?php echo ($vo["arppu"]); ?></td>
											<td><?php echo ($vo["arpu_new"]); ?></td>
											<td><?php echo ($vo["user_all_pay_num"]); ?></td>
											<td><?php echo ($vo["user_all_pay_money"]); ?></td>
											<td><?php echo ($vo["uesr_all_pay_lv"]); ?></td>
                                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                 </tbody>
                            </table>
							
                        </div>
                    </div>
                </div>
            </div>
        </div>
				<!-- 表格结束 -->
            </div>
        </div>

    </div>
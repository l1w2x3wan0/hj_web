<?php
class AlixAction extends Action {
//附件作者: lx3gp
//寄    语：谨以此献给那些tper新手们，祝大家学有所成
//写在前面：要使用本方法，请做好一下的基础工作：1.将本项目下的Public 文件夹下所有的文件Copy到你的新项目的根目录（与项目文件夹平级）；2，将本项目下的Tpl/Alix/下的所有文件复制到新项目对应的文件夹下；3.将本项目的Thinkphp/Extend/Library/ORG/Util/GoogChart.class.php 复制到你新项目对应的文件夹下
//注意事项：如果你需要修改，请根据本实例进行修改对应参数即可
//特别鸣谢：在此感谢Thinkphp 论坛大神zhangya4548，lisan的帮助，有写的不好的地方请大家指正，共同进步，互相学习！
	public function index()
					{
					/*引入GoogChart类*/
					import("ORG.Util.GoogChart");
					$chart = new GoogChart();	

					//ThinkPHP写法：
					
					$firstday='2015-06-01';//根据实际情况修改到时候直接用变量替换	$_POST['firstday']
					
					$lastday='2015-06-10';//根据实际情况修改到时候直接用变量替换 $_POST['lastday']

					$where['time']=array(array('egt',$firstday),array('elt',$lastday),'AND');

					
					//以下为导出name数据	
					
					
//---------------------------------------name数据--------------------------------

					$m = M('think_test');//请将Test修改为你想要获取数据的表
							
					$engine=$m->field("time")->where($where)->group('time')->order('time ASC')->select();
								
//					Print_R($engine);
					//设置$data数组数据;
					foreach($engine as $k=>$value)
								{
								$data0 .= "'".$value[time]."'".",";
								}
								$data0 = substr($data0, 0, -1);
								$this->assign('data0',$data0);	
								
//---------------------------------------小王数据---------------------------								
					
					//以下为导出小王数据	
					
					$m = M('think_test');//请将Test修改为你想要获取数据的表
					//解释一下数据的含义：field("求和所有的sale 作为 sum") -> 条件是($where) ->通过('time')分组 -> 按照('time')升序排列		
					$engine = $m -> field("	sum(CASE WHEN name='小王' THEN sale ELSE 0 END) AS name1")->where($where)->group('time')->order('time ASC')->select();
					//dump($m->_sql());							
//					var_dump($engine);
					//设置$data数组数据;
					$data = "";
					foreach($engine as $k=>$value)
								{
								$data .= $value[name1].",";
								}
								$data = substr($data, 0, -1);
								$this->assign('data',$data);	
					//print_r($data);						
//-------------------------------------小李数据-----------------------------								
					
					//以下为导出小李数据	
					
					$m = M('think_test');//请将Test修改为你想要获取数据的表
					//解释一下数据的含义：field("求和所有的sale 作为 sum") -> 条件是($where) ->通过('time')分组 -> 按照('time')升序排列		
					$engine = $m -> field("sum(CASE WHEN name='小李' THEN sale ELSE 0 END) AS name2")->where($where)->group('time')->order('time ASC')->select();
												
//					var_dump($engine);
					//设置$data数组数据;
					foreach($engine as $k=>$value)
								{
								$data1 .= $value[name2].",";
								}
								$data1 = substr($data1, 0, -1);
								$this->assign('data1',$data1);	
											
//-----------------------------------小宋数据-------------------------------								
					
					//以下为导出小宋数据	
					
					$m = M('think_test');//请将Test修改为你想要获取数据的表
					//解释一下数据的含义：field("求和所有的sale 作为 sum") -> 条件是($where) ->通过('time')分组 -> 按照('time')升序排列		
					$engine = $m -> field("sum(CASE WHEN name='小宋' THEN sale ELSE 0 END) AS name3")->where($where)->group('time')->order('time ASC')->select();
												
//					var_dump($engine);
					//设置$data数组数据;
					foreach($engine as $k=>$value)
								{
								$data2 .= $value[name3].",";
								}
								$data2 = substr($data2, 0, -1);
								$this->assign('data2',$data2);	
												
						$this -> display();


					}						

	}
?>
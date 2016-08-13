<?php
// 运营分析文件

class JiangquanAction extends BaseAction {

	protected $By_tpl = 'Jiangquan'; 
	
	public function tongji1(){
		$table = "fx_tongji1";
		$row = M($table);
		$table1 = "user_info";
		$row1 = M($table1);
		$table2 = "log_mall_lottery_log";
		$row2 = M($table2);
		
		$beginTime = I("beginTime");
		$endTime = I("endTime");
		$user_id = I("user_id");
		$version = I("version");
		$showflag = I("flag");
		$act = I("act");
		
		//查询不能大于当天
		$timenow = strtotime(date("Y-m-d"));
		if (strtotime($beginTime)>strtotime($endTime)){
			$this->error('开始日期不能大于结束日期');
			exit;
		}
		
		if (!empty($beginTime) && !empty($endTime)){
			$date11 = date("Y-m-d", strtotime($beginTime));
			$date12 = date("Y-m-d", strtotime($endTime));
			$day_jian = (strtotime($date12) - strtotime($date11)) / (60 * 60 * 24) + 1;
			
			//$datenow = date("d", strtotime($endTime));
			//$dateend = date("d", strtotime($beginTime));
		}else{
			$date12 = date("Y-m-d");
			$day_jian = 7;
			$date11 = date("Y-m-d", (strtotime($date12) - 60 * 60 * 24 * ($day_jian - 1)));
		}
		
		$this->assign('date11',$date11);
		$this->assign('date12',$date12);
		$this->assign('user_id',$user_id);
		
		$time1 = strtotime($date11);
		$time2 = strtotime($date12)+60 * 60 * 24;
		$sql0 = " (addtime>=$time1 and addtime<$time2)";
		if (!empty($user_id)) $sql0 .= " and user_id=$user_id";
		//echo $day_jian;
		//$date11 .= " 00:00:00"; 
		//$date12 .= " 23:59:59"; 
		$Tablename = "log_mall_lottery_log";
		$rowlist=M($Tablename);
		import('ORG.Util.Page');
		$count=$rowlist->where($sql0)->count('id');
		$Page       = new Page($count,PAGE_SHOW);//实例化分页类传入总记录数和每页显示的记录数		
		$show       = $Page->show();// 分页显示输出
		
		$list = $rowlist->where($sql0)->order('addtime DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($rowlist->_sql());
		foreach($list as $key=>$value){
			$list[$key]['addtime'] = (!empty($value['addtime'])) ? date("Y-m-d H:i:s", $value['addtime']) : "-";
			$list[$key]['typename'] = ($value['type']=="1") ? "虚拟物品" : "实物";
			$list[$key]['status'] = ($value['status']=="1") ? "兑换成功" : "兑换失败";
		}
		//print_r($event1)
				
		//exit;
		$this->assign('pageshow',$show);
		$this->assign('tongji1',$list);
		
		$this->assign('left_css',"71");
		$this->assign('By_tpl',$this->By_tpl);
		$lib_display = $this->By_tpl."/tongji1";
		$this->display($lib_display);
	}
	
	
	
	
	//导出
	public function exportExceldo($expTitle,$expCellName,$expTableData){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = $xlsTitle.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel.PHPExcel");
       
        $objPHPExcel = new PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        
        //$objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        //$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle);  
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $expCellName[$i][1]); 
        } 
          // Miscellaneous glyphs, UTF-8   
        for($i=0;$i<$dataNum;$i++){
          for($j=0;$j<$cellNum;$j++){
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), $expTableData[$i][$expCellName[$j][0]]);
          }             
        }  
        
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
        $objWriter->save('php://output'); 
        exit;   
	}
		
	//测试
	public function test(){
		$table5 = "user_info";
		$row5 = M($table5);
		$sql1 = "!((user_id>=".ROBERT1_BEGIN." and user_id<".ROBERT1_END.") or (user_id>=".ROBERT2_BEGIN." and user_id<=".ROBERT2_END."))";
		$res = $row5->field("lost_count,win_count,lost_count+win_count as sum")->where($sql1." AND win_count>150")->limit(0,200)->select();
		//dump($row5->_sql());
		$count = array();
		for($i=0; $i<8; $i++){
			$count[$i] = 0;
		}
		foreach ($res as $key => $val){
			$lv = (!empty($val['sum'])) ? round($val['win_count']/$val['sum'],3)*100 : 0;
			echo $lv."**";
			if ($key % 9==0) echo "<br>";
			if ($lv<20){
				$count[0]++;
			}else if ($lv<25){
				$count[1]++;
			}else if ($lv<30){
				$count[2]++;
			}else if ($lv<35){
				$count[3]++;
			}else if ($lv<40){
				$count[4]++;
			}else if ($lv<45){
				$count[5]++;
			}else if ($lv<50){
				$count[6]++;
			}else{
				$count[7]++;
			}
		}
		echo "<br>";
		print_r($count);
		exit;
	}	
	
	//判断目录是否为空
	public function is_empty_dir($fp)    
    {    
        $H = @opendir($fp); 
        $i=0;    
        while($_file=readdir($H)){    
            $i++;    
        }    
        closedir($H);    
        if($i>2){ 
            return 1; 
        }else{ 
            return 2;  //true
        } 
    } 
	
	//空方法
	 public function _empty() {  
        $this->display("Index/index");
    }
}
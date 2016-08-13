<?php  
  $str = date("Y-m-d H:i:s");
  $uid = $_POST['uid'];
 
    $str .= "Upload: " . $_FILES["pic"]["name"] . "<br />";  
    $str .= "Type: " . $_FILES["pic"]["type"] . "<br />";  
    $str .= "Size: " . ($_FILES["pic"]["size"] / 1024) . " Kb<br />";  
    $str .= "Temp file: " . $_FILES["pic"]["tmp_name"] . "<br />";  
    
	$temp = explode(".",$_FILES["pic"]["name"]);
	$rand_name = time().rand(1000,9999);
	$temp_name = $rand_name.".".$temp[1];
	
    /*if (file_exists("laoxie_photo/" . $_FILES["pic"]["name"]))  
    {  
      $str .= $_FILES["pic"]["name"] . " already exists. ";  
    }  
    else  
    {  
      move_uploaded_file($_FILES["pic"]["tmp_name"], "laoxie_photo/" . $temp_name);  
      $str .= "Stored in: " . "laoxie_photo/" . $_FILES["pic"]["name"];  
    }  */
	$y = date("Y");
	$dir = "tx/".$y;
	if (!file_exists($dir)) {
		mkdir($dir);
	}
	
	$m = date("m");
	$dir .= "/".$m;
	if (!file_exists($dir)) {
		mkdir($dir);
	}
	
	$d = date("d");
	$dir .= "/".$d;
	if (!file_exists($dir)) {
		mkdir($dir);
	}
	
	if (move_uploaded_file($_FILES["pic"]["tmp_name"], $dir . "/" . $temp_name)){
		$result = array('src' => $dir . "/" . $rand_name);
		
	}else{
		$result = array('src' =>0);
	}
	echo json_encode($result);
	

  
  
//$logs_file = "Logs/pic_".time().".txt";
//file_put_contents($logs_file, $uid.$str);
?>  
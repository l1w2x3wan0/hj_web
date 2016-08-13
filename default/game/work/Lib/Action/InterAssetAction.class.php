<?php
class InterAssetAction extends InterAction{

	//修改用户金币
	public function changeGold($para){
		
        //开始修改金币,USER_ID,GOLD已在前面判断是否为空
        $row = M();
        $sql = " CALL ".MYTABLE_PRIFIX."Http_UpdateUserWealth($para['user_id'], $para['gold'], 0, 0)";
        $result = $row->query($sql);
	}

    //修改用户钻石
    public function changeDiamond($para){

        print_r($para); exit;
        //开始修改钻石,USER_ID,DIAMOND已在前面判断是否为空
        $row = M();
        $sql = " CALL ".MYTABLE_PRIFIX."Http_UpdateUserWealth($para['user_id'], 0, $para['diamond'], 0)";
        $result = $row->query($sql);
	}

    //修改用户存款
    public function changeDeposit($para){

        //开始修改存款
        $row = M();
        $sql = " CALL ".MYTABLE_PRIFIX."Http_UpdateUserWealth($para['user_id'], 0, 0, $para['deposit'])";
        $result = $row->query($sql);
	}

    //修改用户礼物（汽车）
    public function changeCar($para){

        //开始修改汽车
        $row = M();
        $sql = " CALL ".MYTABLE_PRIFIX."Http_UpdateUserGift($para['user_id'], $para['car'], 0, 0)";
        $result = $row->query($sql);
	}

    //修改用户礼物（房子）
    public function changeVilla($para){

        //开始修改房子
        $row = M();
        $sql = " CALL ".MYTABLE_PRIFIX."Http_UpdateUserGift($para['user_id'], 0, $para['villa'], 0)";
        $result = $row->query($sql);
	}

    //修改用户礼物（飞机）
    public function changeYacht($para){

        //开始修改飞机
        $row = M();
        $sql = " CALL ".MYTABLE_PRIFIX."Http_UpdateUserGift($para['user_id'], 0, 0, $para['yacht'])";
        $result = $row->query($sql);
	}
		
}

	
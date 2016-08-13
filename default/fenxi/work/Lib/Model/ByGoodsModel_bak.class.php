<?php
class ByGoodsModel extends Model{
    
	//protected $tableName = 'by_goods'; 
	protected $_validate = array( 
		array('goods_id','require','商品编号必须填写!'), 
		array('goods_name','require','商品名称必须填写!'),
		array('goods_name','','商品名称已经存在',0,'unique',1),
	); 

    
}
<?php 

/**
 * Class Xdelivery_Model_OrderItems
 * @package Xdelivery\Model
 */
class Xdelivery_Model_OrderItems extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;

 
    /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_OrderItems::class;

    /**
     * @param $order_id
     * @param array
     * @return Xdelivery_Model_OrderItems[]
     */
    public function findItemByOrderId($order_id, $param = [])
    {
    	return $this->getTable()->findItemByOrderId($order_id, $param);
    }
     

}
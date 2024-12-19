<?php 

/**
 * Class Xdelivery_Model_ProductAttribute
 * @package Xdelivery\Model
 */
class Xdelivery_Model_OrderPrint extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;

 
     /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_OrderPrint::class;

     /**
     * @param array $params
     * @return Xdelivery_Model_Products[]
     */
    public function getOrderPrint($value_id)
    {
        return $this->getTable()->getOrderPrint($value_id);
    }

}
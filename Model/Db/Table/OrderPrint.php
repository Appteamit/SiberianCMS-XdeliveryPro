<?php
/**
 * Class Xdelivery_Model_Db_Table_OrderPrint
 * @package Xdelivery\Model\Db\Table
 */
class Xdelivery_Model_Db_Table_OrderPrint extends Core_Model_Db_Table
{
    protected $_name                    = "xdelivery_order_print";
    protected $_primary                 = "order_print_id";

	  /**
     * @param $app_id
     * @param int $limit
     * @return array
     */
    public function getOrderPrint($value_id) {
        return array();
        }


}
<?php
/**
 * Class Xdelivery_Model_Db_Table_Printers
 * @package Xdelivery\Model\Db\Table
 */
class Xdelivery_Model_Db_Table_Printers extends Core_Model_Db_Table
{
    protected $_name                    = "xdelivery_printer";
    protected $_primary                 = "printer_id";

	  /**
     * @param $app_id
     * @param int $limit
     * @return array
     */
    public function getPrinters($value_id) {
        return array();
    }
     

}
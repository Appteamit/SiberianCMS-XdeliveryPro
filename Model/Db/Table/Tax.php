<?php
/**
 * Class Xdelivery_Model_Db_Table_Tax
 * @package Xdelivery\Model\Db\Table
 */
class Xdelivery_Model_Db_Table_Tax extends Core_Model_Db_Table
{

    protected $_name                    = "xdelivery_taxes";
    protected $_primary                 = "id";

	public function findAll($value_id=0){        
        $select = "SELECT * FROM `xdelivery_taxes` WHERE  value_id=$value_id AND `status`!='deleted';";
        return $this->_db->fetchAll($select);
    }
}
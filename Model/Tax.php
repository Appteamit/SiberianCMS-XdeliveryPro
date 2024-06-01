<?php 

/**
 * Class Xdelivery_Model_Tax
 * @package Xdelivery\Model
 */
class Xdelivery_Model_Tax extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;

 
   /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_Tax::class;

    /**
     * @param $value_id
     * @return []
     */
    public function findAll($value_id=0)
    {
        return $this->getTable()->findAll($value_id);
    }

}
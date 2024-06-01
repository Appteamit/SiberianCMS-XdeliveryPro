<?php 

/**
 * Class Xdelivery_Model_PaymentMethod
 * @package Xdelivery\Model
 */
class Xdelivery_Model_PaymentMethod extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;

 
    /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_PaymentMethod::class;

}
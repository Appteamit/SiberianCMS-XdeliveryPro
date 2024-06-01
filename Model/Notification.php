<?php 

/**
 * Class Xdelivery_Model_Notification
 * @package Xdelivery\Model
 */
class Xdelivery_Model_Notification extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;
     /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_Notification::class;
}
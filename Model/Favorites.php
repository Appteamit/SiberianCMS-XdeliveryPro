<?php 

/**
 * Class Xdelivery_Model_Favorites
 * @package Xdelivery\Model
 */
class Xdelivery_Model_Favorites extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;

 
    /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_Favorites::class;

}
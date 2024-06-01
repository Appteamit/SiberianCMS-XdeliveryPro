<?php 

/**
 * Class Xdelivery_Model_ExtraOption
 * @package Xdelivery\Model
 */
class Xdelivery_Model_ExtraOption extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;

 
    /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_ExtraOption::class;

    /**
     * @param $valuesId
     * @param array $params
     * @return Xdelivery_Model_Category[]
     */
    public function findByValueId($valuesId, $params = [])
    {
        return $this->getTable()->findByValueId($valuesId, $params);
    }

    /**
     * @param $valuesId
     * @param array $params
     * @return Xdelivery_Model_Category[]
     */
    public function countAllForApp($valuesId, $params = [])
    {
        return $this->getTable()->countAllForApp($valuesId, $params);
    }

    /**
     * @param $valuesId
     * @param array $params
     * @return Xdelivery_Model_Category[]
     */
    public function maxPosition($valuesId)
    {
        return $this->getTable()->maxPosition($valuesId);
    }

    /**
     * @param $valuesId
     * @param array $params
     * @return Xdelivery_Model_Category[]
     */
    public function sortable($params)
    {
        return $this->getTable()->sortable($params);
    }


}
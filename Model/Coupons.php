<?php

class Xdelivery_Model_Coupons extends Core_Model_Default {

    /**
     * @var null
     */
    public static $acl = null;

    /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_Coupons::class;

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
     * @param $value_id
     * @return []
     */
    public function findAll($value_id=0)
    {
        return $this->getTable()->findAll($value_id);
    }

}
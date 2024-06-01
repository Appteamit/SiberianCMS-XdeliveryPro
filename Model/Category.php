<?php

class Xdelivery_Model_Category extends Core_Model_Default {

    /**
     * @var null
     */
    public static $acl = null;

    /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_Category::class;

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
    public function maxPosition($valuesId, $parentId)
    {
        return $this->getTable()->maxPosition($valuesId, $parentId);
    }

    /**
     * @param $valuesId
     * @param array $params
     * @return Xdelivery_Model_Category[]
     */
    public function sortable($params, $parent_id)
    {
        return $this->getTable()->sortable($params, $parent_id);
    }
 
    /**
     * @param $valuesId
     * @param array $params
     * @return Xdelivery_Model_Category[]
     */
    public function getCategorySubCategory($valuesId, $params = [])
    {
        $mainCategory = $this->getTable()->findByValueId($valuesId, $params)->toArray();        
        return $this->getList($mainCategory);
    }
    /**
     * @param $Array
     * @param array $id
     * @return Array[]
     */
    function getList($rows, $id = 0) {
        $newList = [];

        foreach($rows as $key => $row) {
            if ($row['parent_id'] == $id) {
                if(empty($newList[$row['id']])){
                    $newList[$row['id']] = $row;
                }        
                $newList[$row['id']]['subcategory'] = null;       
                if($row['subcategory_count'] > 0){
                    $newList[$row['id']]['subcategory'] = $this->getList($rows, $row['id']); 
                }
           }   
        }        
        return $newList;
    }

   
}
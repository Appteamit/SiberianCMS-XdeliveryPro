<?php

class Xdelivery_Model_Db_Table_AttributeValues extends Core_Model_Db_Table {
    protected $_name                    = "xdelivery_attribute_values";
    protected $_primary                 = "id";


    /**
     * @param $app_id
     * @param int $limit
     * @return array
     */
    public function findByValueId($value_id, $params = []) {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
            	"id",
                "value_name",
                "position",
                "is_active",
                "created_at"          
            ]);

            $select->where("main.value_id = ?", $value_id);
            $select->where("main.attribute_id = ?", $params['attribute_id']);
            $select->where("main.is_active != ?", 2);  
            $select->order('main.position ASC');
	        
            return $this->toModelClass($this->_db->fetchAll($select));

        }


 	 /**
     * @param $value_id
     */
    public function countAllForApp($value_id, $params = [])
    {
        $select =$this->_db->select()
            ->from(['main' => $this->_name], [ 
            	 'COUNT(main.id)'
                ]);

        $select->where("main.value_id = ?", $value_id);
        $select->where("main.attribute_id = ?", $params['attribute_id']);
        $select->where("main.is_active != ?", 2);  
        $select->order('main.position ASC');

        return $this->_db->fetchCol($select);
    }

    /**
     * @param $value_id
     */
    public function maxPosition($value_id)
    {
        $select =$this->_db->select()
            ->from(['main' => $this->_name], [ 
                 'MAX(main.position)'
                ])
        ->where('main.value_id = ?', $value_id)
        ->where("main.attribute_id = ?", $params['attribute_id']);
       
        return $this->_db->fetchCol($select);
    }

     /**
     * @param $data
     */
    public function sortable($data, $attribute_id) {       
      try {
            foreach ($data as $key => $value) {
                if($value!=''){
                   $this->_db->update($this->_name , array('position' => $key + 1 ) , array('value_name = ? ' => $value, 'attribute_id' => $attribute_id ));
                }
            }
        }catch(Exception $e) {
            $this->_db->rollBack();
        }
         return $this;
    }
 
}
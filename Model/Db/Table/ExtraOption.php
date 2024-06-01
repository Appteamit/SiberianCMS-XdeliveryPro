<?php

class Xdelivery_Model_Db_Table_ExtraOption extends Core_Model_Db_Table {
    protected $_name                    = "xdelivery_extra_options";
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
                "name",
                "category_type",
                "is_required",
                "maximum_option",
                "minimum_option",
                "is_active",
                "created_at",
                "values_count" => new Zend_Db_Expr('('.$this->_db->select()->from(array('v'=> "xdelivery_extra_option_values"),array(new Zend_Db_Expr('COUNT(v.id)')))->where('v.option_id = main.id')->where('v.is_active != ?', 2).')'),     
            ]);

          $select->where("main.value_id = ?", $value_id);
          
            if(array_key_exists("is_active", $params)){
              $select->where("main.is_active = ?", $params['is_active']);
            }else{
                $select->where("main.is_active != ?", 2);
            }

         if (array_key_exists("limit", $params) && array_key_exists("offset", $params)) {
	            $select->limit($params["limit"], $params["offset"]);
	        }

	        if (array_key_exists("filter", $params) && !empty($params["filter"])) {
	            $select->where("(main.name LIKE ?)", "%" . $params["filter"] . "%");
	        }
  
          if (array_key_exists("sorts", $params) && !empty($params["sorts"])) {
	            $orders = [];
	            foreach ($params["sorts"] as $key => $dir) {
	                $order = ($dir == -1) ? "DESC" : "ASC";
	                $orders = "main.{$key} {$order}";
	            }
	            $select->order($orders);
	        } else {
	            $select->order('main.position ASC');
	        }
          
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
                ])
            ->where('main.value_id = ?', $value_id);

        $select->where("main.is_active != ?", 2);
   
        if (array_key_exists("filter", $params)) {
            $select->where("(main.name LIKE ?)", "%" . $params["filter"] . "%");
        }
 
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
        ->where('main.value_id = ?', $value_id);
       
        return $this->_db->fetchCol($select);
    }

     /**
     * @param $data
     */
    public function sortable($data) {       
      try {
            foreach ($data as $key => $value) {
                if($value!=''){
                   $id = explode('_',$value, 2);
                    $this->_db->update($this->_name , array('position' => $key+1 ) , array('id = ? ' => $id[1]));
                }
            }
        }catch(Exception $e) {
            $this->_db->rollBack();
        }
         return $this;
    }


   



}
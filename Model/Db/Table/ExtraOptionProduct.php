<?php

class Xdelivery_Model_Db_Table_ExtraOptionProduct extends Core_Model_Db_Table {
    protected $_name  = "xdelivery_extra_option_products";
    protected $_primary = "id";

    /**
     * @param $app_id
     * @param int $limit
     * @return array
     */
    public function getProductOptions($product_id, $params = []) {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "option_id",
                "product_id",
                "created_at",
            ]);

          $select->joinLeft(['o' => 'xdelivery_extra_options'], 'o.id = main.option_id', ["o.name", "o.category_type", "o.is_required",
                "o.maximum_option", "o.minimum_option"]);

          $select->where("main.product_id = ?", $product_id);
          $select->where("o.is_active = ?", 1);
          $select->order('o.position ASC');

          if (array_key_exists("is_active", $params)) {
              $select->where("main.is_active = ?", 1);
          }
       
           
       return $this->_db->fetchAll($select);
    }


}
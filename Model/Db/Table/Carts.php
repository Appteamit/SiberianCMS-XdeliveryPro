<?php

class Xdelivery_Model_Db_Table_Carts extends Core_Model_Db_Table {
    protected $_name                    = "xdelivery_shopping_carts";
    protected $_primary                 = "id";

   /**
     * @param $app_id
     * @param int $limit
     * @return array
     */
    public function findByDeviceId($value_id, $device_uid) {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
            	"id",
                "product_id",
                "cart_key",
                "qty",
                "amount",
                "tax_amount",
                "total_amount",
                "attributejson",
            ]);

            $select->joinLeft(['p' => 'xdelivery_products'], 'p.id = main.product_id', ["product_name", "price", "special_price", "sku", "special_price_start", "special_price_end", "is_active", "manage_stock", "qty as product_qty", "low_stock_threshold","in_stock", "product_type", "description","tax_id", "created_at"]);
		    
            $select->joinRight(['i' => 'xdelivery_product_images'], 'i.product_id = main.product_id AND i.is_base = 1', ['i.product_image']);

            $select->joinLeft(['t' => 'xdelivery_taxes'], 't.id = p.tax_id AND t.status = "active"', ['t.tax_rate']);

            $select->order('main.id DESC');
            $select->where("main.device_uid = ?", $device_uid);
            $select->where("main.value_id = ?", $value_id);
            $select->where("p.is_active != ?", 2);
          
          return $this->toModelClass($this->_db->fetchAll($select));
    }


         /**
     * @param $value_id
     * @param int $limit
     * @return array
     */
    public function findByDeviceAndUserId($value_id, $customer_id, $device_uid) {

        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
              "id",
                "product_id",
                "child_product_id",
                "cart_key",
                "store_id",
                "qty",
                "amount",
                "tax_amount",
                "total_amount",
                "optionsjson",
            ]);

            $select->joinLeft(['p' => 'xdelivery_products'], 'p.id = main.product_id', ["product_name", "price", "special_price", "sku", "special_price_start", "special_price_end", "is_active", "manage_stock", "qty as product_qty", "low_stock_threshold","in_stock", "product_type", "description", "tax_id", "created_at"]);
 
            $select->joinLeft(['i' => 'xdelivery_product_images'], 'i.product_id = main.product_id AND i.is_base = 1', ['i.product_image']);
 
            $select->joinLeft(['t' => 'xdelivery_taxes'], 't.id = p.tax_id AND t.status = "active"', ['t.tax_rate']);

            $select->order('main.id DESC');
            //$select->where("main.device_uid = ? OR main.customer_id = ?", $device_uid, $customer_id);
            if(!empty($customer_id)){
               $select->where("main.customer_id = ?", $customer_id);
            }else{
               $select->where("main.device_uid = ?", $device_uid);
            }          
           
            $select->where("main.value_id = ?", $value_id);
            $select->where("p.is_active != ?", 2);
            // dd($this->_db->fetchAll($select));
          return $this->toModelClass($this->_db->fetchAll($select));
    }

             /**
     * @param $value_id
     * @param int $limit
     * @return array
     */
    public function countByDeviceAndUserId($value_id, $customer_id, $device_uid) {

        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                'count' => new Zend_Db_Expr('COUNT(id)')
            ]);

            $select->joinLeft(['p' => 'xdelivery_products'], 'p.id = main.product_id');
 
            $select->joinLeft(['i' => 'xdelivery_product_images'], 'i.product_id = main.product_id');
 
            $select->joinLeft(['t' => 'xdelivery_taxes'], 't.id = p.tax_id AND t.status = "active"', ['t.tax_rate']);

            if(!empty($customer_id)){
               $select->where("main.customer_id = ?", $customer_id);
            }else{
               $select->where("main.device_uid = ?", $device_uid);
            }          
           
            $select->where("main.value_id = ?", $value_id);
            $select->where("p.is_active != ?", 2);
       
        return (int) count($this->toModelClass($this->_db->fetchAll($select)));

    }



    public function syncDeviceCustomer($device_uid, $customer_id, $value_id){
          $query =  $this->_db->update($this->_name, ["customer_id" => $customer_id, "device_uid" => $device_uid], ["device_uid = ?" => $device_uid, "value_id = ?" => $value_id]);
        if($query){
          return true;
        }
        return false;
     }

     

}
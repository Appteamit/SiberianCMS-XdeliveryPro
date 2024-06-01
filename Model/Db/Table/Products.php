<?php
/**
 * Class Xdelivery_Model_Db_Table_Products
 * @package Xdelivery\Model\Db\Table
 */
class Xdelivery_Model_Db_Table_Products extends Core_Model_Db_Table
{

    protected $_name                    = "xdelivery_products";
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
                "parent_id",
                "product_name",
                "short_description",
                "price",
                "special_price",
                "sku",
                "special_price_start",
                "special_price_end",
                "is_active",
                "manage_stock",
                "qty",
                "low_stock_threshold",
                "in_stock",
                "product_type",
                "description",
                "tax_id",
                "created_at",
                "value_id",
                'max_amount' => new Zend_Db_Expr('('.$this->_db->select()->from(array('m'=> $this->_name),array(new Zend_Db_Expr('MAX(m.price)')))->where('m.parent_id = main.id')->where('m.is_active != ?', 2).')'),
                'min_amount' => new Zend_Db_Expr('('.$this->_db->select()->from(array('m'=> $this->_name),array(new Zend_Db_Expr('MIN(m.price)')))->where('m.parent_id = main.id')->where('m.is_active != ?', 2).')')                       
            ]);

			     $select->where("main.value_id = ?", $value_id);
           $select->where("main.parent_id = ?", 0);


            if (array_key_exists("is_active", $params)) {
              $select->where("main.is_active = ?", $params["is_active"]); 
            } else{
               $select->where("main.is_active != ?", 2);
            }		     
            
            $select->joinLeft(['i' => 'xdelivery_product_images'], 'i.product_id = main.id && i.is_base = 1', ['i.product_image']);

            $select->joinLeft(['t' => 'xdelivery_taxes'], 't.id = main.tax_id', ['t.tax_rate']);

            $select->joinLeft(['s' => 'xdelivery_store'], 's.store_id = main.store_id', ['s.store_name']);
      
            if (array_key_exists("limit", $params) && array_key_exists("offset", $params)) {
              $select->limit($params["limit"], $params["offset"]);
            }

            if (array_key_exists("search", $params)) {
              $select->where("(main.product_name LIKE ?)", "%" . $params["search"] . "%");
            }

            if (array_key_exists("store_id", $params) && !empty($params['store_id'])) {
              $select->where("(main.store_id = ?)", $params["store_id"] );
            }

            if (array_key_exists("parent_id", $params)) {
              $select->joinLeft(['c' => 'xdelivery_product_categories'], 'c.product_id = main.id AND c.category_id = '.$params["parent_id"].'' ,array()); 
              $select->where("(c.category_id = ?)", $params["parent_id"] );
            }

            if (array_key_exists("sorts", $params) && !empty($params["sorts"])) {
              $orders = [];
              foreach ($params["sorts"] as $key => $dir) {
                  $order = ($dir == -1) ? "DESC" : "ASC";
                  $orders = "main.{$key} {$order}";
              }
              $select->order($orders);

            } else {
              $select->order('main.id DESC');
            }
          
          return $this->toModelClass($this->_db->fetchAll($select));
        }
    public function getProducts($value_id, $params = []) {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
            	  "id",
                "parent_id",
                "product_name",
                "short_description",
                "price",
                "special_price",
                "sku",
                "special_price_start",
                "special_price_end",
                "is_active",
                "manage_stock",
                "qty",
                "low_stock_threshold",
                "in_stock",
                "product_type",
                "description",
                "tax_id",
                "created_at",
                "value_id",
                "store_id",
                "brand_id",
                "new_from",
                "new_to",
                'max_amount' => new Zend_Db_Expr('('.$this->_db->select()->from(array('m'=> $this->_name),array(new Zend_Db_Expr('MAX(m.price)')))->where('m.parent_id = main.id')->where('m.is_active != ?', 2).')'),
                'min_amount' => new Zend_Db_Expr('('.$this->_db->select()->from(array('m'=> $this->_name),array(new Zend_Db_Expr('MIN(m.price)')))->where('m.parent_id = main.id')->where('m.is_active != ?', 2).')')                       
            ]);

			     $select->where("main.value_id = ?", $value_id);
           $select->where("main.parent_id = ?", 0);


            if (array_key_exists("is_active", $params)) {
              $select->where("main.is_active = ?", $params["is_active"]); 
            } else{
               $select->where("main.is_active != ?", 2);
            }		     
            
            $select->joinLeft(['i' => 'xdelivery_product_images'], 'i.product_id = main.id && i.is_base = 1', ['i.product_image']);

            $select->joinLeft(['t' => 'xdelivery_taxes'], 't.id = main.tax_id', ['t.tax_rate']);

            $select->joinLeft(['s' => 'xdelivery_store'], 's.store_id = main.store_id', ['s.store_name']);
      
            if (array_key_exists("limit", $params) && array_key_exists("offset", $params)) {
              $select->limit($params["limit"], $params["offset"]);
            }

            if (array_key_exists("search", $params)) {
              $select->where("(main.product_name LIKE ?)", "%" . $params["search"] . "%");
            }

            if (array_key_exists("store_id", $params) && !empty($params['store_id'])) {
              $select->where("(main.store_id = ?)", $params["store_id"] );
            }

            if (array_key_exists("parent_id", $params)) {
              $select->joinLeft(['c' => 'xdelivery_product_categories'], 'c.product_id = main.id AND c.category_id = '.$params["parent_id"].'' ,array()); 
              $select->where("(c.category_id = ?)", $params["parent_id"] );
            }

            if (array_key_exists("sorts", $params) && !empty($params["sorts"])) {
              $orders = [];
              foreach ($params["sorts"] as $key => $dir) {
                  $order = ($dir == -1) ? "DESC" : "ASC";
                  $orders = "main.{$key} {$order}";
              }
              $select->order($orders);

            } else {
              $select->order('main.id DESC');
            }
          
          return $this->toModelClass($this->_db->fetchAll($select));
        }



    /**
     * @param $app_id
     * @param int $limit
     * @return array
     */
    public function findAppByValueId($value_id, $params = []) {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "id",
                "parent_id",
                "product_name",
                "short_description",
                "price",
                "special_price",
                "sku",
                "special_price_start",
                "special_price_end",
                "is_active",
                "manage_stock",
                "qty",
                "low_stock_threshold",
                "in_stock",
                "product_type",
                "description",
                "tax_id",
                "created_at",
                "value_id",
                'max_amount' => new Zend_Db_Expr('('.$this->_db->select()->from(array('m'=> $this->_name),array(new Zend_Db_Expr('MAX(m.price)')))->where('m.parent_id = main.id')->where('m.is_active = ?', 1).')'),
                'min_amount' => new Zend_Db_Expr('('.$this->_db->select()->from(array('m'=> $this->_name),array(new Zend_Db_Expr('MIN(m.price)')))->where('m.parent_id = main.id')->where('m.is_active = ?', 1).')')                       
            ]);

           $select->where("main.value_id = ?", $value_id);
           $select->where("main.parent_id = ?", 0);

            if (array_key_exists("is_active", $params)) {
              $select->where("main.is_active = ?", $params["is_active"]); 
            } else{
               $select->where("main.is_active != ?", 2);
            }        
            
            $select->joinLeft(['i' => 'xdelivery_product_images'], 'i.product_id = main.id && i.is_base = 1', ['i.product_image']);

            $select->joinLeft(['b' => 'xdelivery_brands'], 'b.brand_id = main.brand_id', ['b.name as brand_name', 'b.image as brand_image']);

            $select->joinLeft(['t' => 'xdelivery_taxes'], 't.id = main.tax_id', ['t.tax_rate']);
            
            if (array_key_exists("store_id", $params) && !empty($params['store_id'])) {
               $select->where("(main.store_id = ?)", $params["store_id"] );
            }

            if (array_key_exists("limit", $params) && array_key_exists("offset", $params)) {
              $select->limit($params["limit"], $params["offset"]);
            }

            if (array_key_exists("search", $params)) {
              $select->where("(main.product_name LIKE ?)", "%" . $params["search"] . "%");
            }

            if (array_key_exists("parent_id", $params)) {
              $select->joinLeft(['c' => 'xdelivery_product_categories'], 'c.product_id = main.id AND c.category_id = '.$params["parent_id"].'' ,array()); 
              $select->where("(c.category_id = ?)", $params["parent_id"] );
            }

            if (array_key_exists("sorts", $params) && !empty($params["sorts"])) {
              $orders = [];
              foreach ($params["sorts"] as $key => $dir) {
                  $order = ($dir == -1) ? "DESC" : "ASC";
                  $orders = "main.{$key} {$order}";
              }
              $select->order($orders);

            } else {
              $select->order('main.id DESC');
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
        $select->where("main.parent_id = ?", 0);
        $select->where("main.is_active != ?", 2);
   
        if (array_key_exists("search", $params)) {
	            $select->where("(main.product_name LIKE ?)", "%" . $params["search"] . "%");
	    }
 
        return $this->_db->fetchCol($select);
    }


    /**
     * @param $app_id
     * @param int $limit
     * @return array
     */
    public function findByWishlistByCustomer($value_id, $params = []) {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
              "id",
                "product_name",
                "short_description",
                "price",
                "special_price",
                "sku",
                "special_price_start",
                "special_price_end",
                "is_active",
                "manage_stock",
                "qty",
                "low_stock_threshold",
                "in_stock",
                "product_type",
                "description",
                "tax_id",
                "created_at",
                "max_amount" => new Zend_Db_Expr('('.$this->_db->select()->from(array('m'=> $this->_name),array(new Zend_Db_Expr('MAX(m.price)')))->where('m.parent_id = main.id')->where('m.is_active != ?', 2).')'),
                "min_amount" => new Zend_Db_Expr('('.$this->_db->select()->from(array('m'=> $this->_name),array(new Zend_Db_Expr('MIN(m.price)')))->where('m.parent_id = main.id')->where('m.is_active != ?', 2).')')                        
            ]);

            $select->where("main.value_id = ?", $value_id);
            $select->where("main.parent_id = ?", 0);
         
            if (array_key_exists("is_active", $params)) {
              $select->where("main.is_active = ?", $params["is_active"]); 
            } else{
               $select->where("main.is_active != ?", 2);
            }

            $select->joinLeft(['f' => 'xdelivery_customer_favorites'], 'f.product_id = main.id', ['f.id as favorite_id']);

             $select->where("f.customer_id = ? OR f.device_uid = ? ", $params['customer_id'], $param['device_uid']);

            $select->joinLeft(['i' => 'xdelivery_product_images'], 'i.product_id = main.id && i.is_base = 1', ['i.product_image']);

            $select->joinLeft(['t' => 'xdelivery_taxes'], 't.id = main.tax_id', ['t.tax_rate']);
      
            if (array_key_exists("limit", $params) && array_key_exists("offset", $params)) {
              $select->limit($params["limit"], $params["offset"]);
            }

            if (array_key_exists("search", $params)) {
              $select->where("(main.product_name LIKE ?)", "%" . $params["search"] . "%");
            }
 
            if (array_key_exists("sorts", $params) && !empty($params["sorts"])) {
              $orders = [];
              foreach ($params["sorts"] as $key => $dir) {
                  $order = ($dir == -1) ? "DESC" : "ASC";
                  $orders = "main.{$key} {$order}";
              }
              $select->order($orders);

            } else {
              $select->order('main.id DESC');
            }
          
          return $this->toModelClass($this->_db->fetchAll($select));
        }

	
}
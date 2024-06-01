<?php
/**
 * Class Xdelivery_Model_Db_Table_ProductVariant
 * @package Xdelivery\Model\Db\Table
 */
class Xdelivery_Model_Db_Table_ProductVariant extends Core_Model_Db_Table
{

    protected $_name                    = "xdelivery_products_variants";
    protected $_primary                 = "id";


    /**
     * @param $app_id
     * @param int $limit
     * @return array
     */
    public function getProductVariants($product_id, $params = []) {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "id",
                "product_attribute_id",
                "product_id",
                "attribute_id"
            ]);

			$select->joinLeft(['pa' => 'xdelivery_product_attribute'], 'main.product_attribute_id = pa.id', ['pa.attribute_value_id']);
         
            $select->joinLeft(['a' => 'xdelivery_attributes'], 'a.id = main.attribute_id', ['a.attribute_name']);

            $select->joinLeft(['v' => 'xdelivery_attribute_values'], 'v.id = pa.attribute_value_id', ['v.value_name']);

            $select->joinLeft(['p' => 'xdelivery_products'], 'p.id = main.product_id', ['p.id as varient_product_id','p.parent_id', 'p.price', 'p.special_price', 'p.sku', 'p.special_price_start', 'p.special_price_end', 'p.is_active',
                'p.manage_stock', 'p.qty', 'p.low_stock_threshold', 'p.in_stock']);

            $select->joinLeft(['mainp' => 'xdelivery_products'], 'mainp.id = pa.product_id', ['mainp.id as parent_product_id','mainp.parent_id', 'mainp.product_name', 'mainp.short_description']);

            $select->joinLeft(['img' => 'xdelivery_product_images'], 'img.product_id = p.id', ['img.product_image']);

            $select->joinLeft(['t' => 'xdelivery_taxes'], 't.id = p.tax_id', ['t.tax_rate']);

            $select->where("pa.product_id = ?", $product_id);
            $select->where("pa.is_active = ?", 1);
            $select->where("pa.is_variant = ?", 1);
           
            if (array_key_exists("is_active", $params)) {
                $select->where("p.is_active = ?", $params['is_active']);
            }
            $select->order('p.price ASC');
           
            return $this->toModelClass($this->_db->fetchAll($select))->toArray();
        }


    /**
     * @param $product_id
     * @param int $limit
     * @return array
     */
    public function getProductVariantValues($product_id) {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], []);

            $select->joinLeft(['pa' => 'xdelivery_product_attribute'], 'main.product_attribute_id = pa.id', []);
         
            $select->joinLeft(['a' => 'xdelivery_attributes'], 'a.id = main.attribute_id', ['a.attribute_name']);

            $select->joinLeft(['v' => 'xdelivery_attribute_values'], 'v.id = pa.attribute_value_id', ['v.value_name']);
 
            $select->where("main.product_id = ?", $product_id);            
           
            return $this->toModelClass($this->_db->fetchAll($select))->toArray();
        }



    /**
     * @param $product_id
     * @param int $limit
     * @return array
     */
    public function getProductAttributeById($product_id, $params = []) {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "id as products_variant_id",
            ]);

            $select->joinLeft(['pa' => 'xdelivery_product_attribute'], 'main.product_attribute_id = pa.id', ['pa.attribute_value_id']);

            $select->joinLeft(['p' => 'xdelivery_products'], 'p.id = main.product_id', ['p.id as varient_product_id','p.parent_id', 'p.price', 'p.special_price', 'p.sku', 'p.special_price_start', 'p.special_price_end', 'p.is_active',
                'p.manage_stock', 'p.qty', 'p.low_stock_threshold', 'p.in_stock']);
         
            $select->joinLeft(['a' => 'xdelivery_attributes'], 'a.id = main.attribute_id', ['a.attribute_name']);

            $select->joinLeft(['v' => 'xdelivery_attribute_values'], 'v.id = pa.attribute_value_id', ['v.value_name']);

            $select->joinLeft(['img' => 'xdelivery_product_images'], 'img.product_id = p.id', ['img.product_image']);

            $select->joinLeft(['t' => 'xdelivery_taxes'], 't.id = p.tax_id', ['t.tax_rate']);
    
            $select->where("main.product_id = ?", $product_id);
            $select->where("pa.is_variant = ?", 1);
            $select->where("pa.is_active = ?", 1);
            $select->where("p.is_active = ?", 1);
            return $this->toModelClass($this->_db->fetchAll($select));
        }


}
<?php

use Siberian\Exception;

/**
 * Class Xdelivery_Mobile_ProductController
 */
class Xdelivery_Mobile_ProductController extends Application_Controller_Mobile_Default
{
   /**
     * Fetch All Parent category product
     *
     */
    public function getCategoryByParentIdAction()
    {

        try {

            if($param = $this->getRequest()->getBodyParams()) { 
                $store_id = $param['store_id'];
                
                $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $param['value_id']]);
                $settings = $settingModel->getData();

                $category = (new Xdelivery_Model_Category())->find(['id' => $param['parent_id']]);
                
                $subcategory = (new Xdelivery_Model_Category())->findAll(['value_id' => $param['value_id'], 'parent_id' => $param['parent_id'], 'is_active' => 1 ], 'position ASC')->toArray();

                $sliders = (new Xdelivery_Model_Slider())->findAll(['value_id' => $param['value_id'] , 'category_id' => $param['parent_id'], 'status != ?' => 'deleted'])->toArray();

                $params =   ["limit" => 10,
                            "offset" => $param['offset'],
                            "parent_id" => $param['parent_id'],
                            "is_active" => 1,
                            "store_id" => $param['store_id']
                        ];

                $storeInfo = [];
                $store = (new Xdelivery_Model_Store())->find($store_id);  
                if($store->getStoreId()){
                    $storeInfo = $store->getData();
                }

                $current_date = (new Siberian_Date())->toString("yyyy-MM-dd");
                $products = (new Xdelivery_Model_Products())
                ->findAppByValueId($param['value_id'], $params);
 
                $productsJson = [];
                foreach ($products as $product) {
                    $data = $product->getData();                   

                    /*Stock Manage*/
                    $data['in_stock'] = (integer) $data['in_stock'];
                    if($data['manage_stock'] == "1") {
                        if($data['qty'] == 0){
                            $data['stock'] = p__('xdelivery', 'Out Of Stock');
                            $data['in_stock'] = (integer) "0";
                        }else{
                            $data['in_stock'] = (integer) "1";
                            if($data['qty'] <= $data['low_stock_threshold']){
                                $data['stock'] = p__('xdelivery', 'Low Stock');
                            }else{
                                $data['stock'] = p__('xdelivery', 'In Stock');
                            }
                        }

                    }else{
                        $data['stock'] = $data['in_stock'] == 1 ? p__('xdelivery', 'In Stock') :  p__('xdelivery', 'Out of Stock');
                    }

                    /*End Stock Manage*/
                    $data['selling_price'] = $data['price'];
                    $data['active_special_price'] = 0;
                    if(!empty($data['special_price_start']) && !empty($data['special_price_end'])){
                         if (($current_date >= $data['special_price_start']) && ($current_date <= $data['special_price_end'])){
                            $data['active_special_price'] = 1;
                        }
                    }
     
                    if(empty($data['special_price_start']) && empty($data['special_price_end']) && !empty($data['special_price'])) {
                        $data['active_special_price'] = 1;
                    }                    
                    
                    if($data['active_special_price'] == 1){
                         $data['selling_price'] = $data['special_price'];
                    }
 
                    $data['offer_persent'] = (100 - (( $data['special_price'] * 100 ) / $data['price']));

                    if(is_float($data['offer_persent'])){
                        $data['offer_persent'] = number_format($data['offer_persent'], 2);
                    }

                    if(!empty($data['tax_rate']) && (boolean) $settings['taxes_enable'] && (boolean)$settings['enable_tax_with_product']){
                        $data['price'] = $data['price'] + ($data['price']*$data['tax_rate'])/100; 
                        $data['price'] = (double) number_format($data['price'] , 2, '.', '');
                        $data['special_price'] = $data['special_price'] + ($data['special_price']*$data['tax_rate'])/100; 
                         $data['special_price'] = (double) number_format($data['special_price'] , 2, '.', '');
                    }

                    // Price with currency
                    $currency = Core_Model_Language::getCurrencySymbol();
                    $data['special_price_with_currency'] = Xdelivery_Model_Utility::displayPrice($data['special_price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    $data['price_with_currency'] = Xdelivery_Model_Utility::displayPrice($data['price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    $data['special_price'] = Core_Model_Language::getCurrencySymbol().''.$data['special_price'];
                   $data['price'] = Core_Model_Language::getCurrencySymbol().''.$data['price'];

                    if($data['product_type'] == 'variable'){
                    if(empty($data['max_amount'])){
                            $data['price'] = "-";
                        }else{

                        $data['price_with_currency'] = Xdelivery_Model_Utility::displayPrice($data['min_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']).' - '.Xdelivery_Model_Utility::displayPrice($data['max_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                        }             
                    }
                
                    $data['is_active'] = $data['is_active'] == 1 ? 'Active' : 'InActive';
                    $productsJson[] = $data;
                }

                $customerId = $this->_getCustomerId(false);
                $cart_count = (new Xdelivery_Model_Carts())
                    ->findByDeviceAndUserId($param['value_id'], $customerId, $param['device_uid']);
           
                $payload = [
                        'success' => true,
                        'subcategory' => $subcategory,
                        'category' => $category->getData(),
                        'sliders' => array_values($sliders),
                        'products' => $productsJson,
                        'storeInfo' => $storeInfo,
                        'cart_count' => count($cart_count->toArray())
                    ];
                 
            }

        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }


    /**
     * Fetch All
     *
     */
    public function getProductsByCategoryIdAction()
    {
       try {
            if($param = $this->getRequest()->getBodyParams()) { 
                $current_date = (new Siberian_Date())->toString("yyyy-MM-dd");
                $category = (new Xdelivery_Model_Category())->find(['id' => $param['category_id']]);

                $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $param['value_id']]);
                $settings = $settingModel->getData();

                $storeInfo = [];
                $store = (new Xdelivery_Model_Store())->find($store_id);  
                if($store->getStoreId()){
                    $storeInfo = $store->getData();
                }

                $params =   [
                            "limit" => 10,
                            "offset" => $param['offset'],
                            "parent_id" => $param['category_id'],
                            "is_active" => 1,
                            "store_id" => $param['store_id']
                        ];
                $products = (new Xdelivery_Model_Products())
                ->findAppByValueId($param['value_id'], $params);
 
                $productsJson = [];
                foreach ($products as $product) {
                    $data = $product->getData();
                    
                    /*Stock Manage*/
                    $data['in_stock'] = (integer) $data['in_stock'];
                    if($data['manage_stock'] == "1") {
                        if($data['qty'] == 0){
                            $data['stock'] = p__('xdelivery', 'Out Of Stock');
                            $data['in_stock'] = (integer) "0";
                        }else{
                            $data['in_stock'] = (integer) "1";
                            if($data['qty'] <= $data['low_stock_threshold']){
                                $data['stock'] = p__('xdelivery', 'Low Stock');
                            }else{
                                $data['stock'] = p__('xdelivery', 'In Stock');
                            }
                        }

                    }else{
                        $data['stock'] = $data['in_stock'] == 1 ? p__('xdelivery', 'In Stock') :  p__('xdelivery', 'Out of Stock');
                    }

                    if(!empty($data['tax_rate']) && (boolean) $settings['taxes_enable'] && (boolean)$settings['enable_tax_with_product']){
                        $data['price'] = $data['price'] + ($data['price']*$data['tax_rate'])/100; 
                        $data['special_price'] = $data['special_price'] + ($data['special_price']*$data['tax_rate'])/100; 
                    }

                    /*End Stock Manage*/
                    $data['selling_price'] = $data['price'];
                    $data['active_special_price'] = 0;
                    if(!empty($data['special_price_start']) && !empty($data['special_price_end'])){
                         if (($current_date >= $data['special_price_start']) && ($current_date <= $data['special_price_end'])){
                            $data['active_special_price'] = 1;
                        }
                    }
     
                    if(empty($data['special_price_start']) && empty($data['special_price_end']) && !empty($data['special_price'])) {
                        $data['active_special_price'] = 1;
                    }                    
                    
                    if($data['active_special_price'] == 1){
                         $data['selling_price'] = $data['special_price'];
                    }

                    $data['offer_persent'] = (100 - (( $data['special_price'] * 100 ) / $data['price']));

                    if(is_float($data['offer_persent'])){
                        $data['offer_persent'] = number_format($data['offer_persent'], 2);
                    }

                     // Price with currency
                    $currency = Core_Model_Language::getCurrencySymbol();
                    $data['special_price_with_currency'] = Xdelivery_Model_Utility::displayPrice($data['special_price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);

                    $data['price_with_currency'] = Xdelivery_Model_Utility::displayPrice($data['price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    
                    $data['special_price'] = Core_Model_Language::getCurrencySymbol().''.$data['special_price'];

                    $data['price'] = Core_Model_Language::getCurrencySymbol().''.$data['price'];

                    if($data['product_type'] == 'variable'){
                    if(empty($data['max_amount'])){
                            $data['price'] = "-";
                        }else{
                           $data['price'] = Core_Model_Language::getCurrencySymbol().''.$data['min_amount'].' - '.Core_Model_Language::getCurrencySymbol().''.$data['max_amount'];
                        }             
                    }
                
                    $data['is_active'] = $data['is_active'] == 1 ? 'Active' : 'InActive';

                    $productsJson[] = $data;
                }
           
                $payload = [
                        'success' => true,
                        'category' => $category->getData(),
                        'products' => $productsJson,
                        'storeInfo' => $storeInfo
                    ];
                 
            }

        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }

    /**
     * Fetch All
     *
     */
    public function loadProductsAction()
    {
       try {
            if($param = $this->getRequest()->getBodyParams()) { 
                $current_date = (new Siberian_Date())->toString("yyyy-MM-dd");

                $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $param['value_id']]);
                $settings = $settingModel->getData();

                $params =   [
                            "limit" => 10,
                            "offset" => $param['offset'],
                            "search" => $param['search'],
                            "is_active" => 1,
                            "store_id" => $param['store_id']
                        ];
              
               $products = (new Xdelivery_Model_Products())
                ->findAppByValueId($param['value_id'], $params);
 
                $productsJson = [];
                foreach ($products as $product) {
                    $data = $product->getData();
                    
                    /*Stock Manage*/
                    $data['in_stock'] = (integer) $data['in_stock'];
                    if($data['manage_stock'] == "1") {
                        if($data['qty'] == 0){
                            $data['stock'] = p__('xdelivery', 'Out Of Stock');
                            $data['in_stock'] = (integer) "0";
                        }else{
                            $data['in_stock'] = (integer) "1";
                            if($data['qty'] <= $data['low_stock_threshold']){
                                $data['stock'] = p__('xdelivery', 'Low Stock');
                            }else{
                                $data['stock'] = p__('xdelivery', 'In Stock');
                            }
                        }

                    }else{
                        $data['stock'] = $data['in_stock'] == 1 ? p__('xdelivery', 'In Stock') :  p__('xdelivery', 'Out of Stock');
                    }
                    /*End Stock Manage*/
                    $data['selling_price'] = $data['price'];
                    $data['active_special_price'] = 0;
                    if(!empty($data['special_price_start']) && !empty($data['special_price_end'])){
                         if (($current_date >= $data['special_price_start']) && ($current_date <= $data['special_price_end'])){
                            $data['active_special_price'] = 1;
                        }
                    }
     
                    if(empty($data['special_price_start']) && empty($data['special_price_end']) && !empty($data['special_price'])) {
                        $data['active_special_price'] = 1;
                    }                    
                    
                    if($data['active_special_price'] == 1){
                         $data['selling_price'] = $data['special_price'];
                    }

                    $data['offer_persent'] = (100 - (( $data['special_price'] * 100 ) / $data['price']));

                    if(is_float($data['offer_persent'])){
                        $data['offer_persent'] = number_format($data['offer_persent'], 2);
                    }

                    if(!empty($data['tax_rate']) && (boolean) $settings['taxes_enable'] && (boolean)$settings['enable_tax_with_product']){
                        $data['price'] = $data['price'] + ($data['price']*$data['tax_rate'])/100; 
                        $data['special_price'] = $data['special_price'] + ($data['special_price']*$data['tax_rate'])/100; 
                    }

                    // Price with currency
                    $currency = Core_Model_Language::getCurrencySymbol();
                    $data['special_price_with_currency'] = Xdelivery_Model_Utility::displayPrice($data['special_price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);

                    $data['price_with_currency'] = Xdelivery_Model_Utility::displayPrice($data['price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    
                    $data['special_price'] = Core_Model_Language::getCurrencySymbol().''.$data['special_price'];

                    $data['price'] = Core_Model_Language::getCurrencySymbol().''.$data['price'];

                    if($data['product_type'] == 'variable'){
                        if(empty($data['max_amount'])){
                            $data['price'] = "-";
                        }else{
                           $data['price'] = Xdelivery_Model_Utility::displayPrice($data['min_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']).' - '.Xdelivery_Model_Utility::displayPrice($data['max_amount'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                        }             
                    }
                
                    $data['is_active'] = $data['is_active'] == 1 ? 'Active' : 'InActive';

                    $productsJson[] = $data;
                }
           
                $payload = [
                        'success' => true,                         
                        'products' => $productsJson
                    ];
                 
            }

        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }

    
   /**
     * find By Product Id All
     *
     */
    public function findByProductIdAction() {
       
        try {

            if($param = $this->getRequest()->getBodyParams()) {
                $product_id = $param['product_id'];

                $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $param['value_id']]);
                $settings = $settingModel->getData();
               
                /* Product By Id */
                $productModal = (new Xdelivery_Model_Products()); 
                $productModal->find($product_id); 
                $product = $productModal->getData();

                $modelTax = new Xdelivery_Model_Tax();
                $modelTax->find($product['tax_id']);
                if($modelTax->getId()) {
                    $product['tax_rate'] = $modelTax->getTaxRate();
                }
                
                /* Product Images */
                $productImage = (new Xdelivery_Model_ProductImages())->find(['product_id' => $product_id, 'is_base' => 1]);
                $product['image'] = $productImage->getProductImage();
                $productImagegallery = (new Xdelivery_Model_ProductImages())->findAll(['product_id' => $product_id]);
                $product['gallery'] = $productImagegallery->toArray();

                $product['currency'] = Core_Model_Language::getCurrencySymbol();

                $product_attribute = (new Xdelivery_Model_ProductAttribute())->getProductAttribute($product_id);          
                    $attributes = array();

                    foreach ($product_attribute as $key => $value) {
                        $attributes[$value['attribute_id']]['attribute_id'] = (integer) $value['attribute_id'];
                        $attributes[$value['attribute_id']]['attribute_name'] = $value['attribute_name'];
                        $attributes[$value['attribute_id']]['values'][] = ['id' => (integer) $value['id'], 'value_name' => $value['value_name'], 'attribute_value_id' => (integer) $value['attribute_value_id']];
                    } 
                $product['attributes'] = array_values($attributes);

                 /* Additional Options */
                $product['options'] = (new Xdelivery_Model_ExtraOptionProduct())->getProductOptions($product_id, ['is_active' => 1]);

                /* Change according to varients from here*/
                $product['is_multiple_options'] = false;
                if($product['product_type'] == 'variable'){
                    $product['varients'] = array_values($this->_getVarients($product_id));
                    $product['is_multiple_options'] = (boolean) count($product['varients']);
                    $product['selected_product_id'] = $product['id'];
                    
                    if($product['is_multiple_options']){
                       $product['is_varient_product'] = true;
                       $product['parent_product_id'] = $product['varients'][0]['parent_product_id'];
                        $product['selected_product_id'] = $product['varients'][0]['varient_product_id'];

                        $product['active_special_price'] = $product['varients'][0]['active_special_price'];
                        $product['in_stock'] = $product['varients'][0]['in_stock'];
                        $product['price'] = $product['varients'][0]['price'];
                        $product['selling_price'] = $product['varients'][0]['selling_price'];
                        $product['low_stock_threshold'] = $product['varients'][0]['low_stock_threshold'];
                        $product['manage_stock'] = $product['varients'][0]['manage_stock'];                        
                        $product['product_image'] = !empty($product['varients'][0]['product_image']) ? $product['varients'][0]['product_image'] : $product['product_image'];
                        $product['special_price'] = $product['varients'][0]['special_price'];
                        $product['special_price_end'] = $product['varients'][0]['special_price_end'];
                        $product['special_price_start'] = $product['varients'][0]['special_price_start'];
                        $product['sku'] = $product['varients'][0]['sku'];
                        $product['tax_rate'] = $product['varients'][0]['tax_rate'];

                         if(!empty($product['tax_rate']) && (boolean) $settings['taxes_enable'] && (boolean)$settings['enable_tax_with_product']){
                            $product['price'] = $product['price'] + ($product['price']*$product['tax_rate'])/100; 
                            $product['price'] = (double) number_format($product['price'] , 2, '.', '');

                            $product['special_price'] = $product['special_price'] + ($product['special_price']*$product['tax_rate'])/100;
                            $product['special_price'] = (double) number_format($product['special_price'] , 2, '.', '');
                            $product['selling_price'] = $product['selling_price'] + ($product['selling_price']*$product['tax_rate'])/100; 
                            $product['selling_price'] = (double) number_format($product['selling_price'] , 2, '.', '');
                        }

                        // Price with currency
                        $currency = Core_Model_Language::getCurrencySymbol();
                        $product['special_price_with_currency'] = Xdelivery_Model_Utility::displayPrice($product['special_price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                        $product['price_with_currency'] = Xdelivery_Model_Utility::displayPrice($product['price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);

                    }

                    $currency = Core_Model_Language::getCurrencySymbol();
                    
                    $varientsJson = [];
                    foreach ($product['varients'] as $key => $value) {
                        if(!empty($value['tax_rate']) && (boolean) $settings['taxes_enable'] && (boolean)$settings['enable_tax_with_product']){
                            $value['price'] = $value['price'] + ($value['price']*$value['tax_rate'])/100; 
                            $value['price'] = (double) number_format($value['price'] , 2, '.', '');
                            $value['special_price'] = $value['special_price'] + ($value['special_price']*$value['tax_rate'])/100; 
                            $value['special_price'] = (double) number_format($value['special_price'] , 2, '.', '');
                            $value['selling_price'] = $value['selling_price'] + ($value['selling_price']*$value['tax_rate'])/100; 
                            $value['selling_price'] = (double) number_format($value['selling_price'] , 2, '.', '');
                        }
                        $value['special_price_with_currency'] = Xdelivery_Model_Utility::displayPrice($value['special_price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);

                        $value['price_with_currency'] = Xdelivery_Model_Utility::displayPrice($value['price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);

                        $varientsJson[] = $value;
                    }
                    $product['varients'] = $varientsJson;
                }else{

                    if(!empty($product['tax_rate']) && (boolean) $settings['taxes_enable'] && (boolean)$settings['enable_tax_with_product']){
                        $product['price'] = $product['price'] + ($product['price']*$product['tax_rate'])/100;
                        $product['price'] = (double) number_format($product['price'] , 2, '.', ''); 
                        $product['special_price'] = $product['special_price'] + ($product['special_price']*$product['tax_rate'])/100; 
                        $product['special_price'] = (double) number_format($product['special_price'] , 2, '.', '');
                        $product['selling_price'] = $product['selling_price'] + ($product['selling_price']*$product['tax_rate'])/100; 
                        $product['selling_price'] = (double) number_format($product['selling_price'] , 2, '.', '');
                    }

                    // Price with currency
                        $currency = Core_Model_Language::getCurrencySymbol();
                        $product['special_price_with_currency'] = Xdelivery_Model_Utility::displayPrice($product['special_price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                        $product['price_with_currency'] = Xdelivery_Model_Utility::displayPrice($product['price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                }              

                /*Stock status*/                
                $stockValues = $this->_getStockStatus($product);
                $product = array_merge($product, $stockValues);

                /*Price*/
                $pricevalues = $this->_getPriceStatus($product);
                $product = array_merge($product, $pricevalues);
  
                /* Is favorite*/
                $favoriteModel = (new Xdelivery_Model_Favorites())->find(['device_uid'=> $param["device_uid"], 'product_id' => $product['id']]);
                $product['is_save'] = !empty($favoriteModel->getId()) ? 1 : 0;
                $product['in_cart'] = 0;
                /* Is Cart */
                /* $cartModel = (new Xdelivery_Model_Carts())->find(['device_uid'=>                     $param["device_uid"], 'product_id' => $product['id']]);
                $product['in_cart'] = !empty($cartModel->getId()) ? 1 : 0;
                */

                $brand = (new Xdelivery_Model_Brand())->find(['brand_id'=> $product['brand_id']]);
                $product['brand_name'] = $brand->getName();
                $product['brand_desc'] = $brand->getDescription();
                $product['brand_image'] = $brand->getImage();
                $product['brand_id'] = (integer) $brand->getBrandId();

                $customerId = $this->_getCustomerId(false);
                $cart_count = (new Xdelivery_Model_Carts())
                    ->findByDeviceAndUserId($param['value_id'], $customerId, $param['device_uid']);
                $product['cart_count'] = (int)count($cart_count->toArray());

                $payload = [
                    'success' => true,
                    'product' => $product,
                    'product_id' => $product['id']
               ];
            }else{
                
                $payload = [
                    "error" => true,
                    "message" => p__('xdelivery', 'Param required!')
                ];
            }

        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }


    /**
     * options values
     *
     */
    public function findOptionsValuesByIdAction()
    {
        try {
            if($param = $this->getRequest()->getBodyParams()){
                $option_id = $param['option_id'];                
                $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $param['value_id']]);
                $settings = $settingModel->getData();

                $optionValues = (new Xdelivery_Model_ExtraOptionValue())
                    ->findAll(['option_id' => $option_id, 'is_active = ?' => 1 ], 'position ASC')->toArray();

                $currency = Core_Model_Language::getCurrencySymbol();
                $optionsJson = [];              
                foreach ($optionValues as $key => $value) {
                    $value['price_with_currency'] = Xdelivery_Model_Utility::displayPrice($value['price'], $currency, $settings['number_of_decimals'], $settings['decimal_separator'], $settings['thousand_separator'], $settings['currency_position']);
                    $optionsJson[] = $value;
                }

                $payload = [
                        'success' => true,
                        'values' => $optionsJson,
                ];
                 
            }

        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }
    

    private function _getVarients($product_id){

        $varients = [];

        $varientsData = (new Xdelivery_Model_ProductVariant())->getProductVariants($product_id, ['is_active' => 1]);
           
        foreach ($varientsData as $key => $value) {
                $varients[$value['product_id']]['parent_product_id'] = $value['parent_product_id'];
                $varients[$value['product_id']]['price'] = $value['price'];
                $varients[$value['product_id']]['varient_product_id'] = $value['varient_product_id'];
                $varients[$value['product_id']]['tax_rate'] = $value['tax_rate']; 
                $varients[$value['product_id']]['product_image'] = $value['product_image'];
                $varients[$value['product_id']]['special_price'] = $value['special_price'];
                $varients[$value['product_id']]['sku'] = $value['sku'];
                $varients[$value['product_id']]['special_price_start'] = $value['special_price_start'];
                $varients[$value['product_id']]['special_price_end'] = $value['special_price_end'];
                $varients[$value['product_id']]['is_active'] = $value['is_active'];
                $varients[$value['product_id']]['manage_stock'] = $value['manage_stock'];
                $varients[$value['product_id']]['qty'] = $value['qty'];
                $varients[$value['product_id']]['low_stock_threshold'] = $value['low_stock_threshold'];
                $varients[$value['product_id']]['in_stock'] = $value['in_stock'];

                $current_date = (new Siberian_Date())->toString("yyyy-MM-dd"); 
          
                $varients[$value['product_id']]['selling_price'] = $value['price'];               
                $varients[$value['product_id']]['active_special_price'] = 0;

                if(!empty($value['special_price_start']) && !empty($value['special_price_end'])){
                     if (($current_date >= $value['special_price_start']) && ($current_date <= $value['special_price_end'])){
                        $varients[$value['product_id']]['active_special_price'] = 1;
                    }
                }

                if(empty($value['special_price_start']) && empty($value['special_price_end']) && !empty($value['special_price'])) {
                    $varients[$value['product_id']]['active_special_price'] = 1;
                }               
               

                if($varients[$value['product_id']]['active_special_price'] == 1){
                     $varients[$value['product_id']]['selling_price'] = $value['special_price'];
                     $varients[$value['product_id']]['offer_persent'] = (100 - (( $value['special_price'] * 100 ) / $value['price']));

                     if(is_float($varients[$value['product_id']]['offer_persent'])){
                        $varients[$value['product_id']]['offer_persent'] = number_format($varients[$value['product_id']]['offer_persent'], 2);
                     }
                }

 
                $v = [];
                $v['attribute_name'] = $value['attribute_name'];
                $v['value_name'] = $value['value_name']; 
                $varients[$value['product_id']]['values'][] = $v;
        }

        return $varients;
    }


    private function _getPriceStatus($product){

        $payload = [];
        $current_date = (new Siberian_Date())->toString("yyyy-MM-dd"); 
          
        $payload['selling_price'] = $product['price'];                
        $payload['active_special_price'] = 0;

        if(!empty($product['special_price_start']) && !empty($product['special_price_end'])){
             if (($current_date >= $product['special_price_start']) && ($current_date <= $product['special_price_end'])){
                $payload['active_special_price'] = 1;
            }
        }

        if(empty($product['special_price_start']) && empty($product['special_price_end']) && !empty($product['special_price'])) {
            $payload['active_special_price'] = 1;
        }               
       

        if($payload['active_special_price'] == 1){
             $payload['selling_price'] = $product['special_price'];
             $payload['offer_persent'] = (100 - (( $product['special_price'] * 100 ) / $product['price']));

             if(is_float($payload['offer_persent'])){
                $payload['offer_persent'] = number_format($payload['offer_persent'], 2);
             }
        }

        return $payload;

    }


    private function _getStockStatus($product){
            $payload = [];

            $payload['in_stock'] = (integer) $product['in_stock'];
          
            if($product['manage_stock'] == "1"){
                
                if($product['qty'] == 0){
                   $payload['stock']  = p__('xdelivery', 'Out Of Stock');
                   $payload['in_stock'] = 0;
                }else{
                    if($product['qty'] <= $product['low_stock_threshold']){
                        $payload['stock']  = p__('xdelivery', 'Low Stock');
                        $payload['in_stock'] = 1;
                    }else{
                        $payload['stock']  = p__('xdelivery', 'In Stock');
                        $payload['in_stock'] = 1;
                    }
                }

            }else{
                $payload['stock']  = $product['in_stock'] == 1 ? p__('xdelivery', 'In Stock') :  p__('xdelivery', 'Out of Stock');
            }

        return $payload;
    }

    
    /**
     * @param bool $throw
     * @return mixed|null
     * @throws Exception
     * @throws Zend_Session_Exception
     */
    private function _getCustomerId($throw = true)
    {
        $request = $this->getRequest();
        $session = $this->getSession();
        $customerId = $session->getCustomerId();
        if ($throw && empty($customerId)) {
            throw new Exception(p__('xdelivery', 'Customer login required!'));
        }
        return $customerId;
    }

  
}

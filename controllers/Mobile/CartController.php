<?php

use Siberian\Exception;

/**
 * Class Xdelivery_Mobile_CartController
 */
class Xdelivery_Mobile_CartController extends Application_Controller_Mobile_Default
{

    public function _getCart($param){

        $customerId = $this->_getCustomerId(false);
        $currency = Core_Model_Language::getCurrencySymbol();
        $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $param['value_id']]);
        $settingsData = $settingModel->getData();

        $settings = ['auto_approval_review' => (boolean) $settingsData['auto_approval_review'],
        'enable_acceptance_rejection' => (boolean) $settingsData['enable_acceptance_rejection'],
        'enable_booking' => (boolean) $settingsData['enable_booking'],
        'enable_tax' => (boolean) $settingsData['enable_tax'],
        'hide_price' => (boolean) $settingsData['hide_price'],
        'review_rating_enable' => (boolean) $settingsData['review_rating_enable'],
        'send_invoice_email' => (boolean) $settingsData['send_invoice_email'],
        'max_qty_per_product' => (integer) $settingsData['max_qty_per_product'],
        'max_qty_shopping_cart' => (integer) $settingsData['max_qty_shopping_cart'],
        'min_order_value' => (integer) $settingsData['min_order_value'],
        'min_qty_shopping_cart' => (integer) $settingsData['min_qty_shopping_cart'],
        'delivery_time' => (integer) $settingsData['delivery_time'],
        'pick_up_time' => (integer) $settingsData['pick_up_time'],
        'max_order_value' => (double) $settingsData['max_order_value'],
        'category_design' => (string) $settingsData['category_design'],
        'product_design' => (string) $settingsData['product_design'],
        'home_screen' => (string) $settingsData['home_screen'],
        'delivery_cost' => (double) $settingsData['delivery_cost'],
       ];

        $products = (new Xdelivery_Model_Carts())
        ->findByDeviceAndUserId($param['value_id'], $customerId, $param['device_uid']);
    
        $cart_products = [];
        $total_amount = (double) 0;
        $total_item = (integer) 0;
        $total_tax_amount = (double) 0;
        $total_vat_tax_amount = (double) 0;
        $in_out_of_stock = (integer) 0;
        $current_date = (new Siberian_Date())->toString("yyyy-MM-dd");        
        foreach ($products as $product) {
            $data = $product->getData();
            $total_item+=(integer) $data['qty'];
            $optionsAray = [];
            $option_amount = 0;
            $option_vat_amount = 0;
            $data['optionsjson'] = json_decode($data['optionsjson'], TRUE);

            foreach ($data['optionsjson'] as $key => $value) {
              $optionsAray[] = $value['name'];
              if(!empty($data['tax_rate']) && (boolean) $settingsData['taxes_enable'] && (boolean)$settingsData['enable_tax_with_product'] && (boolean) $settingsData['taxes_to_addons']){
                $option_vat_amount+=($value['price']*$data['tax_rate'])/100;          
                $value['price'] = $value['price'] + ($value['price']*$data['tax_rate'])/100;
              }
              $option_amount  =  $option_amount + (double)$value['price'];
            }
            if ($option_vat_amount > 0) {                
                $total_vat_tax_amount+=$option_vat_amount*$data['qty'];
            }
            $data['option_amount'] = (double) $option_amount;
            $data['options'] = implode(", ",$optionsAray);
            $child_product_id = (integer) $data['child_product_id'];
            $data['cart_qty'] = (integer) $data['qty'];
          
            if($child_product_id > 0) {
                // get child product info
            $productVarientInfo = (new Xdelivery_Model_ProductVariant())->getProductAttributeById($child_product_id);
           
            $data['varients'] = $productVarientInfo->toArray(); 
            if(count($productVarientInfo->toArray())){
               $data['is_varient_product'] = true;
               $data['parent_product_id'] = $data['varients'][0]['parent_product_id'];
                $data['selected_product_id'] = $data['varients'][0]['varient_product_id'];

                $data['active_special_price'] = $data['varients'][0]['active_special_price'];
                $data['in_stock'] = $data['varients'][0]['in_stock'];
                $data['price'] = $data['varients'][0]['price'];
                $data['selling_price'] = (double) $data['varients'][0]['selling_price'];
                $data['low_stock_threshold'] = $data['varients'][0]['low_stock_threshold'];
                $data['manage_stock'] = $data['varients'][0]['manage_stock'];                        
                $data['product_image'] = !empty($data['varients'][0]['product_image']) ? $data['varients'][0]['product_image'] : $data['product_image'];
                $data['special_price'] = $data['varients'][0]['special_price'];
                $data['special_price_end'] = $data['varients'][0]['special_price_end'];
                $data['special_price_start'] = $data['varients'][0]['special_price_start'];
                $data['sku'] = $data['varients'][0]['sku'];
                $data['tax_rate'] = $data['varients'][0]['tax_rate'];
              }
           }

            $stockValues = $this->_getStockStatus($data);
            $data = array_merge($data, $stockValues);

            /*Price*/
            $pricevalues = $this->_getPriceStatus($data);
            $data = array_merge($data, $pricevalues);      
             
            if(!empty($data['tax_rate']) && (boolean) $settingsData['taxes_enable'] && (boolean)$settingsData['enable_tax_with_product']){
                $data['price'] = $data['price'] + ($data['price']*$data['tax_rate'])/100;
                $data['price'] = (double) number_format($data['price'] , 2, '.', ''); 
                $data['special_price'] = $data['special_price'] + ($data['special_price']*$data['tax_rate'])/100;
                $data['special_price'] = (double) number_format($data['special_price'] , 2, '.', ''); 
                $total_vat_tax_amount+=($data['cart_qty'])*(($data['selling_price']*$data['tax_rate'])/100);
                $data['selling_price'] = $data['selling_price'] + ($data['selling_price']*$data['tax_rate'])/100; 
                $data['selling_price'] = (double) number_format($data['selling_price'] , 2, '.', '');
            }

            /*Total values*/
            $data['sub_total'] = ($data['selling_price'] * $data['cart_qty']);
            $data['sub_options_total'] = ($data['option_amount'] * $data['cart_qty']);
            $data['sub_total'] =  $data['sub_total'] + $data['sub_options_total'];
            $total_amount = (double) $total_amount + $data['sub_total'];

            $data['sub_options_total_with_currency'] = Xdelivery_Model_Utility::displayPrice($data['sub_options_total'], $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']);
            $data['price_with_currency'] = Xdelivery_Model_Utility::displayPrice($data['price']*$data['cart_qty'], $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']);
            $data['special_price_with_currency'] = Xdelivery_Model_Utility::displayPrice($data['special_price']*$data['cart_qty'], $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']);

            $data['tax_amount'] = 0;
            
            if(!empty($data['tax_rate']) && (boolean) $settingsData['taxes_enable'] && (boolean) !$settingsData['enable_tax_with_product']){
                if((boolean) $settingsData['taxes_to_addons']){
                    $tax_amount = (($data['selling_price'] + $data['option_amount'])*$data['tax_rate'])/100;
                    $tax_amount = (double) number_format($tax_amount , 2, '.', '');  
               
                }else{
                    $tax_amount = ($data['selling_price']*$data['tax_rate'])/100;
                    $tax_amount = (double) number_format($tax_amount , 2, '.', ''); 
               
                }
               
                $data['tax_amount'] = $data['cart_qty']*$tax_amount;
                $total_tax_amount = (double) $total_tax_amount + $data['tax_amount']; 
            }
            /*End Total*/

            if(!$data['in_stock'] || ($data['is_active'] != 1)) {
                $in_out_of_stock++;
            }

            $cart_products[] = $data;
        }

        $delivery_cost = $settings['delivery_cost'];
        if((boolean) $settingsData['taxes_enable'] && (boolean)$settingsData['enable_tax_with_product']){ //Vat or Product with tax is enabled
            $total_tax_amount = $total_vat_tax_amount;
            if ((boolean) $settingsData['discount_tax_calculation']==1) {                
                $total_amount= $total_amount - $total_vat_tax_amount;            
            }
        }
        $final_amount = $total_amount +  $total_tax_amount;  
        if(count($cart_products)){
            $store_id = (integer) $cart_products[0]['store_id'];
        } 
        $sub_amount_with_vat = ((boolean)$settingsData['enable_tax_with_product']) ? $total_amount+$total_vat_tax_amount: 0 ;

        $payload = [
            'success' => true,
            'store_id' => $store_id,
            'settings' => $settings,
            'settingsData' => $settingsData,
            'cart_products' => $cart_products,
            'discount_amount' => 0,            
            'tips_amount' => '',
            'is_discount' => false,
            'promocode_id' => 0,               
            'sub_amount' => (double) number_format($total_amount, 2, '.', ''),
            'sub_amount_with_vat' => (double) number_format($sub_amount_with_vat, 2, '.', ''),
            'total_amount' => (double) number_format($final_amount, 2, '.', ''),
            'total_item' => $total_item,
            'delivery_cost' => (double) number_format($delivery_cost, 2, '.', ''),
            'currency' => Core_Model_Language::getCurrencySymbol(),
            'in_out_of_stock' => $in_out_of_stock,
            'total_vat_tax_amount' => (double) number_format($total_vat_tax_amount, 2, '.', ''),
            'total_tax_amount' => (double) number_format($total_tax_amount, 2, '.', ''),
            'sub_amount_with_currency' =>  Xdelivery_Model_Utility::displayPrice($total_amount, $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']),
            'sub_amount_with_vat_with_currency' =>  Xdelivery_Model_Utility::displayPrice($sub_amount_with_vat, $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']),
            'total_amount_with_currency' =>  Xdelivery_Model_Utility::displayPrice($final_amount, $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']),
            'total_tax_amount_with_currency' =>  Xdelivery_Model_Utility::displayPrice($total_tax_amount, $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']),
            'delivery_cost_with_currency' =>  Xdelivery_Model_Utility::displayPrice($delivery_cost, $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']),
            'total_discount_amount_with_currency' =>  Xdelivery_Model_Utility::displayPrice(0, $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']),
            'total_tips_amount_with_currency' =>  Xdelivery_Model_Utility::displayPrice(0, $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position'])                      
        ];

        return $payload;
    }
    /**
     * Fetch All
     *
     */
    public function findAllAction() {

        try {

            if($param = $this->getRequest()->getBodyParams()){
                // dd($param);
                $payload = $this->_getCart($param);
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
     * Apply discont code
     *
     */
    public function applypromoAction() {
   
        try {

            if($param = $this->getRequest()->getBodyParams()){ 
                $discount_code = $param['discount_code'];
                $currency = Core_Model_Language::getCurrencySymbol();
                $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $param['value_id']]);
                $settingsData = $settingModel->getData();
                if ($param['call_type']=='remove') {
                    unset($param['discount_code']);
                 }
                $cart = $this->_getCart($param);       
                 $payload=[];
                // Coupon code calculations
                if (isset($param['discount_code']) && !empty($param['discount_code'])) {
                    $model = (new Xdelivery_Model_Coupons())->find(['coupon_code' => $discount_code, 'value_id' => $param['value_id'], 'is_delete' => 0 ]);
                    if($model->getId()){

                        $currentDate = strtotime(date('Y/m/d H:i:s'));
                        $validFrom = strtotime($model->getStartDate());
                        $validUntil = strtotime($model->getEndDate());

                        if(($currentDate >= $validFrom) && ($currentDate <= $validUntil)) {
                            if($model->getMinSpend() <= $cart['total_amount']){
                            if($model->getMaxSpend() >= $cart['total_amount']){

                                $customerId = $this->_getCustomerId(false);
                                $promoHistory = (new Xdelivery_Model_Orders())->findAll(['promocode_id' => $model->getId()])->toArray();
                                
                                if(count($promoHistory) >= $model->getUsageLimitPerCoupon()){
                                    throw new Exception(p__('xdelivery', 'This Coupon code has expired!'));
                                }

                                $customerHistory = (new Xdelivery_Model_Orders())->findAll(['customer_id' => $customerId, 'promocode_id' => $model->getId()])->toArray();
                                
                                if(count($customerHistory) >= $model->getUsageLimitPerCustomer()){
                                    throw new Exception(p__('xdelivery', 'You have already used this Coupon code!'));
                                }
                            
                                //Apply Succesfully                          
                                $cart['promocode_id'] = $model->getId();
                                $cart['discount_code'] = $discount_code;
                                $cart['discount_amount'] = $model->getDiscountValue();

                                /*calculate discount*/
                                if ($settingsData['discount_tax_calculation']==1) {  //Apply Discount before Tax                             
                                    if($model->getDiscountType() == 'percent'){ //If Discount is before Tax calculate tax on discounted amount
                                        $tax_percenteage=($cart['total_tax_amount'] / $cart['sub_amount']) * 100;                                        
                                        $cart['discount_amount'] = ($cart['sub_amount'] * $model->getDiscountValue()) /100;
                                        $discountedAmount=$cart['sub_amount']-$cart['discount_amount'];
                                        //calculate tax on discounted amount
                                        $cart['total_tax_amount']=($discountedAmount * $tax_percenteage) / 100;                                        
                                        $cart['total_amount'] = $cart['sub_amount'] + $cart['total_tax_amount'];
                                        $cart['discounted_amount'] = ($settingsData['enable_tax_with_product']) ? $discountedAmount+$cart['total_tax_amount'] : $discountedAmount ; ;
                                    }
                                }else { //Apply Discount after Tax
                                    if($model->getDiscountType() == 'percent'){
                                        $discountedAmount = ($cart['sub_amount'] + $cart['total_tax_amount']) * $model->getDiscountValue() / 100;
                                        $cart['discount_amount'] = $discountedAmount;
                                        $cart['discounted_amount'] = $cart['sub_amount']+ $cart['total_tax_amount'] - $discountedAmount;
                                    }
                                }

                                $cart['total_amount'] = $cart['total_amount'] - $cart['discount_amount'];
                                
                                $cart['discounted_amount_with_currency'] =  Xdelivery_Model_Utility::displayPrice($cart['discounted_amount'], $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']); 
                                $cart['discounted_amount_with_vat_with_currency'] =  Xdelivery_Model_Utility::displayPrice(($cart['discounted_amount']), $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']); 
                                $cart['total_discount_amount_with_currency'] =  Xdelivery_Model_Utility::displayPrice($cart['discount_amount'], $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']); 
                                $cart['total_tax_amount_with_currency'] =  Xdelivery_Model_Utility::displayPrice($cart['total_tax_amount'], $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']); 

                                $cart['total_amount_with_currency'] =  Xdelivery_Model_Utility::displayPrice($cart['total_amount'], $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']);

                                $cart['is_discount'] = true;
                                $payload = [
                                    'success' => true,
                                    'status' => true,
                                    'cart' => $cart,
                                    'message' =>p__('xdelivery', '%s Coupon code applied successfully.', $discount_code), 
                                ]; 
                               
                            }else{
                                $payload = [
                                    'success' => true,
                                    'status' => false,
                                    'message' => p__('xdelivery', 'Maximum amount must be %s%s or less', $currency , $model->getMaxSpend()), 
                                ];
                            }
                            }else{
                                $payload = [
                                    'success' => true,
                                    'status' => false,
                                    'message' => p__('xdelivery', 'Minimum amount must be %s%s or higher', $currency , $model->getMinSpend()), 
                                ];
                            }
                            }else{
                                $payload = [
                                    'success' => true,
                                    'status' => false,
                                    'message' => p__('xdelivery', 'Sorry, Coupon code is either expired or not active yet!'), 
                                ];
                            }

                        }else{
                            $payload = [
                                    'success' => true,
                                    'status' => false,
                                    'message' => p__('xdelivery', 'Please enter a valid Coupon code!'), 
                                ]; 
                        }   
                    } else {
                        
                        $payload = [
                            'success' => true,
                            'status' => false,
                            'message' => p__('xdelivery', 'Please enter a valid Coupon code!'), 
                        ]; 
                    }
                if($param['call_type']=='tip' || isset($param['tips_amount']) ){
                    $cart['tips_amount'] = (double) $param['tips_amount'];
                    $cart['total_amount'] = $cart['total_amount'] + $cart['tips_amount'];
                    $cart['total_tips_amount_with_currency'] =  Xdelivery_Model_Utility::displayPrice($cart['tips_amount'], $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']); 
                    $cart['total_amount_with_currency'] =  Xdelivery_Model_Utility::displayPrice($cart['total_amount'], $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']);
                    if (empty($param['tips_amount'])) {
                        $cart['tips_amount']='';
                    }                    
                     
                } 
                if($param['call_type']=='remove'){
                    $payload = [
                        'success' => true,
                        'status' => true,
                        'cart' => $cart,
                        'message' =>p__('xdelivery', 'Coupon code removed!'), 
                        ];    
                }else if($param['call_type']=='discount'){
                    if (empty($payload)) {
                        $payload = [
                            'success' => true,
                            'status' => true,                            
                            'cart' => $cart,
                            'message' =>p__('xdelivery', 'Coupon code successfully applied!'), 
                            ];
                    }    
                }else if($param['call_type']=='tip'){
                    $payload = [
                        'success' => true,
                        'status' => true,
                        'cart' => $cart,
                        'message' =>p__('xdelivery', 'Gratuity successfully applied!'), 
                        ];   
                }    
                    
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
     * Apply discont code
     *
     */
    public function applytipsAction() {
   
        try {

            if($param = $this->getRequest()->getBodyParams()){ 

                $currency = Core_Model_Language::getCurrencySymbol();
                $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $param['value_id']]);
                $settingsData = $settingModel->getData();

            
                $cart = $this->_getCart($param);
                $cart['tips_amount'] = (double) $param['tips_amount'];
                $cart['total_amount'] = $cart['total_amount'] + $cart['tips_amount'];
                
                $cart['total_tips_amount_with_currency'] =  Xdelivery_Model_Utility::displayPrice($cart['tips_amount'], $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']); 
                $cart['total_amount_with_currency'] =  Xdelivery_Model_Utility::displayPrice($cart['total_amount'], $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']);

                $payload = [
                    'success' => true,
                    'status' => true,
                    'cart' => $cart,
                    'message' =>p__('xdelivery', 'Gratuity applied successfully.'), 
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
     * Save For Later
     *
     */
    public function saveForLaterAction() {
        try {
         
            if($param = $this->getRequest()->getBodyParams()) {

                $customerId = $this->_getCustomerId(false);
                $favoriteModel = (new Xdelivery_Model_Favorites())->find(['device_uid'=> $param["device_uid"], 'product_id' => $param['product_id']]);
                
                if(!$favoriteModel->getId()) {
                   $favoriteModel->setValueId($param["value_id"])
                        ->setCustomerId($customerId)
                        ->setDeviceUid($param["device_uid"])
                        ->setProductId($param["product_id"])
                        ->save();
                }else{
                      $favoriteModel->delete();
                }
                
                
                $payload = [
                    'success' => true,
                    'message' => p__('xdelivery', 'Info successfully Saved')
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
     * Add to cart
     *
     */
    public function addToCartAction() {
        try {
         
           if($value_id = $this->getRequest()->getParam('value_id')){
                $param = $this->getRequest()->getBodyParams();
                $customerId = $this->_getCustomerId(false);
               
               /* $cartModel = (new Xdelivery_Model_Carts())->find(['device_uid'=> $param["device_uid"], 'product_id' => $param['product_id'],  'child_product_id' => $param['child_product_id']]);*/
                // dd($param["id"], $param);
                // $cartModel = (new Xdelivery_Model_Carts())->find(['id'=> $param["id"]]);                
                $cartModel = new Xdelivery_Model_Carts();                

                $cartModel->setValueId($value_id)
                        ->setCustomerId($customerId)
                        ->setStoreId($param['store_id'])
                        ->setCartKey(time())
                        ->setDeviceUid($param["device_uid"])
                        ->setProductId($param["product_id"])
                        ->setChildProductId($param["child_product_id"])
                        ->setQty($param["qty"])
                        ->setAmount($param["amount"])
                        ->setTaxAmount($param["tax_amount"])
                        ->setTotalAmount($param["total_amount"])
                        ->setOptionsjson(json_encode($param["options"]))
                        ->save();    
                // dd($cartModel->getData());                        
                
                $payload = [
                    'success' => true,
                    'message' => p__('xdelivery
                        ', 'Info Successfully Saved')
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
     * Increase Quantity cart
     *
    */
    public function increaseQuantityAction() {
        try {
         
           if($param = $this->getRequest()->getBodyParams()){               

                $customerId = $this->_getCustomerId(false);
                $product_id = $param['product_id'];
                if(!empty($param['child_product_id']) && $param['child_product_id']) {
                    $product_id = $param['child_product_id'];
                }

                /*Live product status*/
                $productModal = (new Xdelivery_Model_Products()); 
                $productModal->find($product_id);
                $product = $productModal->getData();
                $current_date = (new Siberian_Date())->toString("yyyy-MM-dd");

                $product['in_stock'] = (integer) $product['in_stock'];
                /*Stock Manage*/
                $product['selling_price'] = $product['price'];
                if($product['manage_stock'] == "1"){
                    if($product['qty'] == 0){
                        $product['in_stock'] = (integer) 0;
                    } 
                }

                if($product['in_stock'] == 0){
                     throw new Exception(p__('xdelivery', 'Product is not available to add to cart at the moment!'));
                }

                /*End Stock Manage*/
                $product['active_special_price'] = 0;
                if(!empty($product['special_price_start']) && !empty($product['special_price_end'])){
                     if (($current_date >= $product['special_price_start']) && ($current_date <= $product['special_price_end'])){
                        $product['active_special_price'] = 1;
                    }
                }
 
                if(empty($product['special_price_start']) && empty($product['special_price_end']) && !empty($product['special_price'])) {
                    $product['active_special_price'] = 1;
                }                    
            
                $product['offer_persent'] = (integer)(( $product['special_price'] * 100 ) / $product['price']);
                
                if($product['offer_persent'] > 0){
                    $product['offer_persent'] = 100 - $product['offer_persent'];
                }

                if($product['active_special_price'] == 1){
                     $product['selling_price'] = $product['special_price'];
                }

                
                $cartModel = (new Xdelivery_Model_Carts())->find(['cart_key' => $param['cart_key']]);

                if($cartModel->getId()){
                    $cart_qty = (integer) $cartModel->getQty();
                    $cart_qty = ($cart_qty + 1);

                    if($product['manage_stock'] == "1" && ($product['qty'] < $cart_qty)){
                         throw new Exception(p__('xdelivery', 'Max %s qty available for this product!', ($cart_qty-1) ));
                    }

                    $tax_amount = 0;
                    $total_amount = (double) $product['selling_price'];
                    $total_amount = ($cart_qty * $total_amount);

                    $cartModel->setQty($cart_qty)
                        ->setAmount($product['selling_price'])
                        ->setTaxAmount($tax_amount)
                        ->setTotalAmount($total_amount)
                        ->save(); 
                }             
                                           
                $payload = [
                    'success' => true,
                    'message' => p__('xdelivery', 'Info Successfully Saved')
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
     * Decrease Quantity cart
     *
     */
    public function decreaseQuantityAction() {
        try {
         
           if($param = $this->getRequest()->getBodyParams()){
             
                $customerId = $this->_getCustomerId(false);
                $current_date = (new Siberian_Date())->toString("yyyy-MM-dd");
                $product_id = $param['product_id'];
                if(!empty($param['child_product_id']) && $param['child_product_id']) {
                    $product_id = $param['child_product_id'];
                }

                /*Live product status*/
                $productModal = (new Xdelivery_Model_Products()); 
                $productModal->find($product_id);
                $product = $productModal->getData();

                $product['in_stock'] = (integer) $product['in_stock'];
                /*Stock Manage*/
                $product['selling_price'] = $product['price'];
                if($product['manage_stock'] == "1"){
                    if($product['qty'] == 0){
                        $product['in_stock'] = (integer) 0;
                    } 
                }

                if($product['in_stock'] == 0){
                     throw new Exception(p__('xdelivery', 'Product is not available to add to cart at the moment!'));
                }

                /*End Stock Manage*/
                $product['active_special_price'] = 0;
                if(!empty($product['special_price_start']) && !empty($product['special_price_end'])){
                     if (($current_date >= $product['special_price_start']) && ($current_date <= $product['special_price_end'])){
                        $product['active_special_price'] = 1;
                    }
                }
 
                if(empty($product['special_price_start']) && empty($product['special_price_end']) && !empty($product['special_price'])) {
                    $product['active_special_price'] = 1;
                }                   
            
                $product['offer_persent'] = (integer)(( $product['special_price'] * 100 ) / $product['price']);
                
                if($product['offer_persent'] > 0){
                    $product['offer_persent'] = 100 - $product['offer_persent'];
                }

                if($product['active_special_price'] == 1){
                     $product['selling_price'] = $product['special_price'];
                }

                $cartModel = (new Xdelivery_Model_Carts())->find(['cart_key' => $param['cart_key']]);

                if($cartModel->getId()){
                    $cart_qty = (integer) $cartModel->getQty();
                    $cart_qty = ($cart_qty - 1);
                    $tax_amount = 0;
                    $total_amount = (double) (double) $product['selling_price'];
                    $total_amount = ($cart_qty * $total_amount);

                    if($cart_qty == 0){
                        $cartModel->delete();
                    }else{
                        $cartModel->setQty($cart_qty)
                            ->setAmount($product['selling_price'])
                            ->setTaxAmount($tax_amount)
                            ->setTotalAmount($total_amount)
                            ->save(); 
                    }
                    
                }             
                                           
                $payload = [
                    'success' => true,
                    'message' => p__('xdelivery', 'Info Successfully Saved')
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
     * checkout cart
     *
     */
    public function checkoutScreenAction() {
        try {
         
           if($value_id = $this->getRequest()->getParam('value_id')) {
                $delivery_type = $this->getRequest()->getParam('delivery_type');

                $customer_id = $this->_getCustomerId(false);
                $address = (new Xdelivery_Model_Address())->findAll(['value_id' => $value_id, 'status' => 'active', 'customer_id' => $customer_id], 'is_default ASC')->toArray();

                $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $value_id]);
                $settingsData = $settingModel->getData();

               
                if($delivery_type == 'pickup'){
                    $days_up_to = (integer) $settingsData['days_up_to_pickup'];
                    $break_down_times = (integer) $settingsData['break_down_times_pickup'];
                }else{
                    $days_up_to = (integer) $settingsData['days_up_to_deliver'];
                    $break_down_times = (integer) $settingsData['break_down_times_deliver'];
                    
                }                             

                $break_down_times = (integer) (60 * $break_down_times);
                $dates = $this->_getDaysRange($days_up_to);
                $times = $this->_getHoursRange(0, 86400, $break_down_times , 'h:i a');

                $payload = [
                    'success' => true,
                    'address' => array_values($address),
                    'dates' => $dates,
                    'times' => $times,
                    'today_date' => strtotime(date("Y-m-d")),
                    'current_time' => strtotime(date("H:i", strtotime("+30 minutes"))),
                    'days_up_to' => $days_up_to,
                    'break_down_times' => $break_down_times
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
     * checkout cart
     *
     */
    public function checkoutScreenv2Action() {
        try {
         
           if($value_id = $this->getRequest()->getParam('value_id')) {
                $delivery_type = $this->getRequest()->getParam('delivery_type');
                $param = $this->getRequest()->getBodyParams();
                $currency = Core_Model_Language::getCurrencySymbol();
                $customer_id = $this->_getCustomerId(false);
                $address = (new Xdelivery_Model_Address())->findAll(['value_id' => $value_id, 'status' => 'active', 'customer_id' => $customer_id], 'is_default ASC')->toArray();

                $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $value_id]);
                $settingsData = $settingModel->getData();

               
                if($delivery_type == 'pickup'){
                    $days_up_to = (integer) $settingsData['days_up_to_pickup'];
                    $break_down_times = (integer) $settingsData['break_down_times_pickup'];
                }else{
                    $days_up_to = (integer) $settingsData['days_up_to_deliver'];
                    $break_down_times = (integer) $settingsData['break_down_times_deliver'];
                    
                }

                if(!empty($param['store_id'])){
                    $storeModel = (new Xdelivery_Model_Store())->find(['store_id' =>  $param['store_id']]);
                    $store_id = $storeModel->getId();
                }else{
                    $storeModel = (new Xdelivery_Model_Store())->find(['value_id' =>  $value_id]);
                    $store_id = $storeModel->getId(); 
                }


                $workingdays = (new Xdelivery_Model_WorkingTimes())::getWorkingDays($store_id, $value_id); 
                $workingSlots = [];
                foreach ($workingdays as $key => $value) {
                   $workday = $value->getData();
                   $workingSlots[$workday['working_day']] = $value->getData();
                }            
     
                $break_down_times = (integer) (60 * $break_down_times);
                $dates = $this->_getDaysRangeV2($days_up_to, $workingSlots, $delivery_type, $settingsData['date_format']);
                $times = $this->_getHoursRangeV2(0, 86400, $break_down_times , $settingsData['time_format'], $workingSlots, $delivery_type);

                /*Shipping Cost calculate*/
                $store = $storeModel->getData();
                $distancePoint = (new Xdelivery_Model_Utility())::getDistanceBetweenPointsNew($store['address_lat'], $store['address_lng'], $param['latitude'], $param['longitude']);
                $param['distancePoint'] = (float) $distancePoint;

                $shipping = $this->_getShippingCostAction($value_id, $param);
                if (isset($param['is_delivery_applied']) && $param['is_delivery_applied']) {
                    $ship_amount=0;
                }else{
                    $ship_amount=$shipping['shipping_amount'];
                }
                if($shipping['is_shipping'] && $delivery_type != 'pickup'){
                    $param['amount'] = $param['amount'] + $ship_amount;
                }

                $payload = [
                    'success' => true,
                    'address' => array_values($address),
                    'dates' => $dates,
                    'times' => $times,
                    'today_date' => strtotime(date("Y-m-d")),
                    'current_time' => strtotime(date("H:i", strtotime("+30 minutes"))),
                    'days_up_to' => $days_up_to,
                    'break_down_times' => $break_down_times,
                    'workingSlots' => $workingSlots,
                    'settings' => $settingsData,
                    'shipping' => $shipping,
                    'distancePoint' => $distancePoint,
                    'total_amount_with_currency' => Xdelivery_Model_Utility::displayPrice($param['amount'], $currency, $settingsData['number_of_decimals'], $settingsData['decimal_separator'], $settingsData['thousand_separator'], $settingsData['currency_position']),
                    'total_amount' => $param['amount'],
                    'store' => $store
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
     * get specific date time 
     *
     */
    public function _getShippingCostAction($value_id, $param = []) {

        $methods = (new Xdelivery_Model_ShippingMethod())->findAll(['value_id' => $value_id, 'status' => 1]);
        $shipping = ['is_shipping' => false];
        $methodsJson = [];
        foreach ($methods as $key => $value) {
          $value = $value->getData();
          $methodsJson[$value['method_type']] =  $value; 
        }

        
        if(array_key_exists('flat', $methodsJson)){
            $shipping['is_shipping'] = true;
            $shipping['shipping_method'] = $methodsJson['flat']['label_name'];
            $shipping['shipping_amount'] = (float) $methodsJson['flat']['amount'];
            $shipping['shipping_amount_with_currency'] = Core_Model_Language::getCurrencySymbol().$methodsJson['flat']['amount'];

        }
        
        if(array_key_exists('distance', $methodsJson) && $param['distancePoint'] > 0) {
            $distanceModel = (new Xdelivery_Model_Distance())->findAll(['value_id' => $value_id])->toArray();
            foreach ($distanceModel as $key => $value) {
              if(((float)$value['from_km'] <= $param['distancePoint']) && ((float) $value['upto_km'] >= $param['distancePoint'])){
                    $shipping['shipping_method'] = $methodsJson['distance']['label_name'];
                    $shipping['distance_id'] = (float) $value['id'];
                    $shipping['is_shipping'] = true;
                    $shipping['shipping_amount'] = (float) $value['amount'];
                    $shipping['shipping_amount_with_currency'] = Core_Model_Language::getCurrencySymbol().$value['amount'];
                }
            }            
         }


        if(array_key_exists('free', $methodsJson) && ($methodsJson['free']['amount'] < $param['amount'])){
            $shipping['is_shipping'] = true;
            $shipping['shipping_method'] = $methodsJson['free']['label_name'];
            $shipping['shipping_amount'] = (float) '0';
            $shipping['shipping_amount_with_currency'] = Core_Model_Language::getCurrencySymbol().'0';
        } 


        if(!$shipping['is_shipping']){
            $shipping['shipping_amount'] = (float) '0';
            $shipping['shipping_amount_with_currency'] = Core_Model_Language::getCurrencySymbol().'0';
        }


        return $shipping;
    }    


    /**
     * get specific date time 
     *
     */
    public function getSelectTimeAction() {
        try {
         
           if($value_id = $this->getRequest()->getParam('value_id')) {
               $delivery_type = $this->getRequest()->getParam('delivery_type');
               $delivery_date = $this->getRequest()->getParam('delivery_date');
               $store_id =  $this->getRequest()->getParam('store_id');

                $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $value_id]);
                $settingsData = $settingModel->getData();
                
                if($delivery_type == 'pickup'){
                    $break_down_times = (integer) $settingsData['break_down_times_pickup'];
                }else{
                    $break_down_times = (integer) $settingsData['break_down_times_deliver'];                    
                }
                $break_down_times = (integer) (60 * $break_down_times);

              //  $storeModel = (new Xdelivery_Model_Store())->find(['value_id' =>  $value_id]);
               // $store_id = $storeModel->getId();
                $workingdays = (new Xdelivery_Model_WorkingTimes())::getWorkingDays($store_id, $value_id); 
                $workingSlots = [];
                foreach ($workingdays as $key => $value) {
                   $workday = $value->getData();
                   $workingSlots[$workday['working_day']] = $value->getData();
                }

                $day = strtolower(date("l", $delivery_date));
                $days_settings = $workingSlots[$day];

                $times = $this->_getHoursRangeV2(0, 86400, $break_down_times , $settingsData['time_format'], $days_settings);

                $payload = [
                    'success' => true,
                    'delivery_time' => $times,
                    'break_down_times' => $break_down_times
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
     * checkout cart
     *
     */
    public function paymentScreenAction() {
        try {
         
           if($value_id = $this->getRequest()->getParam('value_id')) {
                $param = $this->getRequest()->getBodyParams();
                $customer_id = $this->_getCustomerId(false);
                $address_id = $param['address_id'];
                $address = (new Xdelivery_Model_Address())->find($address_id);
                $address = $address->getData();

                $paymentMethod = (new Xdelivery_Model_PaymentMethod())->findAll(['value_id' => $value_id, 'status' => 1])->toArray();

                $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $value_id]);
                $settings = $settingModel->getData();
                $address_template = $address['address'];
                if ($settings['is_enable_address_two']) {
                    $address_template.= ', '.$address['address_two'];
                }
                if ($settings['is_enable_locality']) {
                    $address_template.= ', '.$address['locality'];
                }
                if ($settings['is_enable_city']) {
                    $address_template.= ', '.$address['city'];
                }
                $address_template.= ', '.$address['pincode'];                
                $payload = [
                    'success' => true,
                    'address' => $address_template,
                    'delivery_date' => !empty($param['delivery_date']) ?date($settings['date_format'], $param['delivery_date']) : '',
                     'delivery_time' => !empty($param['delivery_time']) ? date($settings['time_format'], $param['delivery_time']) : '',
                     'customer' => $address['customer_name'].', '.$address['phone_number'],
                     'payment_methods' => $paymentMethod,
                     'settings' => $settings
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
     * @param bool $throw
     * @return mixed|null
     * @throws Exception
     * @throws Zend_Session_Exception
     */
    private function _getDaysRange($days_up_to_deliver = 7)
    {   $dates = [];
        $date_from = date("Y-m-d");   
        $date_from = strtotime($date_from);    
        $date_to = date("Y-m-d");   
        $date_to = strtotime($date_to.' +'.$days_up_to_deliver.' day'); 
        
        for ($i = $date_from; $i <= $date_to; $i += 86400) {
            $dates[strtotime(date("Y-m-d", $i))] = ($i == strtotime(date("Y-m-d"))) ? p__('xdelivery', 'Today') : date("D, jS F, Y", $i);
        }  

        return $dates;
    }
   
   /**
     * @param bool $throw
     * @return mixed|null
     * @throws Exception
     * @throws Zend_Session_Exception
     */
    private function _getHoursRange( $lower = 0, $upper = 86400, $step = 3600, $format = '' ) {
        $times = array();

        if (empty( $format)) {
            $format = 'g:i a';
        }

        foreach ( range( $lower, $upper, $step ) as $increment ) {
            $increment = gmdate( 'H:i', $increment );
            list( $hour, $minutes ) = explode( ':', $increment );
            $date = new DateTime( $hour . ':' . $minutes );
            $times[strtotime($increment)] = (string) $date->format( $format );
        }

        return $times;
    }


        /**
     * @param bool $throw
     * @return mixed|null
     * @throws Exception
     * @throws Zend_Session_Exception
     */
    private function _getDaysRangeV2($days_up_to_deliver = 7, $workingdays = [], $delivery_type, $date_format = 'D, jS F, Y') {   

        $dates = [];
        $date_from = date("Y-m-d");   
        $date_from = strtotime($date_from);    
        $date_to = date("Y-m-d");   
        $date_to = strtotime($date_to.' +'.$days_up_to_deliver.' day'); 
        
        for ($i = $date_from; $i <= $date_to; $i += 86400) {

            $day = strtolower(date("l", $i));
            $days_settings = $workingdays[$day];
            if($delivery_type == 'delivery' && $days_settings['enable_delivery'] == '1') {
                $dates[strtotime(date("Y-m-d", $i))] = ($i == strtotime(date("Y-m-d"))) ? p__('xdelivery', 'Today') : date($date_format, $i);
            }

            if($delivery_type == 'pickup' && $days_settings['enable_pickup'] == '1') {
                $dates[strtotime(date("Y-m-d", $i))] = ($i == strtotime(date("Y-m-d"))) ? p__('xdelivery', 'Today') : date($date_format, $i);
            }
            
        }  

        return $dates;
    }
   
   /**
     * @param bool $throw
     * @return mixed|null
     * @throws Exception
     * @throws Zend_Session_Exception
     */
    private function _getHoursRangeV2( $lower = 0, $upper = 86400, $step = 3600, $format = '', $days_settings = []) {
        $times = array();

        if (empty( $format)) {
            $format = 'g:i a';
        }

        foreach ( range( $lower, $upper, $step ) as $increment ) {
            $increment = gmdate( 'H:i', $increment );
            list( $hour, $minutes ) = explode( ':', $increment );
            $date = new DateTime( $hour . ':' . $minutes );
           
            if(strtotime($days_settings['opening_time']) <= strtotime($increment) && strtotime($days_settings['closing_time']) >= strtotime($increment)){
               $times[strtotime($increment)] = (string) $date->format( $format );
        
           }          
        }
        return $times;
    }


    public function syncDeviceCustomerAction(){
          try {
         
           if($value_id = $this->getRequest()->getParam('value_id')) {
               $device_uid = $this->getRequest()->getParam('device_uid');
               $customer_id = $this->_getCustomerId();
               $update = (new Xdelivery_Model_Carts())->syncDeviceCustomer($device_uid,  $customer_id, $value_id);
               
                $payload = [
                    'success' => true,
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
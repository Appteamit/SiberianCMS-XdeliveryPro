<?php

use Siberian\Exception;

/**
 * Class Xdelivery_Mobile_ViewController
 */
class Xdelivery_Mobile_ViewController extends Application_Controller_Mobile_Default
{
   /**
     * Fetch All
     *
     */
    public function findAllAction()
    {

        try {

            if($param = $this->getRequest()->getBodyParams()){

            	$sliders = (new Xdelivery_Model_Slider())->findAll(['value_id' => $param['value_id'] , 'category_id' => 0, 'status != ?' => 'deleted'])->toArray();

            	$parentCategory = (new Xdelivery_Model_Category())->findAll(['value_id' => $param['value_id'], 'parent_id' => 0, 'is_active' => 1 ], 'position ASC')->toArray();
                
                $settingModel = (new Xdelivery_Model_Settings())->find(['value_id' => $param['value_id']]);
                $settingsData = $settingModel->getData();

                $settings = ['auto_approval_review' => (boolean) $settingsData['auto_approval_review'],
                'enable_acceptance_rejection' => (boolean) $settingsData['enable_acceptance_rejection'],
                'enable_booking' => (boolean) $settingsData['enable_booking'],
                'enable_tax' => (boolean) $settingsData['enable_tax'],
                'review_rating_enable' => (boolean) $settingsData['review_rating_enable'],
                'send_invoice_email' => (boolean) $settingsData['send_invoice_email'],
                'max_qty_per_product' => (integer) $settingsData['max_qty_per_product'],
                'max_qty_shopping_cart' => (integer) $settingsData['max_qty_shopping_cart'],
                'min_order_value' => (integer) $settingsData['min_order_value'],
                'min_qty_shopping_cart' => (integer) $settingsData['min_qty_shopping_cart'],
                'discount_tax_calculation' => (integer) $settingsData['discount_tax_calculation'],
                'delivery_time' => (integer) $settingsData['delivery_time'],
                'pick_up_time' => (integer) $settingsData['pick_up_time'],
                'max_order_value' => (double) $settingsData['max_order_value'],
                'category_design' => (string) $settingsData['category_design'],
                'product_design' => (string) $settingsData['product_design'],
                'home_screen' => (string) $settingsData['home_screen'],
                'delivery_cost' => (double) $settingsData['delivery_cost'],
                'enable_time_to_deliver' => (integer) $settingsData['enable_time_to_deliver'],
                'break_down_times_deliver' => (integer) $settingsData['break_down_times_deliver'],
                'enable_date_to_deliver' => (integer) $settingsData['enable_date_to_deliver'],
                'days_up_to_deliver' => (integer) $settingsData['days_up_to_deliver'],
                'enable_time_to_pickup' => (integer) $settingsData['enable_time_to_pickup'],
                'break_down_times_pickup' => (integer) $settingsData['break_down_times_pickup'],
                'enable_date_to_pickup' => (integer) $settingsData['enable_date_to_pickup'],
                'days_up_to_pickup' => (integer) $settingsData['days_up_to_pickup'],
                'enable_to_deliver' => (integer) $settingsData['enable_to_deliver'],
                'enable_to_pickup' => (integer) $settingsData['enable_to_pickup'],
                'is_enable_address_two' => (integer) $settingsData['is_enable_address_two'],
                'is_enable_locality' => (integer) $settingsData['is_enable_locality'],
                'is_enable_city' => (integer) $settingsData['is_enable_city'],
                'enable_retrun' => (boolean) $settingsData['enable_retrun'],
                'return_within' => (integer) $settingsData['return_within'],
                'company_address' => (integer) $settingsData['company_address'],
                'sdi' => (integer) $settingsData['sdi'],
                'pec' => (integer) $settingsData['pec'],
                'cod_fiscale' => (integer) $settingsData['cod_fiscale'],
                'enable_qrscan' => (boolean) $settingsData['enable_qrscan'],
                'taxes_enable' => (boolean) $settingsData['taxes_enable'],
                'enable_tax_with_product' => (boolean) $settingsData['enable_tax_with_product'],
                'app_service_type' => (string) $settingsData['app_service_type'],
                'enable_addtocart' => (boolean) $settingsData['enable_addtocart'],
                'enable_save_later' => (boolean) $settingsData['enable_save_later'],
                'hide_price' => (boolean) $settingsData['hide_price'],
                'enable_tips' => (boolean) $settingsData['enable_tips']
               ];

                if($settings['app_service_type'] == 'other'){
                    $settings['enable_to_deliver'] = 0;
                    $settings['enable_to_pickup'] = 0;
                }

                $storeJson = [];
                if($settings['app_service_type'] == 'food'){
                    $params = [];
                    $params['is_active'] = 1;
                    $stores = (new Xdelivery_Model_Store())->findByValueId($param['value_id'], $params);
                    
                    foreach ($stores as $store) {
                        $data = $store->getData();
                        $data['is_active'] = $data['is_active'] == 1 ? p__('xdelivery', "Active") : p__('xdelivery', "In Active");                         
                        $data['is_default'] = (boolean) $data['is_default'];
                        $data['store_sub_title'] = !empty($data['store_sub_title']) ? $data['store_sub_title'] : '';
                        $storeJson[] = $data;
                    }
                    
                    if(count($storeJson) == 1){

                        $settings['is_food_app'] = true;
                        $settings['home_screen'] = 'category';                        
                     }
                }

                $customerId = $this->_getCustomerId(false);
                $cart_count = (new Xdelivery_Model_Carts())
                    ->findByDeviceAndUserId($param['value_id'], $customerId, $param['device_uid']);

               
      
                $payload = [
                        'success' => true,
                        'sliders' => array_values($sliders),
                        'parent_category' => $parentCategory,
                        'page_title' => (string) $this->getCurrentOptionValue()->getTabbarName(),
                        'settings' => $settings,
                        'stores' => $storeJson,                        
                        'today_day' => strtolower(date("l")),
                        'total_store' => (integer) count($storeJson),
                        'store_id' => (integer) count($storeJson) == 1 ? $storeJson[0]['store_id'] : '',
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
     * popular Suggestions
     *
     */
    public function popularSuggestionsAction()
    {

        try {

            if($param = $this->getRequest()->getBodyParams()){

                $popular_suggestions = (new Xdelivery_Model_Category())->findAll(['value_id' => $param['value_id'], 'is_active' => 1, 'is_popular_search' => 1], 'position ASC')->toArray();

                $payload = [
                        'success' => true,
                        'popular_suggestions' => array_values($popular_suggestions),
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
     * @param var values_id
     * @return Array|null
     * @throws Exception
     */
    public function fetchStripeSettingsAction()
    {
       try {
            $application = $this->getApplication();
            $appId = $application->getId();  
            $value_id = $this->getRequest()->getParam('value_id');          
            $stripeModel = (new Xdelivery_Model_PaymentMethod())->find(['value_id' => $value_id, 'method_type' => 'stripe']);

            if ($stripeModel->getId()) {
                $settings = $stripeModel->getData();
                
                $payload = [
                    'success' => true,
                    'settings' => $settings,
                    'publishable_key' => !empty($settings['publishable_key']) ? $settings['publishable_key'] : false
                ];
            }else{
                $settingsModel = (new Xdelivery_Model_Stripe())->find($appId, 'app_id');
                $settings = $settingsModel->getData();

                $payload = [
                    'success' => true,
                    'settings' => $settings,
                    'publishable_key' => !empty($settings['publishable_key']) ? $settings['publishable_key'] : false
                ];
            }            
 
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);

    }
    public function userTypeAction(){
        try {
          $customerId = $this->_getCustomerId(false);       
          $value_id = $this->getRequest()->getParam('value_id'); 
          $Admins = new Xdelivery_Model_Admins();
          $Admins->find(['customer_id' => $customerId,'value_id' => $value_id]);            
          $is_admin=false;
            if($Admins->getId()) {
                $is_admin=true;
            }

            $payload = [
                "success" => true,                            
                "is_admin"=>$is_admin,
                'page_title' => (string) $this->getCurrentOptionValue()->getTabbarName()
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
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


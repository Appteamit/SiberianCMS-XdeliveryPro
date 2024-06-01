<?php

use Siberian\Exception;

/**
 * Class Xdelivery_Mobile_AccountController
 */
class Xdelivery_Mobile_AccountController extends Application_Controller_Mobile_Default
{

  /**
     * Fetch All
     *
     */
    public function fetchAllWishlistAction()
    {

        try {

            if($param = $this->getRequest()->getBodyParams()) { 
               $current_date = (new Siberian_Date())->toString("yyyy-MM-dd");
               $customer_id = $this->_getCustomerId(); 
               $params =   ["limit" => 50,
                        	"offset" => $param['offset'],
                        	"customer_id" => $customer_id,
                        	"device_uid" => $param['device_uid'],
                        	"is_active" => 1
                        ];
               

                $products = (new Xdelivery_Model_Products())
                ->findByWishlistByCustomer($param['value_id'], $params);
 
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

                    $data['offer_persent'] = (integer)(( $data['selling_price'] * 100 ) / $data['price']);

                    if($data['offer_persent'] > 0){
                        $data['offer_persent'] = 100 - $data['offer_persent'];
                    }
                    
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
     * fetch all customer address
     *
     */
    public function fetchAllAddressAction()
    {
        try {
           
            if($param = $this->getRequest()->getBodyParams()){
                $customer_id = $this->_getCustomerId();
                $address = (new Xdelivery_Model_Address())->findAll(['value_id' => $param['value_id'], 'status' => 'active', 'customer_id' => $customer_id], 'is_default ASC')->toArray();

                $payload = [
                        'success' => true,
                        'address' => array_values($address),
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
     * Save address
     *
     */
    public function saveAddressAction() {
        try {
         
            if($param = $this->getRequest()->getBodyParams()) {
                $value_id = $this->getRequest()->getParam('value_id');
                $customerId = $this->_getCustomerId(false);
                
                $model = (new Xdelivery_Model_Address())->find(['id'=> $param["id"]]);

                $model->setValueId($value_id)
                    ->setCustomerId($customerId)
                    ->setCustomerName($param["customer_name"])
                    ->setPhoneNumber($param["phone_number"])
                    ->setPincode($param["pincode"])
                    ->setAddress($param["address"])
                    ->setLocality($param["locality"])
                    ->setCity($param["city"])
                    ->setState($param["state"])
                    ->setAddressType($param["address_type"])
                    ->setIsDefault($param["is_default"])
                    ->setCompanyAddress($param["company_address"])
                    ->setSdi($param["sdi"])
                    ->setPec($param["pec"])
                    ->setCodFiscale($param["cod_fiscale"])
                    ->save();
            
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
     * delete address
     *
     */
    public function deleteAddressAction() {
        try {
         
            if($address_id = $this->getRequest()->getParam('address_id')) {

                $model = (new Xdelivery_Model_Address())->find(['id'=> $address_id]);
                $model->setStatus('deleted')
                       ->save();
            
                $payload = [
                    'success' => true,
                    'message' => p__('xdelivery', 'Deleted successfully')
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
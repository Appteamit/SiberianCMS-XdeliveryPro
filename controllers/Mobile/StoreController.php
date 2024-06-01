<?php

use Siberian\Exception;

/**
 * Class Xdelivery_Mobile_StoreController
 */
class Xdelivery_Mobile_StoreController extends Application_Controller_Mobile_Default
{
   /**
     * Fetch Store
     *
     */
    public function findStoreAction()
    {

        try {

            if($value_id = $this->getRequest()->getParam('value_id')){
                $store_id = $this->getRequest()->getParam('store_id');
                $device_uid = $this->getRequest()->getParam('device_uid');

                $store = (new Xdelivery_Model_Store())->find($store_id);                  
                $parentCategory = (new Xdelivery_Model_Category())->findAll(['value_id' => $value_id, 'parent_id' => 0, 'is_active' => 1 ], 'position ASC')->toArray();

                  //Clear cart 
                $customerId = $this->_getCustomerId(false);
                if($customerId > 0){
                    $cartExist = (new Xdelivery_Model_Carts())->findAll(['customer_id' => $customerId, 'store_id' => $store_id])->toArray();
                    if(count($cartExist) == 0){
                        $cartModel = (new Xdelivery_Model_Carts())->deleteQuery('xdelivery_shopping_carts', 'customer_id', $customerId); 
                    }
                }

                if(!empty($device_uid)){
                    //Clear cart 
                        $cartExist = (new Xdelivery_Model_Carts())->findAll(['device_uid' => $device_uid, 'store_id' => $store_id])->toArray();
                        if(count($cartExist) == 0){
                            $cartModel = (new Xdelivery_Model_Carts())->deleteQuery('xdelivery_shopping_carts', 'device_uid', $device_uid); 
                        }
                    }
                }

                $cart_count = (new Xdelivery_Model_Carts())
                    ->findByDeviceAndUserId($value_id, $customerId, $device_uid);

                $payload = [
                        'success' => true,
                        'parent_category' => $parentCategory,
                        'store' => $store->getData(),
                        'cart_count' => count($cart_count->toArray())
                    ];

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
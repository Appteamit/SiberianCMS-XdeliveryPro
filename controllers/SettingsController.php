<?php

/**
 * Class Xdelivery_SettingsController
 */
class Xdelivery_SettingsController extends Application_Controller_Default
{


    /**
     *shipping-distance 
     */
    public function shippingdistanceAction()
    {
        $this->loadPartials();
    }

    /**
     *shipping-distance 
     */ 
     public function saveShippingDistanceAction() {

       if($fromDistance = $this->getRequest()->getPost('from_distance')) {     
              $id = $this->getRequest()->getPost('id');
              $uptoKm = $this->getRequest()->getPost('upto_distance');
              $amount = $this->getRequest()->getPost('price');

           try {  
                $value_id = $this->getRequest()->getPost('value_id');

                foreach ($fromDistance as $key => $value) {
                   // if(empty($uptoKm[$key]) || empty($value)) continue;
                    
                    if($id[$key] != 0){
                        $model = (new Xdelivery_Model_Distance())
                        ->find(['value_id' => $value_id, 'id' => $id[$key] ])
                        ->setValueId($value_id)
                        ->setFromKm($value)
                        ->setUptoKm($uptoKm[$key])
                        ->setAmount( $amount[$key])
                        ->save();
                   }else{
                        $model = (new Xdelivery_Model_Distance())
                            ->setValueId($value_id)
                            ->setFromKm($value)
                            ->setUptoKm($uptoKm[$key])
                            ->setAmount($amount[$key])
                            ->save();
                   }
                   

                }                  
               
                $this->getSession()->addSuccess(p__('xdelivery', "Info successfully saved"));

                $html = [
                    "success" => 1,
                    'fromDistance' => $fromDistance,
                    'id' => $id,
                    'uptoKm' => $uptoKm,
                    'amount' => $amount
                ];

            } catch(Exception $e) {
                $html = [
                    "error" => 1,
                    "message" => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->getResponse()->setBody(Zend_Json::encode($html))->sendResponse();
            die;

        }
    }

       //function for delete Attributes  
    public function deleteDistanceAction() {
      try {
            $request = $this->getRequest();
            $id = $request->getParam("id", null);
            $model = new Xdelivery_Model_Distance();
            $model->find(array('id' => $id));
            $model->delete();

            $payload = [
                'success' => true,
                'message' => p__('xdelivery', 'Successfully deleted'),
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
     *
     */
    public function editpostAction()
    {
          try
        {
            $values = $this->getRequest()->getPost();
            // dd($values);
            $application = $this->getApplication();
            $form = new Xdelivery_Form_Settings();
            if ($form->isValid($values))
            {       
                if($values['min_qty_shopping_cart'] <= 0) throw new Siberian_Exception(p__('xdelivery', 'Mimimum Qty In Shopping Cart Must be greater than 0!'));
                if($values['max_qty_shopping_cart'] <= 0 && $values['max_qty_shopping_cart'] < $values['min_qty_shopping_cart']) throw new Siberian_Exception(p__('xdelivery', 'Maximum Qty In Shopping Cart Must be greater than 0 OR Mimimum Qty In Shopping Cart!'));
                if($values['max_qty_per_product'] <= 0) throw new Siberian_Exception(p__('xdelivery', 'Maximum Qty Per Product Must be greater than 0!'));
                if($values['min_order_value'] <= 0) throw new Siberian_Exception(p__('xdelivery', 'Mimimum Order Value Must be greater than 0!'));
                if($values['max_order_value'] <= 0 && $values['min_order_value'] > $values['max_order_value']) throw new Siberian_Exception(p__('xdelivery', 'Maximum Order Value Must be greater than 0 OR Mimimum Order Value !'));  

                if($values['app_service_type'] == 'other'){
                    //$values['enable_to_deliver'] = 0;
                    //$values['enable_to_pickup'] = 0;

                }
                // dd($values);
                $settings = new Xdelivery_Model_Settings();
                $settings->addData($values);
                $settings->save();

                $payload = ["success" => "1", "success_message" => p__('xdelivery',  "Saved successfully") , 'message_timeout' => 1, 'message_button' => 0, 'message_loader' => 0, ];

            }
            else
            {
                /** Do whatever you need when form is not valid */
                $payload = ["error" => true, "message" => $form->getTextErrors() , "errors" => $form->getTextErrors(true) , ];
            }

        }
        catch(\Exception $e)
        {
            $payload = ["error" => true, "message" => $e->getMessage() , ];
        }

        $this->_sendJson($payload);
    }

    /**
     *
     */
    public function foodeditpostAction()
    {
          try
        {
            $values = $this->getRequest()->getPost();
            $application = $this->getApplication();
            $form = new Xdelivery_Form_FoodSettings();
            if ($form->isValid($values))
            {       
                $settings = new Xdelivery_Model_Settings();
                $settings->addData($values);
                $settings->save();

                $payload = ["success" => "1", "success_message" => p__('xdelivery',  "Saved successfully") , 'message_timeout' => 1, 'message_button' => 0, 'message_loader' => 0, ];

            }
            else
            {
                /** Do whatever you need when form is not valid */
                $payload = ["error" => true, "message" => $form->getTextErrors() , "errors" => $form->getTextErrors(true) , ];
            }

        }
        catch(\Exception $e)
        {
            $payload = ["error" => true, "message" => $e->getMessage() , ];
        }

        $this->_sendJson($payload);
    }


    /**
     *
     */
    public function editpaymentmethodAction()
    {
          try
        {
            $values = $this->getRequest()->getPost();
            $application = $this->getApplication();
            
            if($values['method_type'] == 'cash_on_delivery'){
                 $form = new Xdelivery_Form_PaymentMethod_CashOnDelivery();
            }
            if($values['method_type'] == 'bank_transfer'){
                 $form = new Xdelivery_Form_PaymentMethod_BankTransfer();
            }
            if($values['method_type'] == 'check_money_order'){
                 $form = new Xdelivery_Form_PaymentMethod_CheckMoneyOrder();
            } 
            if($values['method_type'] == 'stripe'){
                 $form = new Xdelivery_Form_PaymentMethod_Stripe();
            }
            if($values['method_type'] == 'paypal'){
                 $form = new Xdelivery_Form_PaymentMethod_Paypal();
            } 
            if($values['method_type'] == 'ewallet'){
                 $form = new Xdelivery_Form_PaymentMethod_Ewallet();
                 if(!class_exists("Ewallet_Model_Ewallet")) {
                    throw new Exception(__('Wallet features is not available, Please contact to administrator.'));
                }else{
                    $walletValueId = (new Ewallet_Model_Ewallet)->getCurrentValueId();
                    if(!$walletValueId){
                        throw new Exception(__('Wallet module is not activated, Please add a features in app and try again.'));
                    }
                }
            }                     

            if ($form->isValid($values))
            {       
       
                $settings = new Xdelivery_Model_PaymentMethod();
                $settings->addData($values);
                $settings->save();

                $payload = ["success" => "1", "success_message" => p__('xdelivery',  "Saved successfully") , 'message_timeout' => 1, 'message_button' => 0, 'message_loader' => 0, ];

            }
            else
            {
                /** Do whatever you need when form is not valid */
                $payload = ["error" => true, "message" => $form->getTextErrors() , "errors" => $form->getTextErrors(true) , ];
            }

        }
        catch(\Exception $e)
        {
            $payload = ["error" => true, "message" => $e->getMessage() , ];
        }

        $this->_sendJson($payload);
    }

    /**
     *
     */
    public function editshippingmethodAction()
    {
        try {

            $values = $this->getRequest()->getPost();
            $application = $this->getApplication();
            
            if($values['method_type'] == 'free'){
                 $form = new Xdelivery_Form_ShippingMethod_Free();
            }
            if($values['method_type'] == 'distance'){
                 $form = new Xdelivery_Form_ShippingMethod_Distance();
            }
            if($values['method_type'] == 'flat'){
                 $form = new Xdelivery_Form_ShippingMethod_Flat();
            } 

            if ($form->isValid($values))
            {     
                $settings = new Xdelivery_Model_ShippingMethod();
                $settings->addData($values);
                $settings->save();

                $payload = ["success" => "1", "success_message" => p__('xdelivery',  "Saved successfully") , 'message_timeout' => 1, 'message_button' => 0, 'message_loader' => 0, ];

            }
            else
            {
                /** Do whatever you need when form is not valid */
                $payload = ["error" => true, "message" => $form->getTextErrors() , "errors" => $form->getTextErrors(true) , ];
            }

        }
        catch(\Exception $e)
        {
            $payload = ["error" => true, "message" => $e->getMessage() , ];
        }

        $this->_sendJson($payload);
    }

     /**
     *
     */
    public function edittaxAction()
    {
        try {

            $values = $this->getRequest()->getPost();
            $application = $this->getApplication();
            
            $form = new Xdelivery_Form_Tax_From();
         
            if ($form->isValid($values))
            {     
                $settings = new Xdelivery_Model_Tax();
                $settings->addData($values);
                $settings->save();

                $payload = ["success" => "1", "success_message" => p__('xdelivery',  "Saved successfully") , 'message_timeout' => 1, 'message_button' => 0, 'message_loader' => 0, ];

            }
            else
            {
                /** Do whatever you need when form is not valid */
                $payload = ["error" => true, "message" => $form->getTextErrors() , "errors" => $form->getTextErrors(true) , ];
            }

        }
        catch(\Exception $e)
        {
            $payload = ["error" => true, "message" => $e->getMessage() , ];
        }

        $this->_sendJson($payload);
    }

    public function loadtaxformAction(){
        
        if ($id = $this->getRequest()->getParam("id")) {
            try {
                 
                $model = new Xdelivery_Model_Tax();
                $model->find($id);
                if($model->getId()) {
                    $data = $model->getData();
                    $data['status'] = $data['status'] == 'active' ? 1 : 0;
                    $form = new Xdelivery_Form_Tax_From();
                    $form->populate($data);
                    $form->setElementValueById('value_id', $this->getCurrentOptionValue()->getId());
                    $form->addNav("edit-nav-xdelivery", "Save", false); 
                    $form->removeNav("nav-add-xdelivery");              
                    $form->setElementValueById('id', $model->getId());
                    
                    $payload = array(
                        "check" => 'ready for use',
                        "success"   => true,
                        "form"      => $form->render(),
                        "message"   => p__('xdelivery', "Saved successfull"),
                    );

                }else{
                    $payload = array(
                        "error"     => true,
                        "message"   => p__('xdelivery', 'Taxes you are trying to edit does not exists.'),
                    );
                }              

            } catch (Exception $e) {
                $payload = array(
                    'error' => true,
                    'message' => $e->getMessage()
                );
            }
        }

        $this->_sendHtml($payload);
    }


    /**
     *
     */
    public function deletetaxesAction()
    {   
        $payload = array();

        if ($data = $this->getRequest()->getPost()) {

            try {

                $model = new Xdelivery_Model_Tax();
                $model->find($data['id']);
                if($model->getId()) {
                    $model->setStatus('deleted');
                    $model->save();
              
                    $payload = array(
                        'success' => true,
                        'success_message' => p__('xdelivery', 'Deleted successfully saved'),
                        'message_timeout' => 2,
                        'message_button' => 0,
                        'message_loader' => 0
                    );

                }

            } catch (Exception $e) {
                $payload = array(
                    'error' => true,
                    'message' => $e->getMessage()
                );
            }           
        }

        $this->_sendJson($payload);
    }
    /**
     *business Day 
     */
    public function businessDayAction()
    {
          try
        {
            $values = $this->getRequest()->getPost();
            $application = $this->getApplication();
            $form = new Xdelivery_Form_BusinessDay();
            if ($form->isValid($values))
            {       
                $business = new Xdelivery_Model_BusinessDay();
                $business->addData($values);
                $business->save();

                $payload = ["success" => "1", "success_message" => p__('xdelivery',  "Saved successfully") , 'message_timeout' => 1, 'message_button' => 0, 'message_loader' => 0, ];

            }
            else
            {
                /** Do whatever you need when form is not valid */
                $payload = ["error" => true, "message" => $form->getTextErrors() , "errors" => $form->getTextErrors(true) , ];
            }

        }
        catch(\Exception $e)
        {
            $payload = ["error" => true, "message" => $e->getMessage() , ];
        }

        $this->_sendJson($payload);
    }
   
}
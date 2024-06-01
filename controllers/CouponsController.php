<?php

/**
 * Class Xdelivery_CouponsController
 */
class Xdelivery_CouponsController extends Application_Controller_Default
{
    /**
     *
     */
    public function listAction()
    {
    	$this->loadPartials();
    }


    /**
     *
     */
    public function addAction()
    {
        $this->loadPartials();
    }

     /**
     *
     */
    public function editAction()
    {
         $model = (new Xdelivery_Model_Coupons());  
            if ($id = $this->getRequest()->getParam('id')) {
                $model->find($id); 
                if (!$model->getId()) {
                        $this->getRequest()->addError( p__("xdelivery",  "This coupon does not exist."));
                }
            }

        $this->loadPartials();
        $this->getLayout()->getPartial('content')->setCurrentCoupon($model);
    }


    public function saveAction() {

       if($param = $this->getRequest()->getPost()) {     
            try {  
  
                $model = (new Xdelivery_Model_Coupons())
                    ->find(['id' => $param['id']])
                    ->setValueId($param['value_id'])
                    ->setName($param['name'])
                    ->setDescription($param['description'])
                    ->setCouponCode($param['coupon_code'])
                    ->setDiscountType($param['discount_type'])
                    ->setDiscountValue($param['discount_value'])
                    ->setAllowFreeShipping($param['allow_free_shipping'])
                    ->setMinSpend($param['min_spend'])
                    ->setMaxSpend($param['max_spend'])
                    ->setUsageLimitPerCoupon($param['usage_limit_per_coupon'])
                    ->setUsageLimitPerCustomer($param['usage_limit_per_customer'])
                    ->setStartDate($param['start_date'])
                    ->setEndDate($param['end_date'])
                    ->setStartTime($param['start_time'])
                    ->setEndTime($param['end_time'])
                    ->setStatus($param['status'])
                    ->setIsDelete(0);
                    $model->save();

                $this->getSession()->addSuccess(p__('xdelivery', "Info successfully saved"));
          
            $html = [
                "success" => 1
            ];

          }catch(Exception $e) {
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


    /**
     * fetch category
     */
     public function findAllAction() {
        
        try {
            $request = $this->getRequest();
            $limit = $request->getParam("perPage", 25);
            $offset = $request->getParam("offset", 0);
            $sorts = $request->getParam("sorts", []);
            $queries = $request->getParam("queries", []);

            $filter = null;
            if (array_key_exists("search", $queries)) {
                $filter = $queries["search"];
            }
            
            $params = [
                "limit" => $limit,
                "offset" => $offset,
                "sorts" => $sorts,
                "filter" => $filter,
            ];
          
            $value_id = (new Xdelivery_Model_Xdelivery())->getCurrentValueId();
            $application = $this->getApplication();
            
            $coupons = (new Xdelivery_Model_Coupons())
                ->findByValueId($value_id, $params);

            $countAll = (new Xdelivery_Model_Coupons())->countAllForApp($value_id);
            $countFiltered =   (new Xdelivery_Model_Coupons())->countAllForApp($value_id, $params);

            $couponsJson = [];
            foreach ($coupons as $coupon) {
                $data = $coupon->getData();
                $data['status'] = $data['status'] == 1 ? p__('xdelivery', "Active") : p__('xdelivery', "In Active");
                $couponsJson[] = $data;
            }

            $payload = [
                "records" => $couponsJson,
                "queryRecordCount" => $countFiltered[0],
                "totalRecordCount" => $countAll[0]
            ];

        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        $this->_sendJson($payload);
    }

       //function for delete  
    public function deleteAction() {
      try {
            $request = $this->getRequest();
            $id = $request->getParam("id", null);
            $model = new Xdelivery_Model_Coupons();
            $model->find(array('id' => $id));
            $model->setIsDelete(1);
            $model->save();

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

}
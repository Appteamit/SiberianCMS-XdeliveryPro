<?php
/**
 * Class Xdelivery_AdminsController
 */
class Xdelivery_AdminsController extends Application_Controller_Default
{
    public function adminlistAction(){
        
        if ($datas = $this->getRequest()->getQuery()) {
            try {
                $customer = new Customer_Model_Customer();
                $customers = $customer->findAll(['app_id = ?' => $datas['app_id']]);
                $admins_list = [];
                if(count($customers)) {
                    foreach($customers as $customer) {
                        $Admins = new Xdelivery_Model_Admins();
                        $Admins->find(['customer_id' => $customer->getId(),'value_id' => $datas['value_id']]);
                        $owner_store = p__('xdelivery','N/A');
                        $action = '<button class="btn color-blue" onclick="adminStatus(1, '.$customer->getId().');">'.p__("xdelivery", "No").'</button>';
                        if($Admins->getId()) {
                            $action = '<button class="btn color-blue" onclick="adminStatus(2, '.$customer->getId().');">'.p__("xdelivery","Yes").'</button>';
                        }
                        $stores = (new Xdelivery_Model_Store())->findAll(['value_id' => $datas['value_id'], 'is_active' => 1 ]);
                            if(count($stores)) {
                                $owner_store = '<select '.($Admins->getId() ? "" : "disabled").' style="width:170px;" class="no-dk styled-select color-blue" onchange="adminStore('.$Admins->getStoreId().', this.value, '.$customer->getId().');">';
                                $owner_store .= '<option value="0">'.p__('xdelivery','All Shops (default)').'</option>';
                                foreach($stores as $store) {
                                    $owner_store .= '<option '.($store['store_id'] == $Admins->getStoreId() ? "selected" : "").' value="'.$store['store_id'].'">'.$store['store_name'].'</option>';
                                }
                                $owner_store .= '</select>';
                            }
                        $$admins_list[] = [
                            $customer->getId(),
                            $customer->getFirstname().' '.$customer->getLastname(),
                            $customer->getEmail(),                            
                            $owner_store,
                            $action,
                        ];
                    }
                }
                $payload = [
                    "data" => $$admins_list,
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => __($e->getMessage())
                ];
            }
        } else {
            $payload = [
                'error' => true,
                'message' => __('An error occurred during process. Please try again later.')
            ];
        }
        $this->_sendJson($payload);
    }
    public function adminstatusAction()
    {
        if ($customer_id = $this->getRequest()->getParam('customer_id')) {
            $Admins = new Xdelivery_Model_Admins();
            if($this->getRequest()->getParam('status') == 2) {
                $Admins->find(['customer_id' => $customer_id]);
                $Admins->delete();
            } else {
                $Admins->addData([
                    'value_id' => $this->getRequest()->getParam('value_id'),
                    'customer_id' => $this->getRequest()->getParam('customer_id'),
                ])->save();
            }
            $data = [
                'success' => true,
                'message' => __('User status has been updated successfully.'),
                'message_loader' => 0,
                'message_button' => 0,
                'message_timeout' => 2
            ];
        } else {
            $data = [
                'error' => true,
                'message' => __('An error occurred while deleting the push. Please try again later.')
            ];
        }
        $this->_sendJson($data);
    }

    public function adminstoreAction()
    {
        if ($customer_id = $this->getRequest()->getParam('customer_id')) {
            $Admins = new Xdelivery_Model_Admins();
            $Admins->find(['customer_id' => $customer_id]);
            $Admins->addData([
                'id' => $Admins->getId(),
                'customer_id' => $customer_id,
                'store_id' => $this->getRequest()->getParam('store_id'),
            ])->save();
            $data = [
                'success' => true,
                'message' => p__('xdelivery','Admin store has been updated successfully.'),
                'message_loader' => 0,
                'message_button' => 0,
                'message_timeout' => 1
            ];
        } else {
            $data = [
                'error' => true,
                'message' => p__('xdelivery','An error occurred while deleting the push. Please try again later.')
            ];
        }
        $this->_sendJson($data);
    }
}
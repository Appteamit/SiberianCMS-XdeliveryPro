<?php

class Xdelivery_NotificationController extends Application_Controller_Default {
    public function updloadfile($icon,$app_id) {
        $product_icon = "$icon";
        $ext = pathinfo($icon, PATHINFO_EXTENSION);
        $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
        $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
        if (!is_dir($dir_image)) {
            mkdir($dir_image, 0775, true);
        }
        if (!is_dir($dir_image . "/features")) {
            mkdir($dir_image . "/features", 0775, true);
        }
        if (!is_dir($dir_image . "/features/xdelivery")) {
            mkdir($dir_image . "/features/xdelivery", 0775, true);
        }
        if (!is_dir($dir_image . "/features/xdelivery/notification_images")) {
            mkdir($dir_image . "/features/xdelivery/notification_images", 0775, true);
        }
        $dir_image .= "/features/xdelivery/notification_images/";
        $image_name = str_replace(" ", "_", $icon);
        if (file_exists($file)) {
            copy($file, $dir_image . $image_name);
            $product_icon = $icon;
        } else {
            $product_icon = $icon;
        }
        return $product_icon;
    }
    public function saveAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $application = $this->getApplication();
            $appId = $application->getId();
            $data['app_id'] = $appId;
            $errors = '';
            if(empty($data['notification_order_status'])){
                throw new Exception(p__("xdelivery",'Order Status cannot be empty'));
            }
            // dd($data);
            if (array_key_exists('notification_customer_is_push', $data) && array_key_exists('notification_is_customer', $data)){
                if (empty($data['notification_customer_push_title'])) { 
                    $errors .= p__("xdelivery",'push title cannot be empty.') . "<br>";
                }
                if (empty($data['notification_customer_push_text'])) { 
                    $errors .= p__("xdelivery",'push text cannot be empty.') . "<br>";
                }
                if (!empty($data['notification_customer_push_cover'])) {
                    $icon = $data['notification_customer_push_cover'];
                    $data['notification_customer_push_cover'] = $this->updloadfile($icon,$appId);
                }else{
                    $data['notification_customer_push_cover'] = '';
                }
            }else{
                $data['notification_customer_is_push'] = 0;
                // $data['notification_is_customer'] = 0;
            }
            if (array_key_exists('notification_customer_is_email', $data) && array_key_exists('notification_is_customer', $data)){
                if (empty($data['notification_customer_email_subject'])) { 
                    $errors .= p__("xdelivery",'email subject cannot be empty.') . "<br>";
                }
                if (empty($data['notification_customer_email_body'])) { 
                    $errors .= p__("xdelivery",'email body cannot be empty.') . "<br>";
                }
            }else{
                $data['notification_customer_is_email'] = 0;
                // $data['notification_is_customer'] = 0;
            }
            if(array_key_exists('notification_is_customer', $data)){
                $data['notification_is_customer'] = 1;
            }else{
                $data['notification_is_customer'] = 0;
            }
            if(array_key_exists('notification_is_admin', $data)){
                $data['notification_is_admin'] = 1;
            }else{
                $data['notification_is_admin'] = 0;
            }
            if (array_key_exists('notification_customer_is_sms', $data) && array_key_exists('notification_is_customer', $data)){
                if (empty($data['notification_customer_sms_text'])) { 
                    $errors .= p__("xdelivery",'sms text cannot be empty.') . "<br>";
                }
            }else{
                $data['notification_customer_is_sms'] = 0;
            }
            if (array_key_exists('notification_customer_is_whatsender', $data) && array_key_exists('notification_is_customer', $data)){
                if (empty($data['notification_customer_whatsender_text'])) { 
                    $errors .= p__("xdelivery",'whatsender text cannot be empty.') . "<br>";
                }
            }else{
                $data['notification_customer_is_whatsender'] = 0;
                // $data['notification_is_customer'] = 0;
            }
            if (!empty($errors)) {
                throw new Exception($errors);
            }
            // admin
            if (array_key_exists('notification_admin_is_push', $data) && array_key_exists('notification_is_admin', $data)){
                if (empty($data['notification_admin_push_title'])) { 
                    $errors .= p__("xdelivery",'push title cannot be empty.') . "<br>";
                }
                if (empty($data['notification_admin_push_text'])) { 
                    $errors .= p__("xdelivery",'push text cannot be empty.') . "<br>";
                }
                if (!empty($data['notification_admin_push_cover'])) {
                    $icon = $data['notification_admin_push_cover'];
                    $data['notification_admin_push_cover'] = $this->updloadfile($icon,$appId);
                }
            }else{
                $data['notification_admin_is_push'] = 0;
                // $data['notification_is_admin'] = 0;
            }
            if (array_key_exists('notification_admin_is_email', $data) && array_key_exists('notification_is_admin', $data)){
                if (empty($data['notification_admin_email_subject'])) { 
                    $errors .= p__("xdelivery",'email subject cannot be empty.') . "<br>";
                }
                if (empty($data['notification_admin_email_body'])) { 
                    $errors .= p__("xdelivery",'email body cannot be empty.') . "<br>";
                }
            }else{
                $data['notification_admin_is_email'] = 0;
                // $data['notification_is_admin'] = 0;
            }
            if (array_key_exists('notification_admin_is_sms', $data) && array_key_exists('notification_is_admin', $data)){
                if (empty($data['notification_admin_sms_text'])) { 
                    $errors .= p__("xdelivery",'sms text cannot be empty.') . "<br>";
                }
            }else{
                $data['notification_admin_is_sms'] = 0;
                // $data['notification_is_admin'] = 0;
            }
            if (array_key_exists('notification_admin_is_whatsender', $data) && array_key_exists('notification_is_admin', $data)){
                if (empty($data['notification_admin_whatsender_text'])) { 
                    $errors .= p__("xdelivery",'whatsender text cannot be empty.') . "<br>";
                }
            }else{
                $data['notification_admin_is_whatsender'] = 0;
                // $data['notification_is_admin'] = 0;
            }

            if (!empty($errors)) {
                throw new Exception($errors);
            }
            $check = (new Xdelivery_Model_Notification())->find([
                'notification_order_status'=> $data['notification_order_status'],
                'app_id' => $data['app_id'],
                'value_id' => $data['value_id'],
            ]);

            if (!count($check->getData())){
                $msg = "Notification data saved successfully.";
            } else {
                $data['notification_id'] = $check->getId();
                $msg = "Notification data updated successfully.";
            }
            $notification = new Xdelivery_Model_Notification();
            $notification->setData($data)->save();
            $data = [
                    "success" => 1,
                    "message" => p__("xdelivery", $msg)
                ];
        } catch (Exception $e) {
            $data = array(
                "error" => 1,
                "message" => p__("xdelivery",$e->getMessage())
            );
        }
        $this->_sendJson($data);
    }
    public function editNotificationAction()
    {
        if ($datas= $this->getRequest()->getParams()) {
            $application = $this->getApplication();
            $appId = $application->getId();
            $notification = (new Xdelivery_Model_Notification())->find(['app_id'=> $appId, 'value_id'=> $datas['value_id'],'notification_order_status'=> $datas['notification_order_status']])->getData();
            $data = [
                'success' => true,
                'notification' => $notification,
            ];
        } else {
            $data = [
                'error' => true,
                'message' => p__("xdelivery",'An error occurred while deleting the push. Please try again later.')
            ];
        }
        $this->_sendJson($data);
    }
}
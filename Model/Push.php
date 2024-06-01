<?php

class Xdelivery_Model_Push extends Core_Model_Default
{

    /**
     * Notification for approval
     */
    public function send($data)
    {
    	if (Push_Model_Message::hasIndividualPush() && class_exists("Push_Model_Customer_Message") && $data['receiver_id'] > 0 ) {

                $message_push = new Push_Model_Message();
                $message_push->setMessageType(Push_Model_Message::TYPE_PUSH);
                $data_push = [
                    "title" => $data['title'],
                    "text" => $data['text'],
                    "send_at" => time(),
                    "action_value" => $data['value_id'],
                    "value_id" => $data['value_id'],
                    "type_id" => $message_push->getMessageType(),
                    "app_id" => $data['app_id'],
                    "send_to_all" => 0,
                    "send_to_specific_customer" => 1,
                ];
               $message_push->setData($data_push)->save();

               $customer_message = new Push_Model_Customer_Message();
                $customer_message_data = [
                    "customer_id" => $data['receiver_id'],
                    "message_id" => $message_push->getId(),
                ];
                $customer_message->setData($customer_message_data);
                $customer_message->save();

                return true;                
            }

    }

}
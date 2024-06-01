<?php

class Xdelivery_Model_Whatsender extends Core_Model_Default {

    
     /**
     * @param $key, $params
     * @param array $params
     * @return Xdelivery_Model_Whatsender[]
     */
    public function sent($apikey, $params = [])
    {
       
        $phone = $params['phone'];
        $message = $params['message'];
        $sender = $params['sender'];
        //$url = 'https://api.whatsender.it/api/post.php'; 
        $url = 'https://node.whatsender.it/send';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT,30);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array( 'token' => $apikey, 'receiver' => $phone, 'msgtext' => $message)));
        $response = curl_exec($curl);
        curl_close($curl);

        return $response ; 
    }
  
    
}


<?php

/**
 * Class Xdelivery_StoreController
 */
class Xdelivery_StoreController extends Application_Controller_Default
{   
    
    /**
     * Load a stores
     */
    public function listAction()
    {
        $this->loadPartials();
    }

    
     /**
     * Create a new Store
     */
    public function addAction()
    {   
        $this->loadPartials();
    }


    /**
     * edit category
     */
    public function editAction()
    {   
         $model = (new Xdelivery_Model_Store());  
            if ($id = $this->getRequest()->getParam('id')) {
                $model->find($id); 
                if (!$model->getStoreId()) {
                        $this->getRequest()->addError( p__("xdelivery",  "This store does not exist."));
                }
            } 

        $this->loadPartials();
        $this->getLayout()->getPartial('content')->setCurrentStore($model);
    }

    /**
     * fetch stores
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
                "filter" => $filter
            ];
          
            $value_id = (new Xdelivery_Model_Xdelivery())->getCurrentValueId();
            $application = $this->getApplication();
            
            $stores = (new Xdelivery_Model_Store())
                ->findByValueId($value_id, $params);
            // dd($stores);
            $countAll = (new Xdelivery_Model_Store())->countAllForApp($value_id);
            $countFiltered =   (new Xdelivery_Model_Store())->countAllForApp($value_id, $params);

            $storeJson = [];
            foreach ($stores as $store) {
                $data = $store->getData();
                $data['is_active'] = $data['is_active'] == 1 ? p__('xdelivery', "Active") : p__('xdelivery', "In Active"); 
                $data['is_default_hide'] = $data['is_default'] == 1 ? "hide" : ""; 
                $data['is_default'] = $data['is_default'] == 1 ? "(".p__('xdelivery', "Main Store"). ")": "";  

                $storeJson[] = $data;
            }

            $payload = [
                "records" => $storeJson,
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

    /**
     *
     */
    public function editpostAction()
    {
          try
        {
            $values = $this->getRequest()->getPost();
            $application = $this->getApplication();
            $form = new Xdelivery_Form_Store_From();
            if ($form->isValid($values))
            {       
                if(!empty($values['slider'])){
                    foreach ($values['slider'] as $key => $value) {
                       if (file_exists(Core_Model_Directory::getTmpDirectory(true) . "/" . $value)) {
                                list($relativePath, $filename) = $this->_getImageData($value);
                                $values['image'] = $relativePath . '/' . $filename;                            
                        }
                    }
                }

                $settings = new Xdelivery_Model_Store();
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
    
       //function for delete category  
    public function deleteAction() {
      try {
            $request = $this->getRequest();
            $id = $request->getParam("id", null);
            $model = new Xdelivery_Model_Store();
            $model->find(array('store_id' => $id));
            $model->setIsActive(2);
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


      /**
     * @param $image
     * @return array
     * @throws Siberian_Exception
     */
    private function _getImageData($image)
    {

        $img_src = Core_Model_Directory::getTmpDirectory(true) . "/" . $image;
        $info = pathinfo($img_src);
        $filename = $info['basename'];
        $relativePath = $this->getCurrentOptionValue()->getImagePathTo();
        $img_dst = Application_Model_Application::getBaseImagePath() . $relativePath;

        if (!is_dir($img_dst)) {
            mkdir($img_dst, 0777, true);
        }
        $img_dst .= '/' . $filename;
        rename($img_src, $img_dst);
        
        if (!file_exists($img_dst)) {
            throw new Siberian_Exception(p__('xdelivery', 'An error occurred while saving your picture. Please try againg later.'));
        }
        return [$relativePath, $filename];
    }

}
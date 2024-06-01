<?php

/**
 * Class Xdelivery_ExtraController
 */
class Xdelivery_ExtraController extends Application_Controller_Default
{

    /**
     * Load a extra
     */
    public function listAction()
    {
    	$this->loadPartials();
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
                "filter" => $filter                
            ];
          
            $value_id = (new Xdelivery_Model_Xdelivery())->getCurrentValueId();
            $application = $this->getApplication();
            
            $categories = (new Xdelivery_Model_ExtraOption())
                ->findByValueId($value_id, $params);

            $countAll = (new Xdelivery_Model_ExtraOption())->countAllForApp($value_id);
            $countFiltered =   (new Xdelivery_Model_ExtraOption())->countAllForApp($value_id, $params);

            $categoryJson = [];
            foreach ($categories as $category) {
                $data = $category->getData();
                $data['is_active'] = $data['is_active'] == 1 ? p__('xdelivery', "Active") : p__('xdelivery', "In Active"); 
                $categoryJson[] = $data;
            }

            $payload = [
                "records" => $categoryJson,
                "queryRecordCount" => $countFiltered[0],
                "totalRecordCount" => $countAll[0],
                "value_id" => $value_id
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
     * Create a new extra
     */
    public function addAction()
    {   
      	$this->loadPartials();
    }

    /**
     * Save extra
     */
    public function saveAction() {

       if($param = $this->getRequest()->getPost()) {     
              
           try {  

            $value_id = (new Xdelivery_Model_Xdelivery())->getCurrentValueId();
            $maxPostion = (new Xdelivery_Model_ExtraOption())->maxPosition($value_id);            
            $position = $maxPostion[0] + 1;
            $param['is_active'] = $param['is_active'] == 1 ? 1: 0;
           
            $model = (new Xdelivery_Model_ExtraOption())
                ->find(['id' => $param['id']])
                ->setValueId($value_id)
                ->setName($param['name'])
                ->setCategoryType($param['category_type'])
                ->setMaximumOption($param['maximum_option'])
                ->setMinimumOption($param['minimum_option'])
                ->setIsRequired($param['is_required'])
                ->setIsActive($param['is_active']);
                
            if(empty($param['id'])){
                $model->setPosition($position);
            }
                                    
            $model->save();
            $option_id = $model->getId();

            if(!empty($param['id'])){
                $deleteCategory = (new Xdelivery_Model_Products())->deleteQuery('xdelivery_extra_option_categories', 'option_id', $option_id );
            }

            foreach ($param['product_category'] as $key => $value) {
                 $modelCategory = (new Xdelivery_Model_ExtraOptionCategory())
                    ->find(['option_id' => $option_id, 'category_id' => $value])
                    ->setOptionId($option_id)
                    ->setCategoryId($value)
                    ->save();
            }

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
     * edit extra
     */
    public function editAction()
    {   
         $model = (new Xdelivery_Model_ExtraOption());  
            if ($id = $this->getRequest()->getParam('id')) {
                $model->find($id); 
                if (!$model->getId()) {
                        $this->getRequest()->addError( p__("xdelivery",  "This extra options does not exist."));
                }
            }

        
        $value_id = (new Xdelivery_Model_Xdelivery())->getCurrentValueId();
        $categories = (new Xdelivery_Model_Category())->getCategorySubCategory($value_id);

        $extraOptionCategory = (new Xdelivery_Model_ExtraOptionCategory())
                    ->findAll(['option_id' => $id]);

        $selectedCategory = [];
        foreach ($extraOptionCategory as $key => $value) {
              $selectedCategory[] = $value->getCategoryId(); 
        }
       
        $this->loadPartials();
        $this->getLayout()->getPartial('content')->setCurrentOption($model)->setCategory($categories)->setSelectedCategory($selectedCategory);
    }

   //function for delete  
    public function deleteAction() {
      try {
            $request = $this->getRequest();
            $id = $request->getParam("id", null);
            $model = new Xdelivery_Model_ExtraOption();
            $model->find(array('id' => $id));
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
     * @param $param
     * @return array
     * @throws Siberian_Exception
     */
    public function sortableAction() {
        $payload = array();
        if($data = $this->getRequest()->getPost('data')) { 
            $model = new Xdelivery_Model_ExtraOption(); 
            $model->sortable($data);                
            $payload = array("success" => 1 );
        }
        
        $this->_sendJson($payload);     
    }
   /**
     * manage options
     */
    public function optionsAction()
    {   
        $model = (new Xdelivery_Model_ExtraOption());  
        if ($id = $this->getRequest()->getParam('id')) { 
                $model->find($id); 
                if (!$model->getId()) {
                        $this->getRequest()->addError( p__("xdelivery",  "This category does not exist."));
                }
            
            $this->loadPartials();
            $this->getLayout()->getPartial('content')->setCategory($model);
        }      
        
    }



     public function saveOptionAction() {

       if($name = $this->getRequest()->getPost('name')) {   
            $price = $this->getRequest()->getPost('price');
            try {  
               $value_id = $this->getRequest()->getPost('value_id');
                $option_id =  $this->getRequest()->getPost('option_id');
               
                foreach ($name as $key => $value) {

                    if(!empty($value)) {
                       $model = (new Xdelivery_Model_ExtraOptionValue())
                        ->find(['name' => $value, 'option_id' => $option_id ])
                        ->setValueId($value_id)
                        ->setOptionId($option_id)
                        ->setName($value)
                        ->setPrice($price[$key])
                        ->setPosition( $key+1)
                        ->setIsActive(1)
                        ->save(); 
                    }                 
                }                  
               
                $this->getSession()->addSuccess(p__('xdelivery', "Info successfully saved"));

                $html = [
                    "success" => 1
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


    //function for delete  
    public function deleteOptionAction() {
      try {
            $request = $this->getRequest();
            $id = $request->getParam("id", null);
            $model = new Xdelivery_Model_ExtraOptionValue();
            $model->find(array('id' => $id));
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


     
}
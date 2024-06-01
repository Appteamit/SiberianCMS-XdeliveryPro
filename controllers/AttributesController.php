<?php

/**
 * Class Xdelivery_AttributesController
 */
class Xdelivery_AttributesController extends Application_Controller_Default
{

    /**
     * Load a Attributes
     */
    public function listAction()
    {
    	$this->loadPartials();
    }

     /**
     * Create a new Attributes
     */
    public function addAction()
    {   
     	$this->loadPartials();
    }


    
    /**
     * edit Attributes
     */
    public function editAction()
    {   
         $model = (new Xdelivery_Model_Attribute());  
            if ($id = $this->getRequest()->getParam('id')) {
                $model->find($id); 
                if (!$model->getId()) {
                        $this->getRequest()->addError( p__("xdelivery",  "This attributes does not exist."));
                }
            }       

        $this->loadPartials();
        $this->getLayout()->getPartial('content')->setCurrentAttribute($model);
    }


    public function saveAction() {

       if($param = $this->getRequest()->getPost()) {     
              
           try {  

            $value_id = (new Xdelivery_Model_Xdelivery())->getCurrentValueId();
            $maxPostion = (new Xdelivery_Model_Attribute())->maxPosition($value_id);
            $position = $maxPostion[0] + 1;
            $param['is_active'] = $param['is_active'] == 1 ? 1: 0;
               
                $model = (new Xdelivery_Model_Attribute())
                    ->find(['id' => $param['id']])
                    ->setValueId($value_id)
                    ->setAttributeName($param['attribute_name'])
                    ->setIsFilterable($param['is_filterable'])
                    ->setIsActive($param['is_active']);
                    
                    if(empty($param['id'])){
                        $model->setPosition($position);
                    }
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
     * fetch Attributes
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
            
            $attributes = (new Xdelivery_Model_Attribute())
                ->findByValueId($value_id, $params);

            $countAll = (new Xdelivery_Model_Attribute())->countAllForApp($value_id);
            $countFiltered =   (new Xdelivery_Model_Attribute())->countAllForApp($value_id, $params);

            $attributeJson = [];
            foreach ($attributes as $attribute) {
                $data = $attribute->getData();
                $data['is_active'] = $data['is_active'] == 1 ? p__('xdelivery', "Active") : p__('xdelivery', "In Active"); 
                $data['is_filterable'] = $data['is_filterable'] == 1 ? p__('xdelivery', "Yes") : p__('xdelivery', "No");   
                $attributeJson[] = $data;
            }

            $payload = [
                "records" => $attributeJson,
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


   //function for delete Attributes  
    public function deleteAction() {
      try {
            $request = $this->getRequest();
            $id = $request->getParam("id", null);
            $model = new Xdelivery_Model_Attribute();
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
            $parent_id = $this->getRequest()->getPost('parent_id');
            $model = new Xdelivery_Model_Attribute();                     
            $model->sortable($data, $parent_id);                
            $payload = array("success" => 1 );
        }
        
        $this->_sendJson($payload);     
    }


   /**
     * edit Attributes
     */
    public function valuesAction()
    {   
         $model = (new Xdelivery_Model_Attribute());  
            if ($id = $this->getRequest()->getParam('id')) {
                $model->find($id); 
                if (!$model->getId()) {
                        $this->getRequest()->addError( p__("xdelivery",  "This attributes does not exist."));
                }
            }       

        $this->loadPartials();
        $this->getLayout()->getPartial('content')->setCurrentAttribute($model);
    }

 
     public function saveValueAction() {

       if($valueNames = $this->getRequest()->getPost('value_name')) {     
              
           try {  

                $value_id = $this->getRequest()->getPost('value_id');
                $attribute_id =  $this->getRequest()->getPost('attribute_id');
               
                foreach ($valueNames as $key => $value) {

                   $model = (new Xdelivery_Model_AttributeValues())
                    ->find(['value_name' => $value, 'attribute_id' => $attribute_id ])
                    ->setValueId($value_id)
                    ->setAttributeId($attribute_id)
                    ->setValueName($value)
                    ->setPosition( $key+1)
                    ->setIsActive(1)
                    ->save();

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


    //function for delete Attributes  
    public function deleteValueAction() {
      try {
            $request = $this->getRequest();
            $id = $request->getParam("id", null);
            $model = new Xdelivery_Model_AttributeValues();
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
<?php

/**
 * Class Xdelivery_CategoryController
 */
class Xdelivery_CategoryController extends Application_Controller_Default
{

    /**
     * Load a category
     */
    public function listAction()
    {
    	$this->loadPartials();
    }

     /**
     * Create a new category
     */
    public function addAction()
    {   

        $model = (new Xdelivery_Model_Category()); 
        $value_id = (new Xdelivery_Model_Xdelivery())->getCurrentValueId(); 
        $parentCategory = $model->findAll(['value_id' => $value_id, 'parent_id' => 0, 'is_active' => 1 ], 'category_name ASC')->toArray();
       
    	$this->loadPartials();
        $this->getLayout()->getPartial('content')->setParentCategory($parentCategory);
    }


     /**
     * Create a new category
     */
    public function subcategoryAction()
    {   
        $model = (new Xdelivery_Model_Category());  
        if ($id = $this->getRequest()->getParam('id')) { 
                $model->find($id); 
                if (!$model->getId()) {
                        $this->getRequest()->addError( p__("xdelivery",  "This category does not exist."));
                }
            
            $this->loadPartials();
            $this->getLayout()->getPartial('content')->setParentCategory($model)->setParentId($id);
        }      
        
    }

    /**
     * edit category
     */
    public function editAction()
    {   
         $model = (new Xdelivery_Model_Category());  
            if ($id = $this->getRequest()->getParam('id')) {
                $model->find($id); 
                if (!$model->getId()) {
                        $this->getRequest()->addError( p__("xdelivery",  "This category does not exist."));
                }
            }

        $modelParent = (new Xdelivery_Model_Category()); 
        $value_id = (new Xdelivery_Model_Xdelivery())->getCurrentValueId(); 
        $parentCategory = $modelParent->findAll(['value_id' => $value_id, 'parent_id' => 0, 'is_active' => 1 ], 'category_name ASC')->toArray();

        $this->loadPartials();
        $this->getLayout()->getPartial('content')->setCurrentCategory($model)->setParentCategory($parentCategory);
    }


    public function saveAction() {

       if($param = $this->getRequest()->getPost()) {     
              
           try {  

            $value_id = (new Xdelivery_Model_Xdelivery())->getCurrentValueId();
 
            if(!empty($param['slider'])){
                foreach ($param['slider'] as $key => $value) {
                   if (file_exists(Core_Model_Directory::getTmpDirectory(true) . "/" . $value)) {
                            list($relativePath, $filename) = $this->_getImageData($value);
                            $param['image'] = $relativePath . '/' . $filename;                            
                    }
                }
            }

            $maxPostion = (new Xdelivery_Model_Category())->maxPosition($value_id , $param['parent_id']);
            $position = $maxPostion[0] + 1;
            $param['is_active'] = $param['is_active'] == 1 ? 1: 0;
               
                $model = (new Xdelivery_Model_Category())
                    ->find(['id' => $param['id']])
                    ->setValueId($value_id)
                    ->setCategoryName($param['category_name'])
                    ->setShortSummery($param['short_summery'])
                    ->setParentId($param['parent_id'])
                    ->setIsPopularSearch($param['is_popular_search'])
                    ->setIsActive($param['is_active']);
                    
                    if(empty($param['id'])){
                        $model->setPosition($position);
                    }

                    if(!empty($param['image'])){
                        $model->setImage($param['image']);
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
     * fetch category
     */
     public function findAllAction() {
        
        try {
            $request = $this->getRequest();
            $limit = $request->getParam("perPage", 25);
            $offset = $request->getParam("offset", 0);
            $sorts = $request->getParam("sorts", []);
            $queries = $request->getParam("queries", []);
            $parent_id = $request->getParam("parent_id", 0);

            $filter = null;
            if (array_key_exists("search", $queries)) {
                $filter = $queries["search"];
            }
            
            $params = [
                "limit" => $limit,
                "offset" => $offset,
                "sorts" => $sorts,
                "filter" => $filter,
                "parent_id" => $parent_id
            ];
          
            $value_id = (new Xdelivery_Model_Xdelivery())->getCurrentValueId();
            $application = $this->getApplication();
            
            $categories = (new Xdelivery_Model_Category())
                ->findByValueId($value_id, $params);

            $countAll = (new Xdelivery_Model_Category())->countAllForApp($value_id);
            $countFiltered =   (new Xdelivery_Model_Category())->countAllForApp($value_id, $params);

            $categoryJson = [];
            foreach ($categories as $category) {
                $data = $category->getData();
                $data['is_active'] = $data['is_active'] == 1 ? p__('xdelivery', "Active") : p__('xdelivery', "In Active"); 
                $data['is_popular_search'] = $data['is_popular_search'] == 1 ? p__('xdelivery', "Yes") : p__('xdelivery', "No");   
                $categoryJson[] = $data;
            }

            $payload = [
                "records" => $categoryJson,
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


   //function for delete category  
    public function deleteAction() {
      try {
            $request = $this->getRequest();
            $id = $request->getParam("id", null);
            $model = new Xdelivery_Model_Category();
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
                    $model = new Xdelivery_Model_Category();                     
                    $model->sortable($data, $parent_id);                
                    $payload = array("success" => 1 );
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
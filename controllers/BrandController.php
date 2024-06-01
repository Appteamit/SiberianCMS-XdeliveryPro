<?php

/**
 * Class Xdelivery_BrandController
 */
class Xdelivery_BrandController extends Application_Controller_Default
{

    /**
     *Home screen
     */
    public function listAction() {
        $this->loadPartials();
    }

    /**
     *brand add
     */
    public function addAction() {
        $this->loadPartials();
    }

    /**
     *brand Edit
     */
    public function editAction() {
        $model = (new Xdelivery_Model_Brand());  
        if ($id = $this->getRequest()->getParam('id')) {
            $model->find($id); 
            if (!$model->getBrandId()) {
                    $this->getRequest()->addError( p__("xdelivery",  "This brand does not exist."));
            }
        }
        $this->loadPartials();
        $this->getLayout()->getPartial('content')->setBrand($model);
         
    }

    /**
     *brand delete
     */
    public function deleteAction() {
        
        try {

        $model = (new Xdelivery_Model_Brand());  
        if ($id = $this->getRequest()->getParam('id')) {
            $model->find($id); 
            if (!$model->getBrandId()) {
                    $this->getRequest()->addError( p__("xdelivery",  "This brand does not exist."));
            }else{
                $model->setIsDelete(1);
                $model->save();
            }
        }
        
            $payload = [
                'success' => true,
                'message' => p__('xdelivery', 'Successfully deleted'),
                'datas' => $datas
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
     *category Save
     */
    public function saveAction() {
       
        if($param = $this->getRequest()->getPost()) {
      
        try {
            
            $is_active = $param['is_active'] == 1 ? 1: 0;
            $model = (new Xdelivery_Model_Brand())
                    ->find(['brand_id' => $param['brand_id']])
                    ->setValueId($param['value_id'])
                    ->setName($param['name'])
                    ->setDescription($param['description'])
                    ->setIsActive($is_active);

                if(!empty($param['slider'])){
                    foreach ($param['slider'] as $iKey => $iValue) {
                        if (file_exists(Core_Model_Directory::getTmpDirectory(true) . "/" . $iValue)) {
                                list($relativePath, $filename) = $this->_getImageData($iValue);
                                $imageURL = $relativePath . '/' . $filename;
                                $model->setImage($imageURL);
                        }
                    }
                }

                $model->save();

              $payload = [
                    'success' => true,
                    'message' => p__('xdelivery', 'Successfully save'),
                    'datas' => $datas
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

       /**
     * @param $image
     * @return array
     * @throws Siberian_Exception
     */
    private function _getImageData($image) {

        $img_src = Core_Model_Directory::getTmpDirectory(true) . "/" . $image;

        $info = pathinfo($img_src);
        $filename = $info['basename'];
        $relativePath = '/xdelivery/'.$this->getApplication()->getId().'/brand';
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
            
            $brands = (new Xdelivery_Model_Brand())
                ->findByValueId($value_id, $params);

            $countAll = (new Xdelivery_Model_Brand())->countAllForApp($value_id);
            $countFiltered =   (new Xdelivery_Model_Brand())->countAllForApp($value_id, $params);

            $brandsJson = [];
            foreach ($brands as $brand) {
                $data = $brand->getData();
                $data['is_active'] = $data['is_active'] == 1 ? p__('xdelivery', "Active") : p__('xdelivery', "In Active"); 
                $data['image'] = empty($data['image']) ? '/app/local/modules/Xdelivery/resources/design/desktop/flat/images/dummy-image.jpg' : $data['image'];
                
                $brandsJson[] = $data;
            }

            $payload = [
                "records" => $brandsJson,
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
 

    
}

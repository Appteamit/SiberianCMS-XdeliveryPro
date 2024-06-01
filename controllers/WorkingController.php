<?php

/**
 * Class Xdelivery_WorkingController
 */
class Xdelivery_WorkingController extends Application_Controller_Default
{

    /**
     *
    */
    public function saveAction() {   
        $payload = array();
        if ($data = $this->getRequest()->getPost()) {
            try {

                $form = new Xdelivery_Form_Store_WorkingDays();
                if ($form->isValid($data)) {
                    
                    $working = new Xdelivery_Model_WorkingTimes();
                    $working
                            ->find($data['working_id'])
                            ->setOpeningTime($data['opening_time'])
			                ->setClosingTime($data['closing_time'])
                            ->setEnableDelivery($data['enable_delivery'])
                            ->setEnablePickup($data['enable_pickup'])
			                ->save();

                    $payload = array(
                        'success' => true,
                        'success_message' => p__('xdelivery', 'Saved successfull'),
                        'message_timeout' => 2,
                        'message_button' => 0,
                        'message_loader' => 0
                    );
                } else {
                    /** Do whatever you need when form is not valid */
                    $payload = array(
                        "error" => 1,
                        "message" => $form->getTextErrors(),
                        "errors" => $form->getTextErrors(true),
                    );
                }

            } catch (Exception $e) {
                $payload = array(
                    'error' => true,
                    'message' => $e->getMessage()
                );
            }
        }

         $this->_sendJson($payload);
    }


    public function loadformAction(){
        
        if ($working_id = $this->getRequest()->getParam("working_id")) {
        	$value_id = $this->getRequest()->getParam("value_id");
            try {
                 
                $workingModel = new Xdelivery_Model_WorkingTimes();
                $workingModel->find($working_id);
                if($workingModel->getId()) {
                 
                    $form = new Xdelivery_Form_Store_WorkingDays();
                    $form->populate($workingModel->getData());
                    $form->setElementValueById('value_id', $value_id);
                    $form->addNav("edit-nav-workingdays", "Save", false); 
                    $form->removeNav("nav-add-xdelivery");              
                    $form->setElementValueById('working_id', $workingModel->getId());
                    
                    $payload = array(
                        "check" => 'ready for use',
                        "success"   => true,
                        "form"      => $form->render(),
                        "message"   => p__('xdelivery', "Saved successfull"),
                    );

                }else{
                    $payload = array(
                        "error"     => true,
                        "message"   => p__('xdelivery', 'Working you are trying to edit does not exists.'),
                    );
                }
              

            } catch (Exception $e) {
                $payload = array(
                    'error' => true,
                    'message' => $e->getMessage()
                );
            }
        }

        $this->_sendHtml($payload);
    }


}
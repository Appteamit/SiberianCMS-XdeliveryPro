<?php
/**
 * Class Xdelivery_PrinterController
 */
class Xdelivery_PrinterController extends Application_Controller_Default
{

    public function printersAction()
    {

        if ($datas = $this->getRequest()->getQuery()) {

            try {

                $printer = new Xdelivery_Model_Printers();

                $printers = $printer->findAll([

                    'app_id = ?' => $datas['app_id'],

                    'value_id = ?' => $datas['value_id']

                ]);

                $data = [];

                if (count($printers)) {

                    $ini_url = $this->getRequest()->getBaseUrl() . '/xdelivery/printer/downloadini/printer_id/';

                    foreach ($printers as $printer) {

                        $data[] = [

                            $printer->getPrinterId(),

                            $printer->getPrinterTitle(),

                            $printer->getPrinterUsername(),

                            $printer->getPrinterPassword(),

                            '<a class="btn btn-xs color-blue" href="' . $ini_url . $printer->getPrinterId() . '/protocol/1/type/' . $printer->getPrinterType() . '">' .p__('xdelivery', 'download') . '</a> <span class="badge badge-info">v' . $printer->getPrinterType() . '.0</span>',//'<a class="link_color" href="'.$ini_url.$printer->getPrinterId().'/protocol/1/type/'.$printer->getPrinterType().'">'.__('HTTP').'</a> | <a class="link_color" href="'.$ini_url.$printer->getPrinterId().'/protocol/2/type/'.$printer->getPrinterType().'">'.__('HTTPS').'</a><span class="badge badge-info">v'.$printer->getPrinterType().'.0</span>',

                            $printer->getStoreId(),

                            $printer->getCurrencyCode() . ' - ' . $printer->getCurrencySymbol(),

                            '<a class="btn btn-xs color-red" href="javascript:;" onclick="deletePrinter(' . $printer->getPrinterId() . ');">' .p__('xdelivery', 'Delete') . '</a>',

                        ];

                    }

                }

                $payload = [

                    "data" => $data,

                ];

            } catch (\Exception $e) {

                $payload = [

                    'error' => true,

                    'message' =>p__('xdelivery', $e->getMessage())

                ];

            }

        } else {

            $payload = [

                'error' => true,

                'message' =>p__('xdelivery', 'An error occurred during process. Please try again later.')

            ];

        }

        $this->_sendJson($payload);

    }



    public function downloadiniAction()
    {

        if ($printer_id = $this->getRequest()->getParam('printer_id')) {

            $protocol = $this->getRequest()->getParam('protocol');

            $type = $this->getRequest()->getParam('type');

            $printer = new Xdelivery_Model_Printers();

            $printer->find($printer_id);

            // $base_url = 'https://webhook.site/381f5380-6fb2-49ba-aed3-21a883f6c1c2'; //$base_url = $this->getRequest()->getBaseUrl();

            $base_url = $this->getRequest()->getBaseUrl();
            $port = 80;

            $http_https = preg_replace("/^https:/i", "http:", $base_url);

            if ($protocol == 2) {

                $port = 443;

                $http_https = preg_replace("/^http:/i", "https:", $base_url);

            }
            // $folder_name = 'testing';
            $folder_name = 'GT5000W';

            if ($type == 3) {

                $folder_name = 'GT5000SW';

            }

            $domain = rtrim(str_replace(array("http://", "https://", "HTTP://", "HTTPS://"), "", $this->getRequest()->getBaseUrl()), '/');
            $baseUrl = $this->getRequest()->getBaseUrl();
            $ini_raw_data = (new Xdelivery_Model_Printers())::IniFileData($folder_name);
            
            $ini_file_contents = str_replace([

                "@@RES_ID@@",

                "@@IP@@",

                "@@PORT@@",

                "@@FILE_PATH@@",

                "@@CALLBACK_URL@@",

                "@@USER_NAME@@",

                "@@PASSWORD@@",

                "@@AUTO_CHECK@@"

            ], [
                $printer->getStoreId(),
               rtrim(str_replace(array("http://", "https://", "HTTP://", "HTTPS://"), "", $base_url), '/'),
                $port,
                $http_https."/xdelivery/public_order/getorder",
                $http_https."/xdelivery/public_order/updateorderstatus",
                $printer->getPrinterUsername(),
                $printer->getPrinterPassword(),
                30
            ], $ini_raw_data);
            header('Content-Disposition: attachment; filename="goodcom.ini"');

            header('Content-Type: text/plain');

            header('Content-Length: ' . strlen($ini_file_contents));

            header('Connection: close');

            echo $ini_file_contents;

            exit;

        }

    }



    public function deleteprinterAction()
    {

        if ($printer_id = $this->getRequest()->getParam('printer_id')) {

            $printer = new Xdelivery_Model_Printers();

            $printer->find($printer_id);

            // $log = new Migaprintv2_Model_Log();

            // foreach ($log->findAll(['printer_restaurant_id = ?' => $printer->getPrinterRestaurantId(), 'log_type = ?' => 1]) as $log_type) {

            //     $log_type->delete();

            // }

            $printer->delete();

            $data = [

                'success' => true,

                'message' =>p__('xdelivery', 'Printer has been deleted successfully.'),

                'message_loader' => 0,

                'message_button' => 0,

                'message_timeout' => 2

            ];

        } else {

            $data = [

                'error' => true,

                'message' =>p__('xdelivery', 'An error occurred while deleting the push. Please try again later.')

            ];

        }

        $this->_sendJson($data);

    }



    public function saveAction()
    {
        if ($datas = $this->getRequest()->getPost()) {
            try {
                if (!$datas['value_id']) {
                    throw new Exception(p__('xdelivery', 'An error occurred while saving. Please try again later.'));
                }
                $errors = '';
                if (empty($datas['printer_title'])) {
                    $errors .=p__('xdelivery', 'Printer name cannot be empty.') . "<br/>";
                }
                if (empty($datas['start_fetching_orders_from'])) {
                    $errors .=p__('xdelivery', 'Start fetching orders from cannot be empty.') . "<br/>";
                }
                if (empty($datas['printer_username'])) {
                    $errors .=p__('xdelivery', 'Unique key id cannot be empty.') . "<br/>";
                }

                if (empty($datas['printer_password'])) {
                    $errors .=p__('xdelivery', 'Password cannot be empty.') . "<br/>";
                }

                if (empty($datas['store_id'])) {
                    $errors .=p__('xdelivery', 'Please select a shop.') . "<br/>";
                }

                if (empty($datas['printer_type'])) {
                    $errors .=p__('xdelivery', 'Please select INI file.') . "<br/>";
                }

                if (empty($datas['currency_code']) || empty($datas['currency_symbol'])) {
                    $errors .=p__('xdelivery', 'Please select a currency.') . "<br/>";
                }



                $printer = new Xdelivery_Model_Printers();
                $filters = [
                    "printer_username = ?" => $datas['printer_username'],

                ];
                if (!empty($datas['printer_username']) && $printer->countAll($filters)) {
                    $errors .=p__('xdelivery', 'Printer key already exists.') . "<br/>";
                }
                if (!empty($errors)) {
                    throw new Exception($errors);
                }

                $printer->addData($datas)->save();
                $html = [
                    'success' => true,
                    'message' =>p__('xdelivery', 'Printer successfully saved.'),
                    'message_timeout' => 2,
                    'message_button' => 0,
                    'message_loader' => 0
                ];

            } catch (Exception $e) {
                $html = [
                    'error' => true,
                    'message' => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }
            $this->_sendJson($html);
        }
    }
}
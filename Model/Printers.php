<?php 

/**
 * Class Xdelivery_Model_ProductAttribute
 * @package Xdelivery\Model
 */
class Xdelivery_Model_Printers extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;

 
     /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_Printers::class;

     /**
     * @param array $params
     * @return Xdelivery_Model_Products[]
     */
    public function getPrinters($value_id)
    {
        return $this->getTable()->getPrinters($value_id);
    }
    public static function IniFileData()
    {
        return $ini="[1]
        Name=
        NameEn=ini version
        Command=v0006
        IsValid=1
        DataMode=1
        IsHide=0
        [2]
        Name=
        NameEn=server center number
        Command=
        IsValid=0
        DataMode=1
        IsHide=1
        [3]
        Name=
        NameEn=Auto Print
        Command=01
        IsValid=1
        DataMode=0
        IsHide=0
        [4]
        Name=Center Platform Number
        NameEn=Center Platform Number
        Command=*
        IsValid=1
        DataMode=1
        IsHide=0
        [5]
        Name=
        NameEn=RES ID
        Command=@@RES_ID@@
        IsValid=1
        DataMode=3
        IsHide=0
        [6]
        Name=Keyword for read time from email
        NameEn=Keyword for read time from email
        Command=0
        IsValid=|
        DataMode=1
        IsHide=0
        [7]
        Name=USSD Print
        NameEn=USSD Print
        Command=
        IsValid=1
        DataMode=0
        IsHide=1
        [8]
        Name=IP & Web Server
        NameEn=IP & Web Server
        Command=@@IP@@
        IsValid=;|
        DataMode=3
        IsHide=0
        [9]
        Name=Port
        NameEn=Port
        Command=@@PORT@@
        IsValid=1
        DataMode=1
        IsHide=1
        [10]
        Name=Order List Template
        NameEn=Order List Template
        Command=
        IsValid=1
        DataMode=1
        IsHide=1
        [11]
        Name=Tickets Style
        NameEn=Tickets Style
        Command=10
        IsValid=1
        DataMode=0
        IsHide=0
        [12]
        Name=User Password
        NameEn=User Password
        Command=
        IsValid=1
        DataMode=1
        IsHide=0
        [13]
        Name=Order format
        NameEn=Order format
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [14]
        Name=Email Print Type
        NameEn=Email Print Type(text:00,html:10)
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [15]
        Name=Reject Reason
        NameEn=Reject Reason
        Command=TOO BUSY;FOOD UNAVAILABLE;UNABLE TO DELIVER;DONT DELIVER TO AREA;UNKNOWN ADDRESS;TIME UNAVAILABLE;JAM - PLEASE REORDER
        IsValid=;
        DataMode=3
        IsHide=0
        [16]
        Name=Repeat Reply
        NameEn=Repeat Reply
        Command=02
        IsValid=1
        DataMode=0
        IsHide=0
        [17]
        Name=Disable receiving SMS numbers
        NameEn=Disable receiving SMS numbers
        Command=
        IsValid=1
        DataMode=1
        IsHide=0
        [18]
        Name=Auto Response
        NameEn=Auto Reply
        Command=01
        IsValid=1
        DataMode=0
        IsHide=0
        [19]
        Name=Http header(;key1: value1\r\nkey2: value2)
        NameEn=Http header(;key1: value1\r\nkey2: value2)
        Command=
        IsValid=1
        DataMode=3
        IsHide=0
        [20]
        Name=Beep Duration(s)
        NameEn=Beep Duration(s)
        Command=99
        IsValid=1
        DataMode=1
        IsHide=0
        [21]
        Name=reserved
        NameEn=reserved
        Command=
        IsValid=0
        DataMode=1
        IsHide=1
        [22]
        Name=
        NameEn=Connection Mode(00:Gprs/wifi/Sms, 01:Sms, 02:Gprs/Wifi, 03:email)
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [23]
        Name=Music Tips
        NameEn=Music Tips
        Command=41
        IsValid=1
        DataMode=0
        IsHide=0
        [24]
        Name=Comprehensive parameters
        NameEn=Comprehensive parameters
        Command=06;d02;R=Update;H00;B=1,4,188,50,230;r01
        IsValid=;
        DataMode=1
        IsHide=0
        [25]
        Name=
        NameEn=Print Date Time
        Command=30
        IsValid=1
        DataMode=0
        IsHide=0
        [26]
        Name=
        NameEn=Connection server interval(s)
        Command==@@AUTO_CHECK@@
        IsValid=1
        DataMode=1
        IsHide=0
        [27]
        Name=NewLineText
        NameEn=NewLineText
        Command=%%
        IsValid=1
        DataMode=1
        IsHide=0
        [28]
        Name=PrintDateFormat
        NameEn=PrintDateFormat
        Command=02
        IsValid=1
        DataMode=0
        IsHide=0
        [29]
        Name=
        NameEn=PrintTimeFormat
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [30]
        Name=reserved
        NameEn=reserved
        Command=
        IsValid=1
        DataMode=3
        IsHide=1
        [31]
        Name=Operation mode after confirming the order
        NameEn=Operation mode after confirming the order
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [32]
        Name=
        NameEn=Page header
        Command=Welcome/r-------------------------
        IsValid=;
        DataMode=3
        IsHide=0
        [33]
        Name=
        NameEn=Page footer
        Command=--------------------------\rThanks!
        IsValid=1
        DataMode=3
        IsHide=0
        [34]
        Name=Email Order Number Locator
        NameEn=Email&SMS Order Number
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [35]
        Name=
        NameEn=New Order Manage (00:list, 01:display,02:process)
        Command=02
        IsValid=1
        DataMode=0
        IsHide=0
        [36]
        Name=Order Server URL
        NameEn=Order Server URL
        Command=@@FILE_PATH@@
        IsValid=;
        DataMode=1
        IsHide=0
        [37]
        Name=Callback URL
        NameEn=Callback URL
        Command=@@CALLBACK_URL@@
        IsValid=;
        DataMode=1
        IsHide=0
        [38]
        Name=Email callback
        NameEn=Email callback
        Command=
        IsValid=1
        DataMode=1
        IsHide=0
        [39]
        Name=GPRS password
        NameEn=GPRS password
        Command=@@PASSWORD@@
        IsValid=1
        DataMode=1
        IsHide=1
        [40]
        Name=Manager Password
        NameEn=Manager Password
        Command=123456
        IsValid=1
        DataMode=1
        IsHide=0
        [41]
        Name=Second Confirm
        NameEn=Second Confirm
        Command=
        IsValid=1
        DataMode=0
        IsHide=1
        [42]
        Name=Print SMS Sender
        NameEn=Print Sender Number
        Command=01
        IsValid=1
        DataMode=0
        IsHide=0
        [43]
        Name=Incoming SMS number
        NameEn=Incoming SMS number
        Command=
        IsValid=1
        DataMode=3
        IsHide=0
        [44]
        Name=
        NameEn=Support Common SMS
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [45]
        Name=reserved
        NameEn=reserved
        Command=
        IsValid=1
        DataMode=1
        IsHide=1
        [46]
        Name=Number of copies to print
        NameEn=Number of printed copies of accepted orders
        Command=1
        IsValid=1
        DataMode=1
        IsHide=0
        [47]
        Name=reserved
        NameEn=reserved
        Command=
        IsValid=1
        DataMode=1
        IsHide=1
        [48]
        Name=Automatically print after reply
        NameEn=Automatically print after reply
        Command=01
        IsValid=1
        DataMode=0
        IsHide=0
        [49]
        Name=reserved
        NameEn=reserved
        Command=
        IsValid=1
        DataMode=0
        IsHide=1
        [50]
        Name=reserved
        NameEn=reserved
        Command=
        IsValid=1
        DataMode=1
        IsHide=1
        [51]
        Name=Built in printing parameters
        NameEn=Built in printing parameters
        Command=
        IsValid=1
        DataMode=1
        IsHide=1
        [52]
        Name=Select Fonts
        NameEn=Select Fonts(00-Gc Font I,20-Gc Font II,10-System Font)
        Command=20
        IsValid=1
        DataMode=0
        IsHide=0
        [53]
        Name=Accept Items
        NameEn=Accept Items
        Command=10 Minutes;15 Minutes;20 Minutes;25 Minutes;30 Minutes;35 Minutes;40 Minutes;Other
        IsValid=;
        DataMode=3
        IsHide=0
        [54]
        Name=callback send sms
        NameEn=callback send sms
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [55]
        Name=
        NameEn=Detect SMS sending status
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [56]
        Name=File Download Mode
        NameEn=File Download Mode (00:php/jsp,01:txt)
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [57]
        Name=
        NameEn=Send Email Port
        Command=
        IsValid=1
        DataMode=1
        IsHide=0
        [58]
        Name=Web Parameter
        NameEn=Login Web UserName
        Command=@@USER_NAME@@
        IsValid=1
        DataMode=1
        IsHide=0
        [59]
        Name=
        NameEn=Login Web Password
        Command=@@PASSWORD@@
        IsValid=1
        DataMode=1
        IsHide=0
        [60]
        Name=Incoming Mobile Number for SMS settings
        NameEn=Incoming Mobile Number for SMS settings
        Command=
        IsValid=1
        DataMode=1
        IsHide=0
        [61]
        Name=Lock or unlock device
        NameEn=Lock or unlock device
        Command=
        IsValid=1
        DataMode=1
        IsHide=0
        [62]
        Name=
        NameEn=lock status
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [63]
        Name=
        NameEn=Send Email Host
        Command=
        IsValid=1
        DataMode=1
        IsHide=0
        [64]
        Name=Timeout
        NameEn=Timeout
        Command=99
        IsValid=1
        DataMode=1
        IsHide=0
        [65]
        Name=
        NameEn=Restart Time(HH:mm)
        Command=0
        IsValid=1
        DataMode=1
        IsHide=0
        [66]
        Name=
        NameEn=How to accept or reject orders
        Command=10
        IsValid=1
        DataMode=0
        IsHide=0
        [67]
        Name=Online Time
        NameEn=Online Time
        Command=18:09;0:0;0:0;
        IsValid=;
        DataMode=1
        IsHide=0
        [68]
        Name=Offline Time
        NameEn=GPRS Close Time
        Command=18:09;0:0;0:0;
        IsValid=;
        DataMode=1
        IsHide=0
        [69]
        Name=Email Locator(Subtitle No)& Automatically adjust paper size
        NameEn=Email Locator(Subtitle No) & Automatically adjust paper size
        Command=
        IsValid=1
        DataMode=1
        IsHide=1
        [70]
        Name=reserved
        NameEn=reserved
        Command=
        IsValid=;
        DataMode=1
        IsHide=1
        [71]
        Name=reserved
        NameEn=reserved
        Command=
        IsValid=1
        DataMode=1
        IsHide=1
        [72]
        Name=Add print text after processing order
        NameEn=Add print text after processing order
        Command=\-\rAccepted for:\r\3;\-\rAccepted for:\r\3;\-\rOrder Cancel:\r\3
        IsValid=;
        DataMode=3
        IsHide=0
        [73]
        Name=Action settings
        NameEn=Action settings（title;action;Package name;Full class name）
        Command=0
        IsValid=1
        DataMode=1
        IsHide=0
        [74]
        Name=print Density
        NameEn=print Density
        Command=09
        IsValid=1
        DataMode=0
        IsHide=0
        [75]
        Name=Disable menu (mainId>subId,subId,...)
        NameEn=Disable menu (mainId>subId,subId,...)
        Command=
        IsValid=1
        DataMode=1
        IsHide=1
        [76]
        Name=print Speed
        NameEn=print Speed
        Command=09
        IsValid=1
        DataMode=0
        IsHide=0
        [77]
        Name=
        NameEn=Allow skipping printing when short of paper
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [78]
        Name=Order Content
        NameEn=Order Content
        Command=10
        IsValid=1
        DataMode=0
        IsHide=0
        [79]
        Name=Support Network Image
        NameEn=Support Network Image
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [80]
        Name=
        NameEn=Music
        Command=41
        IsValid=1
        DataMode=0
        IsHide=0
        [81]
        Name=Default Size
        NameEn=Default Size
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [82]
        Name=Use template when replying to SMS orders
        NameEn=Use template when replying to SMS orders
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [83]
        Name=Continuous monitoring order
        NameEn=Continuous monitoring order
        Command=02
        IsValid=1
        DataMode=0
        IsHide=0
        [84]
        Name=Server Type
        NameEn=Server Type
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [85]
        Name=Submit Reasons
        NameEn=Submit Reasons
        Command=test1;test2;Other
        IsValid=;
        DataMode=3
        IsHide=0
        [86]
        Name=
        NameEn=Number of printed copies of rejected orders
        Command=1
        IsValid=1
        DataMode=1
        IsHide=0
        [87]
        Name=
        NameEn=The printing interval between two copies
        Command=3
        IsValid=1
        DataMode=1
        IsHide=0
        [88]
        Name=
        NameEn=Whether to print the next copy after timeout
        Command=01
        IsValid=1
        DataMode=0
        IsHide=0
        [89]
        Name=SMS template for reply to accept
        NameEn=SMS template for reply to accept
        Command=
        IsValid=1
        DataMode=3
        IsHide=0
        [90]
        Name=SMS template for reply after rejection
        NameEn=SMS template for reply after rejection
        Command=
        IsValid=1
        DataMode=3
        IsHide=0
        [91]
        Name=SMS template for accept  under secondary confirmation
        NameEn=SMS template for accept  under secondary confirmation
        Command=
        IsValid=1
        DataMode=3
        IsHide=0
        [92]
        Name=SMS template rejected after second confirmation
        NameEn=SMS template rejected after second confirmation
        Command=
        IsValid=1
        DataMode=3
        IsHide=0
        [93]
        Name=Barcode Type
        NameEn=Barcode Type
        Command=01
        IsValid=1
        DataMode=0
        IsHide=0
        [94]
        Name=Button settings for secondary confirmation(repeat reply 2)
        NameEn=Button settings for secondary confirmation(repeat reply 2)
        Command=Update
        IsValid=1
        DataMode=1
        IsHide=0
        [95]
        Name=Order related functions
        NameEn=Order related functions
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [96]
        Name=Status prompt
        NameEn=Status prompt
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [97]
        Name=
        NameEn=Keep Screen On
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [98]
        Name=
        NameEn=Fixed Screen
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [99]
        Name=Disable order separator
        NameEn=Disable order separator
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [100]
        Name=Align mode
        NameEn=Align mode
        Command=
        IsValid=1
        DataMode=0
        IsHide=1
        [101]
        Name=Server Format Setting
        NameEn=Server Date Format
        Command=01
        IsValid=1
        DataMode=0
        IsHide=0
        [102]
        Name=
        NameEn=Server date separator
        Command=
        IsValid=1
        DataMode=1
        IsHide=0
        [103]
        Name=The font size of the app display interface
        NameEn=The font size of the app display interface
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [104]
        Name=
        NameEn=Overlay other apps
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [105]
        Name=
        NameEn=Get Log(01:open,00:close)
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [106]
        Name=
        NameEn=Accept Content
        Command=10 Minutes
        IsValid=1
        DataMode=3
        IsHide=0
        [107]
        Name=
        NameEn=Reject Reason
        Command=TOO BUSY
        IsValid=1
        DataMode=3
        IsHide=0
        [108]
        Name=Device type
        NameEn=Printer Type
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [109]
        Name=Email For Receive Order
        NameEn=Email Protocol (00:POP3,01:IMAP)
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [110]
        Name=
        NameEn=Email Receive Host
        Command=0
        IsValid=1
        DataMode=3
        IsHide=0
        [111]
        Name=Email Receive Host & Port
        NameEn=Email Receive Port
        Command=993
        IsValid=1
        DataMode=1
        IsHide=0
        [112]
        Name=Email Acount
        NameEn=Email account for receive order\n(Format: xxx@yyy.zz)
        Command=0
        IsValid=1
        DataMode=1
        IsHide=0
        [113]
        Name=
        NameEn=Email password
        Command=0
        IsValid=1
        DataMode=1
        IsHide=0
        [114]
        Name=
        NameEn=Continuous monitoring order
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [115]
        Name=Email Locator(Subtitle No)
        NameEn=Email Locator(Subtitle No)
        Command=0
        IsValid=1
        DataMode=1
        IsHide=0
        [116]
        Name=Adjust HTML
        NameEn=Adjust paper size
        Command=
        IsValid=1
        DataMode=1
        IsHide=1
        [117]
        Name=Add SMS Order Number
        NameEn=Add SMS Order Number
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [118]
        Name=Statistical Report Menu
        NameEn=Statistical Report Menu
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [119]
        Name=Reply text for callback
        NameEn=Reply text for callback
        Command=\-\rAccepted for:\r\3;\-\rOrder confirm:\r\3;\-\rOrder Cancel:\r\3
        IsValid=;
        DataMode=3
        IsHide=0
        [120]
        Name=HTML refresh
        NameEn=HTML refresh
        Command=01
        IsValid=1
        DataMode=0
        IsHide=0
        [121]
        Name=Language
        NameEn=Language
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [122]
        Name=XML layout template for UI
        NameEn=XML layout template for UI
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [123]
        Name=Countdown waiting for confirmation
        NameEn=Countdown waiting for confirmation
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [124]
        Name=Paper size for printer
        NameEn=Paper size for printer
        Command=00
        IsValid=1
        DataMode=0
        IsHide=0
        [125]
        Name=Reject for offline
        NameEn=Reject for offline
        Command=
        IsValid=1
        DataMode=0
        IsHide=0
        [126]
        Name=
        NameEn=Reason for reject
        Command=
        IsValid=1
        DataMode=1
        IsHide=0
        [127]
        Name=Format URL parameters
        NameEn=Format URL parameters
        Command=
        IsValid=1
        DataMode=0
        IsHide=0
        [128]
        Name=Html Print Optimization
        NameEn=Html Print Optimization
        Command=
        IsValid=1
        DataMode=0
        IsHide=0
        [129]
        Name=HTML page adjustment
        NameEn=Adjust paper size
        Command=
        IsValid=1
        DataMode=0
        IsHide=0
        [130]
        Name=
        NameEn=Enable Page Optimization
        Command=
        IsValid=1
        DataMode=0
        IsHide=0
        [131]
        Name=
        NameEn=Width Scale(The maximum is 100, which is 100%)
        Command=
        IsValid=1
        DataMode=0
        IsHide=0
        [132]
        Name=
        NameEn=Width Scale(The maximum is 100, which is 100%)
        Command=
        IsValid=1
        DataMode=0
        IsHide=0
        [133]
        Name=
        NameEn=Maximum page width(in pixels,Set 0 to use default)
        Command=
        IsValid=1
        DataMode=0
        IsHide=0
        [134]
        Name=Html Image processing
        NameEn=Html Image processing
        Command=
        IsValid=1
        DataMode=0
        IsHide=0
        [135]
        Name=
        NameEn=Enlarge Mode
        Command=
        IsValid=1
        DataMode=0
        IsHide=0
        [136]
        Name=
        NameEn=Disable wrap line when enlarge image
        Command=
        IsValid=1
        DataMode=0
        IsHide=0
        [137]
        Name=
        NameEn=Remove color background
        Command=
        IsValid=1
        DataMode=0
        IsHide=0
        [138]
        Name=
        NameEn=Disable border removal
        Command=
        IsValid=1
        DataMode=0
        IsHide=0
        [139]
        Name=
        NameEn=Enlarge Image Level
        Command=
        IsValid=1
        DataMode=1
        IsHide=0
        ";            
    }

}
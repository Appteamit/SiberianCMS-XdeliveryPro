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
    public static function IniFileData($type)
    {
        $ini_array = array();
        $ini_array['GT5000W'] = "[1]
Name=ini version
NameEn=ini version
Command=v0006
IsValid=1
DataMode=1
IsHide=0
[2]
Name=远程升级中心号码
NameEn=server center number
Command=0
IsValid=0
DataMode=1
IsHide=1
[3]
Name=自动打印
NameEn=Auto Print
Command=01
IsValid=1
DataMode=0
IsHide=0
[4]
Name=短信平台地址号码
NameEn=Center Platform Number
Command=*
IsValid=1
DataMode=1
IsHide=1
[5]
Name=账号
NameEn=RES ID
Command=@@RES_ID@@
IsValid=1
DataMode=3
IsHide=0
[6]
Name=语音平台地址号码
NameEn=
Command=0
IsValid=0
DataMode=1
IsHide=1
[7]
Name=区域（协议）选择
NameEn=Area Setting
Command=04
IsValid=1
DataMode=0
IsHide=1
[8]
Name=IP地址
NameEn=IP
Command=@@IP@@
IsValid=1
DataMode=1
IsHide=0
[9]
Name=IP端口
NameEn=Port
Command=@@PORT@@
IsValid=1
DataMode=1
IsHide=0
[10]
Name=IP APN
NameEn=IP APN
Command=cmnet
IsValid=1
DataMode=1
IsHide=0
[11]
Name=IP地址与端口
NameEn=ip
Command=00
IsValid=0
DataMode=0
IsHide=1
[12]
Name=确认密码
NameEn=pwd
Command=0
IsValid=0
DataMode=1
IsHide=1
[13]
Name=小版本号
NameEn=Version Setting
Command=09
IsValid=1
DataMode=0
IsHide=1
[14]
Name=按协议处理
NameEn=SMS Demand
Command=01
IsValid=1
DataMode=0
IsHide=1
[15]
Name=拒绝理由列表
NameEn=Reject Reason
Command=TOO BUSY;FOOD UNAVAILABLE;UNABLE TO DELIVER;DONT DELIVER TO AREA;UNKNOWN ADDRESS;TIME UNAVAILABLE;JAM - PLEASE REORDER;
IsValid=1
DataMode=3
IsHide=0
[16]
Name=重复回复
NameEn=Repeat Reply
Command=00
IsValid=1
DataMode=0
IsHide=0
[17]
Name=自定义USSD命令
NameEn=
Command=0
IsValid=0
DataMode=1
IsHide=1
[18]
Name=自动回复
NameEn=Auto Reply
Command=00
IsValid=1
DataMode=0
IsHide=0
[19]
Name=自动回复内容
NameEn=Auto Reply Content
Command=0
IsValid=1
DataMode=3
IsHide=0
[20]
Name=提示音时间
NameEn=Beep Duration
Command=99
IsValid=1
DataMode=1
IsHide=0
[21]
Name=消息发布中心号
NameEn=Message release center number
Command=0
IsValid=0
DataMode=1
IsHide=1
[22]
Name=联接模式(00:Gprs, 01:Sms)
NameEn=Connection Mode(00:Gprs, 01:Sms)
Command=00
IsValid=1
DataMode=0
IsHide=1
[23]
Name=提示音开关
NameEn=Music Tips
Command=01
IsValid=1
DataMode=0
IsHide=0
[24]
Name=短信打印间隔时间
NameEn=Print Blank
Command=6
IsValid=1
DataMode=1
IsHide=0
[25]
Name=打印日期时间与号码开关
NameEn=Print Date Time
Command=01
IsValid=1
DataMode=0
IsHide=0
[26]
Name=GPRS自动查询
NameEn=GPRS auto check interval
Command=@@AUTO_CHECK@@
IsValid=1
DataMode=1
IsHide=0
[27]
Name=换行字符串
NameEn=NewLineText
Command=%%
IsValid=1
DataMode=1
IsHide=0
[28]
Name=日期格式
NameEn=PrintDateFormat
Command=00
IsValid=1
DataMode=0
IsHide=0
[29]
Name=时间格式
NameEn=PrintTimeFormat
Command=00
IsValid=1
DataMode=0
IsHide=0
[30]
Name=待机显示字符
NameEn=IdleText
Command=Net Printer
IsValid=1
DataMode=3
IsHide=0
[31]
Name=受理订单回复类型 (00:item, 01:time)
NameEn=Accept reply type (00:item, 01:time)
Command=01
IsValid=1
DataMode=0
IsHide=0
[32]
Name=打印页眉内容
NameEn=Page header
Command=Welcome/r-------------------------
IsValid=1
DataMode=3
IsHide=0
[33]
Name=打印页脚内容
NameEn=Page footer
Command=--------------------------
IsValid=1
DataMode=3
IsHide=0
[34]
Name=开机是否响音乐
NameEn=Music on startup
Command=00
IsValid=1
DataMode=0
IsHide=1
[35]
Name=新订单人工处理方式显示类型
NameEn=New Order Manage
Command=02
IsValid=1
DataMode=0
IsHide=0
[36]
Name=GPRS文件地址
NameEn=File Path
Command=@@FILE_PATH@@
IsValid=1
DataMode=1
IsHide=0
[37]
Name=GPRS订单回复地址
NameEn=Callback URL
Command=@@CALLBACK_URL@@
IsValid=1
DataMode=1
IsHide=0
[38]
Name=GPRS USER NAME
NameEn=GPRS User Name
Command=@@USER_NAME@@
IsValid=1
DataMode=1
IsHide=0
[39]
Name=GPRS PSW
NameEn=GPRS Password
Command=@@PASSWORD@@
IsValid=1
DataMode=1
IsHide=0
[40]
Name=管理员菜单显示密码
NameEn=Manager Password
Command=*123456#
IsValid=1
DataMode=1
IsHide=1
[41]
Name=订单处理回复选取内容二次确认
NameEn=Second Confirm
Command=00
IsValid=1
DataMode=0
IsHide=1
[42]
Name=打印来信号码
NameEn=Print Sender Number
Command=01
IsValid=1
DataMode=0
IsHide=1
[43]
Name=订单短信平台地址号码
NameEn=Incoming SMS number
Command=*
IsValid=1
DataMode=1
IsHide=1
[44]
Name=非协议短信是否处理
NameEn=Support Common SMS
Command=01
IsValid=1
DataMode=0
IsHide=1
[45]
Name=删除国家代码位数
NameEn=Del Reply Number Bit
Command=0
IsValid=0
DataMode=1
IsHide=1
[46]
Name=打印联数
NameEn=Print Count
Command=0
IsValid=0
DataMode=1
IsHide=0
[47]
Name=添加号码
NameEn=Add Number
Command=0
IsValid=0
DataMode=1
IsHide=1
[48]
Name=回复是否自动打印
NameEn=Reply Auto Print
Command=01
IsValid=1
DataMode=0
IsHide=0
[49]
Name=通话设置
NameEn=Call Set
Command=00
IsValid=1
DataMode=0
IsHide=1
[50]
Name=过滤垃圾信息
NameEn=Disuse Msg option
Command=0
IsValid=1
DataMode=1
IsHide=1
[51]
Name=回复公司名称
NameEn=Reply Company Name
Command=0
IsValid=1
DataMode=1
IsHide=0
[52]
Name=英文大字体打印
NameEn=Print Max Fonts
Command=00
IsValid=1
DataMode=0
IsHide=1
[53]
Name=接受理由列表
NameEn=Accept Items
Command=10 Minutes;15 Minutes;20 Minutes;25 Minutes;30 Minutes;35 Minutes;40 Minutes;
IsValid=1
DataMode=3
IsHide=0
[54]
Name=直接回复
NameEn=Answer Direct
Command=00
IsValid=1
DataMode=0
IsHide=0
[55]
Name=设置检测新订单方式
NameEn=Enable Check Order
Command=00
IsValid=1
DataMode=0
IsHide=0
[56]
Name=文件下载模式
NameEn=File Download Mode (00:php,01:txt)
Command=00
IsValid=1
DataMode=0
IsHide=0
[57]
Name=检测新订单端口
NameEn=Check Order Port
Command=7070
IsValid=1
DataMode=1
IsHide=0
[58]
Name=用户名称
NameEn=Login Web UserName
Command=@@USER_NAME@@
IsValid=1
DataMode=1
IsHide=0
[59]
Name=用户密码
NameEn=Login Web Password
Command=@@PASSWORD@@
IsValid=1
DataMode=1
IsHide=0
[60]
Name=设置参数的手机号码,这个参数不需要在菜单内设置
NameEn=Incoming Mobile Number for SMS settings
Command=*
IsValid=1
DataMode=1
IsHide=1
[61]
Name=远程锁机密码
NameEn=Remote Lock Terminal PSW
Command=123456
IsValid=1
DataMode=1
IsHide=1
[62]
Name=远程锁机标志
NameEn=Remote Lock Terminal Flag
Command=80
IsValid=1
DataMode=0
IsHide=0
[63]
Name=检测是否有新订单的IP地址
NameEn=Check Order IP
Command=
IsValid=1
DataMode=1
IsHide=0
[64]
Name=订单超时时间
NameEn=timeout
Command=99
IsValid=1
DataMode=1
IsHide=0
[65]
Name=重启时间
NameEn=ReBootTime
Command=05:20
IsValid=1
DataMode=1
IsHide=0
[66]
Name=接受所有订单
NameEn=Accept All Repast
Command=00
IsValid=1
DataMode=0
IsHide=0
[67]
Name=GPRS开启时间
NameEn=GPRS Open Time
Command=18:09
IsValid=1
DataMode=1
IsHide=1
[68]
Name=GPRS关闭时间
NameEn=GPRS Close Time
Command=18:09
IsValid=1
DataMode=1
IsHide=1
[69]
Name=reserved
NameEn=reserved
Command=***
IsValid=1
DataMode=1
IsHide=1
[70]
Name=DNS服务器
NameEn=DNS HOSTS
Command=8.8.8.8:53;8.8.4.4:53;
IsValid=1
DataMode=1
IsHide=1
[71]
Name=短信回复时替换国际代码
NameEn=Replace international code when SMS reply
Command=0
IsValid=1
DataMode=1
IsHide=1
[72]
Name=打印文本:接受;确认;取消
NameEn=print text:Accepted;Confirmed;Cancel
Command=0
IsValid=1
DataMode=3
IsHide=0
[73]
Name=Accept-Encoding:
NameEn=Accept-Encoding:
Command=deflate
IsValid=1
DataMode=1
IsHide=0
[74]
Name=print Density
NameEn=print Density
Command=10
IsValid=1
DataMode=1
IsHide=0
[75]
Name=GPRS or 3G dial number
NameEn=GPRS or 3G dial number
Command=*99***#
IsValid=1
DataMode=1
IsHide=0";


        $ini_array['GT5000SW'] = "[1]
Name=ini version
NameEn=ini version
Command=v0006
IsValid=1
DataMode=1
IsHide=0
[2]
Name=远程升级中心号码
NameEn=server center number
Command=0
IsValid=0
DataMode=1
IsHide=0
[3]
Name=自动打印
NameEn=Auto Print
Command=01
IsValid=1
DataMode=0
IsHide=0
[4]
Name=短信平台地址号码
NameEn=Center Platform Number
Command=*
IsValid=1
DataMode=1
IsHide=0
[5]
Name=账号
NameEn=RES ID
Command=@@RES_ID@@
IsValid=1
DataMode=3
IsHide=0
[6]
Name=reserved
NameEn=reserved
Command=0
IsValid=1
DataMode=1
IsHide=0
[7]
Name=USSD打印设置
NameEn=USSD Print
Command=04
IsValid=1
DataMode=0
IsHide=0
[8]
Name=IP地址
NameEn=IP
Command=@@IP@@
IsValid=1
DataMode=3
IsHide=0
[9]
Name=IP端口
NameEn=Port
Command=@@PORT@@
IsValid=1
DataMode=1
IsHide=0
[10]
Name=IP APN
NameEn=IP APN
Command=cmnet
IsValid=1
DataMode=1
IsHide=0
[11]
Name=检测更多订单
NameEn=Check More Orders
Command=00
IsValid=1
DataMode=0
IsHide=0
[12]
Name=确认密码
NameEn=pwd
Command=0
IsValid=1
DataMode=1
IsHide=0
[13]
Name=小版本号
NameEn=Version Setting
Command=00
IsValid=1
DataMode=0
IsHide=0
[14]
Name=按协议处理
NameEn=SMS Demand
Command=01
IsValid=1
DataMode=0
IsHide=0
[15]
Name=拒绝理由列表
NameEn=Reject Reason
Command=TOO BUSY;FOOD UNAVAILABLE;UNABLE TO DELIVER;DONT DELIVER TO AREA;UNKNOWN ADDRESS;TIME UNAVAILABLE;JAM - PLEASE REORDER;
IsValid=1
DataMode=3
IsHide=0
[16]
Name=重复回复
NameEn=Repeat Reply
Command=00
IsValid=1
DataMode=0
IsHide=0
[17]
Name=Disable the SMS number
NameEn=Disable the SMS number
Command=0
IsValid=1
DataMode=1
IsHide=0
[18]
Name=自动回复
NameEn=Auto Reply
Command=00
IsValid=1
DataMode=0
IsHide=0
[19]
Name=自动回复内容
NameEn=Auto Reply Content
Command=0
IsValid=1
DataMode=3
IsHide=0
[20]
Name=提示音时间
NameEn=Beep Duration
Command=99
IsValid=1
DataMode=1
IsHide=0
[21]
Name=消息发布中心号
NameEn=Message release center number
Command=0
IsValid=0
DataMode=1
IsHide=0
[22]
Name=联接模式(00:Gprs, 01:Sms)
NameEn=Connection Mode(00:Gprs, 01:Sms)
Command=00
IsValid=1
DataMode=0
IsHide=0
[23]
Name=提示音开关
NameEn=Music Tips
Command=41
IsValid=1
DataMode=0
IsHide=0
[24]
Name=短信打印间隔时间
NameEn=Print Blank
Command=06;V05;c10;
IsValid=1
DataMode=1
IsHide=0
[25]
Name=打印日期时间与号码开关
NameEn=Print Date Time
Command=01
IsValid=1
DataMode=0
IsHide=0
[26]
Name=GPRS自动查询
NameEn=GPRS auto check interval
Command=@@AUTO_CHECK@@
IsValid=1
DataMode=1
IsHide=0
[27]
Name=换行字符串
NameEn=NewLineText
Command=%%
IsValid=1
DataMode=1
IsHide=0
[28]
Name=日期格式
NameEn=PrintDateFormat
Command=00
IsValid=1
DataMode=0
IsHide=0
[29]
Name=时间格式
NameEn=PrintTimeFormat
Command=00
IsValid=1
DataMode=0
IsHide=0
[30]
Name=待机显示字符
NameEn=IdleText
Command=GSM Printer
IsValid=1
DataMode=3
IsHide=0
[31]
Name=受理订单回复类型 (00:item, 01:time)
NameEn=Accept reply type (00:item, 01:time)
Command=01
IsValid=1
DataMode=0
IsHide=0
[32]
Name=打印页眉内容
NameEn=Page header
Command=Welcome/r-------------------------
IsValid=1
DataMode=3
IsHide=0
[33]
Name=打印页脚内容
NameEn=Page footer
Command=--------------------------\rThanks!
IsValid=1
DataMode=3
IsHide=0
[34]
Name=设备类型
NameEn=Device Type(01:wifi)
Command=01
IsValid=1
DataMode=0
IsHide=0
[35]
Name=新订单人工处理方式显示类型
NameEn=New Order Manage
Command=02
IsValid=1
DataMode=0
IsHide=0
[36]
Name=GPRS文件地址
NameEn=File Path
Command=@@FILE_PATH@@
IsValid=1
DataMode=1
IsHide=0
[37]
Name=GPRS订单回复地址
NameEn=Callback URL
Command=@@CALLBACK_URL@@
IsValid=1
DataMode=1
IsHide=0
[38]
Name=GPRS USER NAME
NameEn=GPRS User Name
Command=@@USER_NAME@@
IsValid=1
DataMode=1
IsHide=0
[39]
Name=GPRS PSW
NameEn=GPRS Password
Command=@@PASSWORD@@
IsValid=1
DataMode=1
IsHide=0
[40]
Name=管理员菜单显示密码
NameEn=Manager Password
Command=*123456#
IsValid=1
DataMode=1
IsHide=1
[41]
Name=订单处理回复选取内容二次确认
NameEn=Second Confirm
Command=00
IsValid=1
DataMode=0
IsHide=0
[42]
Name=打印来信号码
NameEn=Print Sender Number
Command=01
IsValid=1
DataMode=0
IsHide=0
[43]
Name=订单短信平台地址号码
NameEn=Incoming SMS number
Command=0
IsValid=1
DataMode=1
IsHide=0
[44]
Name=非协议短信是否处理
NameEn=Support Common SMS
Command=01
IsValid=1
DataMode=0
IsHide=0
[45]
Name=删除国家代码位数
NameEn=Del Reply Number Bit
Command=0
IsValid=0
DataMode=1
IsHide=0
[46]
Name=打印联数
NameEn=Print Count
Command=0
IsValid=1
DataMode=1
IsHide=0
[47]
Name=添加号码
NameEn=Add header Number for send sms
Command=0
IsValid=1
DataMode=1
IsHide=0
[48]
Name=回复是否自动打印
NameEn=Reply Auto Print
Command=01
IsValid=1
DataMode=0
IsHide=0
[49]
Name=通话设置
NameEn=Call Set
Command=00
IsValid=1
DataMode=0
IsHide=0
[50]
Name=过滤垃圾信息
NameEn=Disuse Msg option
Command=0
IsValid=1
DataMode=1
IsHide=0
[51]
Name=回复公司名称
NameEn=Reply Company Name
Command=0
IsValid=1
DataMode=1
IsHide=0
[52]
Name=英文大字体打印
NameEn=Print Max Fonts
Command=00
IsValid=1
DataMode=0
IsHide=0
[53]
Name=接受理由列表
NameEn=Accept Items
Command=10 Minutes;15 Minutes;20 Minutes;25 Minutes;30 Minutes;35 Minutes;40 Minutes;
IsValid=1
DataMode=3
IsHide=0
[54]
Name=直接回复
NameEn=Answer Direct
Command=00
IsValid=1
DataMode=0
IsHide=0
[55]
Name=设置检测新订单方式
NameEn=Enable Check Order
Command=00
IsValid=1
DataMode=0
IsHide=0
[56]
Name=文件下载模式
NameEn=File Download Mode (00:php,01:txt)
Command=00
IsValid=1
DataMode=0
IsHide=0
[57]
Name=检测新订单端口
NameEn=Check Order Port
Command=7070
IsValid=1
DataMode=1
IsHide=0
[58]
Name=用户名称
NameEn=Login Web UserName
Command=@@USER_NAME@@
IsValid=1
DataMode=1
IsHide=0
[59]
Name=用户密码
NameEn=Login Web Password
Command=@@PASSWORD@@
IsValid=1
DataMode=1
IsHide=0
[60]
Name=设置参数的手机号码,这个参数不需要在菜单内设置
NameEn=Incoming Mobile Number for SMS settings
Command=*
IsValid=1
DataMode=1
IsHide=0
[61]
Name=远程锁机密码
NameEn=Remote Lock Terminal PSW
Command=123456
IsValid=1
DataMode=1
IsHide=0
[62]
Name=远程锁机标志
NameEn=Remote Lock Terminal Flag
Command=00
IsValid=1
DataMode=0
IsHide=0
[63]
Name=检测是否有新订单的IP地址
NameEn=Check Order IP
Command=
IsValid=1
DataMode=1
IsHide=0
[64]
Name=订单超时时间
NameEn=timeout
Command=99
IsValid=1
DataMode=1
IsHide=0
[65]
Name=重启时间
NameEn=ReBootTime
Command=0
IsValid=1
DataMode=1
IsHide=0
[66]
Name=接受所有订单
NameEn=Accept All Repast
Command=00
IsValid=1
DataMode=0
IsHide=0
[67]
Name=GPRS开启时间
NameEn=GPRS Open Time
Command=18:09
IsValid=1
DataMode=1
IsHide=0
[68]
Name=GPRS关闭时间
NameEn=GPRS Close Time
Command=18:09
IsValid=1
DataMode=1
IsHide=0
[69]
Name=reserved
NameEn=reserved
Command=***
IsValid=1
DataMode=1
IsHide=0
[70]
Name=DNS服务器
NameEn=DNS HOSTS
Command=8.8.8.8:53;8.8.4.4:53;192.168.1.1:53;
IsValid=1
DataMode=1
IsHide=0
[71]
Name=短信回复时替换国际代码
NameEn=Replace international code when SMS reply
Command=0
IsValid=1
DataMode=1
IsHide=0
[72]
Name=打印文本:接受;确认;取消
NameEn=print text:Accepted;Confirmed;Cancel
Command=0
IsValid=1
DataMode=3
IsHide=0
[73]
Name=Accept-Encoding:
NameEn=Accept-Encoding:
Command=1
IsValid=1
DataMode=1
IsHide=0
[74]
Name=print Density
NameEn=print Density
Command=12
IsValid=1
DataMode=1
IsHide=0
[75]
Name=WIFI-SSID
NameEn=WIFI-SSID
Command=
IsValid=1
DataMode=1
IsHide=0
[76]
Name=WIFI-Password
NameEn=WIFI-Password
Command=
IsValid=1
DataMode=1
IsHide=0";

$ini_array['testing'] = "[1]
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
Command=00
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
Command=@@IP@@;
IsValid=;|
DataMode=3
IsHide=0
CopyValStr=Host address for receiving emails;Reserve|Reserve|Reserve|Reserve;Reserve|Reserve|Reserve|Reserve
CopyTips=Host address for receiving emails
[9]
Name=Port
NameEn=Port
Command=@@PORT@@
IsValid=1
DataMode=1
IsHide=0
[10]
Name=Order List Template
NameEn=Order List Template
Command=
IsValid=|&
DataMode=1
IsHide=1
[11]
Name=
NameEn=Check More Orders
Command=13
IsValid=1
DataMode=0
IsHide=0
[12]
Name=User Password
NameEn=User Password
Command=0
IsValid=1
DataMode=1
IsHide=0
[13]
Name=Type
NameEn=Order format
Command=00
IsValid=1
DataMode=0
IsHide=0
[14]
Name=Email Print Type
NameEn=Email Print Type(text:00,html:10)
Command=10
IsValid=1
DataMode=0
IsHide=0
[15]
Name=Reject Reason
NameEn=Reject Reason
Command=TROPPO OCCUPATO;IMPOSSIBILE ORDINARE;
IsValid=;
DataMode=3
IsHide=0
[16]
Name=
NameEn=Repeat Reply
Command=00
IsValid=1
DataMode=0
IsHide=0
[17]
Name=Disable receiving SMS numbers
NameEn=Disable receiving SMS numbers
Command=0
IsValid=1
DataMode=1
IsHide=0
[18]
Name=
NameEn=Auto Reply
Command=00
IsValid=1
DataMode=0
IsHide=0
[19]
Name=Http header(;key1: value1\r\nkey2: value2)
NameEn=Http header(;key1: value1\r\nkey2: value2)
Command=0
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
Command=06;V05;s35;s=1;H30;d02;
IsValid=;
DataMode=1
IsHide=0
[25]
Name=
NameEn=Print Date Time
Command=31
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
Name=
NameEn=Accept reply type (00:item, 01:time)
Command=01
IsValid=1
DataMode=0
IsHide=0
[32]
Name=Page header
NameEn=Page header
Command=Nuovo Ordine /r-------------------------
IsValid=|;
DataMode=3
IsHide=0
[33]
Name=Page footer
NameEn=Page footer
Command=--------------------------\rAssistenza Tecnica\r www.xdelivery.it\r33376600014!
IsValid=|;
DataMode=3
IsHide=0
[34]
Name=Email&SMS Order Number
NameEn=Email&SMS Order Number
Command=01
IsValid=1
DataMode=0
IsHide=0
[35]
Name=
NameEn=New Order Manage (00:list, 01:display,02:process)
Command=01
IsValid=1
DataMode=0
IsHide=0
[36]
Name=Order Server URL
NameEn=Order Server URL
Command=;;@@FILE_PATH@@;
IsValid=;
DataMode=1
IsHide=0
[37]
Name=Callback URL
NameEn=Callback URL
Command=;;@@CALLBACK_URL@@;
IsValid=;
DataMode=1
IsHide=0
[38]
Name=Email Reply
NameEn=Email Reply
Command=@@USER_NAME@@
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
Command=*123456#
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
Command=0
IsValid=1
DataMode=3
IsHide=0
[44]
Name=
NameEn=Support Common SMS
Command=01
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
Name=Print Count
NameEn=Number of copies to print
Command=0
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
Command=02
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
NameEn=Select Fonts(00-GcFont,10-System Font)
Command=20
IsValid=1
DataMode=0
IsHide=0
[53]
Name=Accept Items
NameEn=Accept Items
Command=10 Minutes;15 Minutes;20 Minutes;25 Minutes;30 Minutes;35 Minutes;40 Minutes;
IsValid=|;
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
Name=Continuous monitoring order
NameEn=Detect SMS sending status
Command=00
IsValid=1
DataMode=0
IsHide=0
[56]
Name=Email For Receive Order
NameEn=File Download Mode (00:php,01:txt)
Command=00
IsValid=1
DataMode=0
IsHide=0
[57]
Name=
NameEn=Send Email Port
Command=7070
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
Command=0
IsValid=1
DataMode=1
IsHide=0
[61]
Name=password for lock
NameEn=password for lock or unlock
Command=0
IsValid=1
DataMode=1
IsHide=0
[62]
Name=lock status
NameEn=lock status
Command=
IsValid=1
DataMode=0
IsHide=1
[63]
Name=
NameEn=Send Email Host
Command=27.154.162.15
IsValid=1
DataMode=1
IsHide=0
[64]
Name=Timeout
NameEn=Timeout
Command=9999
IsValid=1
DataMode=1
IsHide=0
[65]
Name=
NameEn=Restart Time(hh:mm)
Command=0
IsValid=1
DataMode=1
IsHide=0
[66]
Name=Accept All Order
NameEn=Accept All Repast
Command=00
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
Command=***;html:0|1|100|100;
IsValid=1
DataMode=1
IsHide=0
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
Name=print text & reply text
NameEn=print text:Accepted;Confirmed;Cancel
Command=Accettato per;Ordine Confermato;Ordine Cancellato;
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
Name=print Parameter
NameEn=print Density
Command=9;9;
IsValid=1
DataMode=1
IsHide=0
[75]
Name=Disable menu (mainId>subId,subId,...)
NameEn=Disable menu (mainId>subId,subId,...)
Command=0
IsValid=1
DataMode=1
IsHide=0
[76]
Name=reserved
NameEn=reserved
Command=
IsValid=0
DataMode=1
IsHide=1";

        return $ini_array[$type];
    }

}
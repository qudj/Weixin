<?php
define("TXTTPL","<xml>
		             <ToUserName><![CDATA[%s]]></ToUserName>
		             <FromUserName><![CDATA[%s]]></FromUserName>
		             <CreateTime>%s</CreateTime>
		             <MsgType><![CDATA[%s]]></MsgType>
		             <Content><![CDATA[%s]]></Content>
		             <MsgId>0</MsgId>
	       		 </xml>");
class IndexAction extends Action
{ 
    public function verify()
    {
        $tmpstr = $_GET['echostr'];

        if(!empty($tmpstr)){
            $signature  =   $_GET['signature'];
            $timestamp  =   $_GET['timestamp'];
            $nonce      =   $_GET['nonce'];
            $token      =   'weixin';
            $array      =   array($timestamp,$nonce,$token);
            sort($array);
            $tmpstr     =   implode('',$array);
            $tmpstr     =   sha1($tmpstr);  
            if($tmpstr==$signature)
            {
                echo $_GET['echostr'];
                exit;
            }            
        }else{
            return true;
        }
    }

    public function responseMessage()
    { 
        $this->verify();
        $postStr = file_get_contents('php://input');
        
        if(!empty($postStr)){
            $redis = RedisCache::getInstance();
            $postObj    =   simplexml_load_string($postStr,'SimpleXMLElement',LIBXML_NOCDATA);
            $msgtype    =   trim($postObj->MsgType);
            $content     =   trim($postObj->Content);
            $fromUsername   =   $postObj->FromUserName;
            $toUsername =   $postObj->ToUserName;
            $time       =   time();
            $skey = $fromUsername."_select";
            switch ($msgtype) {
                case 'text':
                    if($content==='0'){
                        $redis->delete($skey);
                        $select=0;
                    }else{
                        $select = $redis->get($skey);
                        if(empty($select)){
                            switch($content) {
                                case '1': $select=1;break;
                                case '2': $select=2;break; 
                                case '3': $select=3;break; 
                                case '4': $select=4;break; 
                                default: break;
                            }
                        }
                    }
                    break;

                case 'voice':
                    $select=4;
                    $redis->set($skey, $select, 600);
                    break;

                case 'image':
                    break;

                case 'location':
                    break;

                case 'event':
                    $eventkey = trim($postObj->EventKey);
                    $redis->delete($skey);
                    switch($eventkey) {
                        case 'V1001_RETURN':  $select=0;break;
                        case 'rselfmenu_0_1': $select=1;break;
                        case 'rselfmenu_0_2': $select=2;break; 
                        case 'rselfmenu_0_3': $select=3;
                                              $redis->set($skey, $select, 600);
                                              break; 
                        case 'rselfmenu_0_4': $select=4;break; 
                        default: $select=0;break;
                    }
                    break;
                                                    
                default:
                    break;
            }
            
            $select = isset($select)?$select:$select = $redis->get($skey);
            if(!empty($select)){
                FactoryService::create($select);
            }else{
                $showinfo = "你好，请在菜单中或回复以下数字选择服务类型：\n\n".
                            "【1】 qu在线翻译\n\n".
                            "【2】 qu实时天气\n\n".
                            "【3】 qu新闻搜索\n\n".
                            "【4】 qu智能回复\n\n".
                            "【0】 返回主菜单\n";

                $resultStr  =   sprintf(TXTTPL,$fromUsername,$toUsername,$time,"text",$showinfo);
                echo $resultStr;
            }
        }else{
            echo "获取参数失败";exit;
        }
    }

}
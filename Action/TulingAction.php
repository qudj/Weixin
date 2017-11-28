<?php
class TulingAction extends Action
{ 
     public function responseMessage(){
        $redis = RedisCache::getInstance();
        $postStr = file_get_contents('php://input');
        $postObj    =   simplexml_load_string($postStr,'SimpleXMLElement',LIBXML_NOCDATA);
        $msgtype    =   trim($postObj->MsgType);

        if($msgtype == 'voice'){
            $content    =   trim($postObj->Recognition);
        }else{
            $content    =   trim($postObj->Content);
        }

        $fromUsername   =   $postObj->FromUserName;
        $toUsername =   $postObj->ToUserName;
        $time = time();
        $skey = $fromUsername."_select";
        $select = $redis->get($skey);

        if(empty($select)){
                $showinfo = "你好，我是图灵机器人！";
        }else{
            if(empty($content) && ($msgtype == 'voice')){
                $showinfo = "对不起，未能识别您的语音消息！";
            }else{
                $tuservice = new TulingService();
                $showinfo = $tuservice->get($content);
            }
        }
        
        $redis->set($skey, 4, 600);
        $resultStr  =   sprintf(TXTTPL,$fromUsername,$toUsername,$time,"text",$showinfo);
        echo $resultStr;
     } 

} 


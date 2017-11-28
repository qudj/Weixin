<?php
class TranslateAction extends Action
{
     public function responseMessage(){
        $redis = RedisCache::getInstance();
        $postStr = file_get_contents('php://input');
        $postObj    =   simplexml_load_string($postStr,'SimpleXMLElement',LIBXML_NOCDATA);
        $msgtype    =   trim($postObj->MsgType);
        $content    =   trim($postObj->Content);
        $fromUsername   =   $postObj->FromUserName;
        $toUsername =   $postObj->ToUserName;
        $time = time();
        $skey = $fromUsername."_select";
        $select = $redis->get($skey);

        if(empty($select)){
                $showinfo = "欢迎使用Qu翻译！";
        }else{
                $trservice = new TranslateService();
                $showinfo = $trservice->get($content);
        }

        $redis->set($skey, 1, 600);
        $resultStr  =   sprintf(TXTTPL,$fromUsername,$toUsername,$time,"text",$showinfo);
        echo $resultStr;
        
     }

} 


<?php
class WeatherAction extends Action
{ 
     public function responseMessage(){
        $comService = CommonService::getInstance();
        $postStr = file_get_contents('php://input');
        $postObj    =   simplexml_load_string($postStr,'SimpleXMLElement',LIBXML_NOCDATA);
        $msgtype    =   trim($postObj->MsgType);
        $content    =   trim($postObj->Content);
        $fromUsername   =   $postObj->FromUserName;
        $toUsername =   $postObj->ToUserName;
        $time = time();

        $cacheData =  $comService->CacheGet($fromUsername,10*60);
        $select = $cacheData['select'];

        if(empty($select)){
                $showinfo = "欢迎使用Qu天气！\n\n*回复城市名称获取天气信息";
        }else{
                $content = empty(strstr($content,"市",true))?$content:strstr($content,"市",true);
                $showinfo = $content."：\n";
                $showinfo .= $comService->GetWeather($content);
        }

        $resultStr  =   sprintf(TXTTPL,$fromUsername,$toUsername,$time,"text",$showinfo);
        echo $resultStr;
        $comService->CacheSet($fromUsername,array('select'=>2));
     }

} 


<?php
class ErrorAction extends Action
{ 
	public function responseMessage(){
		$postStr = file_get_contents('php://input');
        $this->postObj    =   simplexml_load_string($this->postStr,'SimpleXMLElement',LIBXML_NOCDATA);
        $this->msgtype    =   trim($this->postObj->MsgType);
        $this->content     =   trim($this->postObj->Content);
        $this->fromUsername   =   $this->postObj->FromUserName;
        $this->toUsername =   $this->postObj->ToUserName;
        $time = time();
        $showinfo = "未知错误";

        $resultStr  =   sprintf(TXTTPL,$this->fromUsername,$this->toUsername,$time,"text",$showinfo);
        echo $resultStr;
	} 

} 


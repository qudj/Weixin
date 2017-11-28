<?php
class TulingService
{
	public function get($keyname){
        $keyname = urlencode($keyname);
		$url = "http://op.juhe.cn/robot/index?info=".$keyname."&key=1c7e3aa65dda9cfb86fed09e8c5a09f0";
		$httpclient = HttpClient::getInstance();
		$data = $httpclient->get($url);
		$data = json_decode($data,TRUE);

		$answer = $data['result']['text'];

		$answer = str_replace("<br>", "\n", $answer);
		$answer = str_replace(";", "\n", $answer);
		$answer = str_replace(":", ":\n", $answer);

		return $answer;		
	}
}
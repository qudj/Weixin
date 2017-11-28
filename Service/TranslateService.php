<?php
class TranslateService
{
	public function get($keyname){
	    $keyname = urlencode($keyname);
	    $url = "http://fanyi.youdao.com/openapi.do?keyfrom=qudjweb&key=653939050&type=data&doctype=json&version=1.1&q=".$keyname;
	    
		$httpclient = HttpClient::getInstance();
		$data = $httpclient->get($url);
		$data = json_decode($data,TRUE);
		
		if(array_key_exists("basic", $data)){
		    $str = "翻译：".$data['translation'][0]."\n";
		    $str .= "其它：".implode(",", $data['basic']['explains'])."\n";
		    $str .= "读音：".$data['basic']['phonetic']."\n";
		}else{
		    $str = "翻译：".$data['translation'][0]."\n";
		}
		
		return $str;		
	}
}
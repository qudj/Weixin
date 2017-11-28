<?php
class CommonService
{
    private static $instance = NULL;

    //获取单例
    public static function getInstance() {
        if (!isset (self::$instance)) {
            self::$instance = new CommonService();
        }
        return self::$instance;
    }

	private function __construct(){} 

	public function CurlGetData($url,$header=array()){
		$ch = curl_init($url) ;   
		curl_setopt($ch, CURLOPT_ENCODING, "gzip"); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	public function CurlPostData($url,$curlPost,$header=''){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		curl_setopt($ch, CURLOPT_ENCODING, "gzip"); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	public function CacheSet($keyname,$data){

		$data = json_encode($data);
		$filename = './'.__APPNAME__.'/Data/Session/'.$keyname.'.txt';
		if(file_put_contents($filename, $data)){
			return true;
		}else{
			return false;
		}
	}

	public function CacheGet($keyname,$savetime){
		$filename = './'.__APPNAME__.'/Data/Session/'.$keyname.'.txt';
		if(time()-filemtime($filename)<$savetime){
			$data = file_get_contents($filename);
			return json_decode($data,true);
		}else{
			return false;
		}
	}

	public function GetWeather($keyname){
		$keyname = urlencode($keyname);
		$url = "http://apistore.baidu.com/microservice/weather?city=".$keyname;
		$data = $this->CurlGetData($url);		
		$data = json_decode($data,TRUE);
		if($data['errNum']!=0){ return "您输入的城市有误";}

		$str = "今天：".$data['data']['forecast'][0]['date']."\n";
		$str .= "天气：".$data['data']['forecast'][0]['type']."\n";
		$str .= "温度：".$data['data']['forecast'][0]['low']."~".$data['data']['forecast'][0]['high']."\n";
		$str .= "风向：".$data['data']['forecast'][0]['fengxiang']." ".$data['data']['forecast'][0]['fengli']."\n\n";
		
		$str .= "明天：".$data['data']['forecast'][1]['date']."\n";
		$str .= "天气：".$data['data']['forecast'][1]['type']."\n";
		$str .= "温度：".$data['data']['forecast'][1]['low']."~".$data['data']['forecast'][1]['high']."\n";
		$str .= "风向：".$data['data']['forecast'][1]['fengxiang']." ".$data['data']['forecast'][1]['fengli']."\n";
		return $str;
	}

	public function GetNews($keyname=''){
		$keyname = urlencode($keyname);
		$url = "http://apis.baidu.com/showapi_open_bus/channel_news/search_news?channelId=5572a109b3cdc86cf39001db&title=".$keyname."&page=1";
		$header = array(
					'apikey:9de915e5820a284f97f65bf7d0466831',
				  );
		$data = $this->CurlGetData($url,$header);	
		$data = json_decode($data,TRUE);

		$news = $data['showapi_res_body']['pagebean']['contentlist'];
		foreach ($news as &$obj) {
			if(!empty($obj['imageurls'])){
				$tmp = $news[0];
				$news[0] = $obj;
				$obj = $tmp;
				break;
			}
		}
		return $news;
	}

	public function GetIPCity($ip=''){
		$url = "http://apis.juhe.cn/ip/ip2addr?ip=".$ip."&key=5ff7829cbecf1273b730ddcccecdec17";
		$data = $this->CurlGetData($url);
		$data = json_decode($data,TRUE);
		$city = $data['result']['area'];

		return $city;
	}

}
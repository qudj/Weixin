<?php
class FactoryService
{ 
	public static function create($type) 
	{ 
		switch($type) 
		{ 
			case 1: 
				$trans = new TranslateAction;
				return $trans->responseMessage(); break; 
			case 2: 
				$weather = new WeatherAction;
				return $weather->responseMessage(); break; 				
			case 3: 
				$news = new NewsAction;
				return $news->responseMessage(); break; 				
			case 4: 
				$tuling = new TulingAction;
				return $tuling->responseMessage(); break; 							
			default: 
				$error = new ErrorAction;
				return $error->responseMessage(); break;
		} 
	} 
}
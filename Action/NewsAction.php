<?php
class NewsAction extends Action
{ 
    public function responseMessage(){
        //$comService = CommonService::getInstance();
        $redis = RedisCache::getInstance();
        $postStr = file_get_contents('php://input');
        $postObj    =   simplexml_load_string($postStr,'SimpleXMLElement',LIBXML_NOCDATA);
        $msgtype    =   trim($postObj->MsgType);
        $content    =   trim($postObj->Content);
        $fromUsername   =   $postObj->FromUserName;
        $toUsername =   $postObj->ToUserName;
        $time = time();
        $skey = $fromUsername."_select";
        
        //$cacheData =  $comService->CacheGet($fromUsername,10*60);
        //$select = $cacheData['select'];
        $select = $redis->get($skey);

        if(empty($select)){
                $showinfo = "欢迎使用Qu新闻！\n\n*回复1-9获取最新新闻动态\n\n*回复关键字搜索相关新闻动态";
                $resultStr  =   sprintf(TXTTPL,$fromUsername,$toUsername,$time,"text",$showinfo);
                echo $resultStr;
                //$comService->CacheSet($fromUsername,array('select'=>3));
                $redis->set($skey, 3, 600);
                die;             
        }


        if($content>0 && $content<10){
            $content = '';
        }
        $newsarr = $comService->GetNews($content);
        $newsarr = array_slice($newsarr,0,6);
        if(count($newsarr)==0){
            $showinfo = "暂时没有关于'".$content."'的消息";
            $resultStr  =   sprintf(TXTTPL,$fromUsername,$toUsername,$time,"text",$showinfo);
            echo $resultStr;

        }else{
            $newitem = "<item>
                        <Title><![CDATA[%s]]></Title> 
                        <Description><![CDATA[%s]]></Description>
                        <PicUrl><![CDATA[%s]]></PicUrl>
                        <Url><![CDATA[%s]]></Url>
                        </item>";
                    
            $itemstr = '';

            foreach($newsarr as &$obj){
                $itemstr .= sprintf($newitem,$obj['title'],$obj['desc'],$obj['imageurls'][0]['url'],$obj['link']);
            }

            $resultStr = "<xml>
                    <ToUserName><![CDATA[".$fromUsername."]]></ToUserName>
                    <FromUserName><![CDATA[".$toUsername."]]></FromUserName>
                    <CreateTime>".$time."</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>".count($newsarr)."</ArticleCount>
                    <Articles>
                    ".$itemstr."
                    </Articles>
                    </xml>";
            
            echo $resultStr;
            $redis->set($skey, 3, 600);
            //$comService->CacheSet($fromUsername,array('select'=>3));        
        }
	} 

} 


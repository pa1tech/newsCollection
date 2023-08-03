<?php

$feed = file_get_contents("https://www.youtube.com/feeds/videos.xml?channel_id=UCmRbHAgG2k2vDUvb3xsEunQ");
$xml = new SimpleXmlElement($feed);
$path = "https://api.telegram.org/bot<token>";

$count = count($xml->entry);
for ($i=0; $i < $count; $i++) {
    $url = $xml->entry[$i]->link->attributes();
    $title = $xml->entry[$i]->title;
    $descp = $xml->entry[$i]->children( 'media', true )->group->description;
    if( time() - strtotime($xml->entry[$i]->published) < (1.5*24*60*60) ){
        if ( (str_contains( strtolower($descp), "india") and str_contains( strtolower($descp), "business") and str_contains( strtolower($descp), "hour")) or (str_contains( strtolower($descp), "top") and str_contains( strtolower($descp), "headlines")) ) {

        $fuull = explode("CNBC TV18 LIVE TV",$fuull)[0];
        $msg = date('jS M', strtotime($xml->entry[$i]->published))."\n".$url['href']."\n\n";//.$fuull; 
        echo $msg;
        foreach ($_GET as $chatId => $value) {
          echo "$chatId <br>";
          $chatId = intval($chatId); 
          file_get_contents($path."/sendmessage?chat_id=".$chatId."&parse_mode=HTML&text=".urlencode($msg));
        }

    }
}
}

//https://dcblog.dev/display-youtube-videos-from-a-users-rss-feed-with-php
?>

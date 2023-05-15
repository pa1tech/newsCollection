<?php

date_default_timezone_set("Asia/Kolkata");

$feed = file_get_contents("https://timesofindia.indiatimes.com/podcast/59660725.cms?ltype=english");
$xml = new SimpleXmlElement($feed);
$path = "https://api.telegram.org/bot<token>";

$count = count($xml->channel->item);
$pubD = $xml->channel->item[0]->pubDate;
echo time()."<br>";
echo strtotime($pubD)."<br>";
echo (time() - strtotime($pubD))."<br>";

if( time() - strtotime($pubD) < (15*60*60) ){
    echo $xml->channel->item[0]->guid;

    foreach ($_GET as $chatId => $value) {
      echo "$chatId <br>";
      $chatId = intval($chatId); 
      file_get_contents($path."/sendaudio?chat_id=".$chatId."&caption=".urlencode($xml->channel->item[0]->title)."&audio=".urlencode($xml->channel->item[0]->guid));
    }


}

?>
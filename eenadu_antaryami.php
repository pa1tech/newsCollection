<?php

$context = stream_context_create(
    array(
        "http" => array(
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
        )
    )
);

$feed = file_get_contents("https://www.eenadu.net/antaryami", false, $context);
//$feed = file_get_contents("gg.html", false, $context);
$dom = new DOMDocument;
$path = "https://api.telegram.org/bot<token>";

@$dom->loadHTML($feed); 
$xPath = new DOMXPath($dom );
$anchorTags = $xPath->evaluate("//div[@class=\"telugu_uni_body\"]//a/@href")[0];
echo $anchorTags->nodeValue;

foreach ($_GET as $chatId => $value) {
  echo "$chatId <br>";
  $chatId = intval($chatId);
  file_get_contents($path."/sendmessage?chat_id=".$chatId."&parse_mode=HTML&text=".urlencode($anchorTags->nodeValue));
}

?>

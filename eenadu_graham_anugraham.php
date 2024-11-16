<?php

$context = stream_context_create(
    array(
        "http" => array(
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
        )
    )
);

$feed = file_get_contents("https://www.eenadu.net/rashi-phalalu", false, $context);
$dom = new DOMDocument;
$path = "https://api.telegram.org/bot<token>";

@$dom->loadHTML($feed); 
$divs = $dom->getElementsByTagName('div');


$dateHtml = "";

foreach ($divs as $div){
    
    if( $div->getAttribute('class') == "center"){

        $articleDom = new DOMDocument;
        $textDom = new DOMDocument;

        $node = $textDom->importNode($div, true);
        $textDom->appendChild($node);
        $html = $textDom->saveHTML();
        echo $html."<br>";

        $html = strip_tags($html);
        $html = html_entity_decode($html);
        //$html = $html."\n\n";
        echo $html."<br>";

        $dateHtml = "<b>".$html."</b>";

    }

    if( $div->getAttribute('class') == "arti"){

        $articleDom = new DOMDocument;
        $textDom = new DOMDocument;

        $node = $articleDom->importNode($div, true);
        $articleNodes = $node->getElementsByTagName('p');
        
        $node = $textDom->importNode($articleNodes[0], true);
        $textDom->appendChild($node);
        $html = $textDom->saveHTML();
        echo $html."<br>";

        $html = strip_tags($html);
        $html = html_entity_decode($html);
        $html = str_replace(";","\n",$html);
        echo $html."<br>";

        foreach ($_GET as $chatId => $value) {
          echo "$chatId <br>";
          $chatId = intval($chatId);
          file_get_contents($path."/sendmessage?chat_id=".$chatId."&parse_mode=HTML&text=".urlencode($dateHtml.$html));
        }

    }
    
}


?>

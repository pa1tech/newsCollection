<?php

$feed = file_get_contents("https://www.nism.ac.in/nism-newsletter-2023/");
$path = "https://api.telegram.org/bot<token>";

$dom = new DOMDocument;

@$dom->loadHTML($feed);

$links = $dom->getElementsByTagName('a');
foreach ($links as $link){

    if(str_contains($link->getAttribute('href'), "https://www.nism.ac.in/wp-content/uploads/2023/")){
        if (str_contains( strtolower($link->nodeValue), strtolower(date('M')) )){ 

            foreach ($_GET as $chatId => $value) {
              echo "$chatId <br>";
              $chatId = intval($chatId); 
              file_get_contents($path."/senddocument?chat_id=".$chatId."&document=".urlencode($link->getAttribute('href')));
            }
            
        }
        else{
            echo strtolower($link->nodeValue); 
            echo strtolower(date('M'));
            echo $link->getAttribute('href'), '<br>';
        }

        
    }
}

?>

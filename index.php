<?php
header('Content-type: text/html; charset=utf-8');
require_once "lib/simple_html_dom.php";

//create an array where we'll save data
$product = array();
$comments = array();

//create a few different referer array to avoid captcha
$refArray = [
  "http://www.google.com",
  "http://www.yahoo.com",
  "http://www.yandex.ru",
  "http://www.bing.com",
  "https://duckduckgo.com/",
  "https://www.dogpile.com/"
];


$json_filename = 'product_links.json';

if(file_exists($json_filename)){
  $cat_links = json_decode(file_get_contents($json_filename));
}

for($i=0; $i<6; $i++){

  $url = 'https://www.amazon.com'.$cat_links[$i]->href;

  $int = rand(0, 5);
  $referer = $refArray[$int];
  
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_REFERER, $referer);
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_TIMEOUT, 40);
  curl_setopt($ch, CURLOPT_ENCODING ,"");

  $content = curl_exec($ch);
  curl_close($ch);


  $dom = str_get_html($content);

  $elements = $dom->find('.view-point');
  foreach ($elements as $element) {

    // get the positive comment
    $positive = $element->find('.positive-review');

      foreach ($positive as $item ) {
      $name = $item->find('span.a-profile-name',0)->plaintext;
      $date = $item->find('span.review-date',0)->plaintext;
      $text = $item->find('.a-spacing-top-mini',0)->plaintext;
      $link = $item->find('a.a-size-base',0);

      $text =  str_replace(array("\r\n", "\r", "\n"), '',  strip_tags($text));
      $link = "http://www.amazon.com". $link->href;

      $arr = compact('name', 'date', 'text', 'link');

      if(!in_array($arr, $comments)){
        $comments['positive']= $arr;
      }
      
    }

    // get the critical comment
    $critical = $element->find('.critical-review');
    foreach ($critical as $item ) {
      $name = $item->find('span.a-profile-name',0)->plaintext;
      $date = $item->find('span.review-date',0)->plaintext;
      $text = $item->find('.a-spacing-top-mini',0)->plaintext;
      $link = $item->find('a.a-size-base',0);

      $text =  str_replace(array("\r\n", "\r", "\n"), '',  strip_tags($text));
      $link = "http://www.amazon.com". $link->href;

      $arr = compact('name', 'date', 'text', 'link');

      if(!in_array($arr, $comments)){
        $comments['critical']= $arr;
      }
    }

    // get the title of product
    $title = $dom->find('.a-fixed-left-grid-inner');

    $item = $title[0]; //instead foreach
      $a = $item->find('.product-title',0);
      
      if(!array_key_exists($a->plaintext, $product)){
        $product["$a->plaintext"] = $comments;
      }
    
  }

}


echo "<pre>";
print_r($product);
echo "</pre>";

<?php

$account = 0; // your account id here

include 'src/service/SearchService.php';

// example - do a search for pictures of waterfalls
$request = new BigstockAPI\Service\SearchService($account);
$request->addTerm('waterfalls');
$request->excludeTerm('vista');
$request->setOrder('new');
$request->setIllustrationConstraint('n');
$request->setVectorConstraint('n');
$request->setSizeConstraint('l');
$response = $request->fetchJSON();

$json = json_decode($response);
if ($json->response_code == '200' && $json->message == 'success') {
    echo '<ul>';
    foreach($json->data->images as $image) {
        $url = $image->small_thumb->url;
        $height = $image->small_thumb->height;
        $width = $image->small_thumb->width;
        
        $title = $image->title;
        
        echo "<li><img src=\"{$url}\" alt=\"{$title}\" height=\"{$height}\" width=\"{$width}\" /> - {$title}</li>";
    }
    echo '</ul>';
}
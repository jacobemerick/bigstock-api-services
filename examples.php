<?php

$account = 0; // your account id here

// example - do a search for pictures of waterfalls
include 'src/service/SearchService.php';
$request = new BigstockAPI\Service\SearchService($account);
$request->addTerm('waterfalls');
$request->excludeTerm('vista');
$request->setOrder('new');
$request->setIllustrationConstraint('n');
$request->setSizeConstraint('l');
$request->setLimit(20);
$response = $request->fetchJSON();

$json = json_decode($response);
if ($json->response_code == 200 && $json->message == 'success') {
    echo '<h1>Image Search Result</h1>';
    echo '<ul>';
    foreach ($json->data->images as $image) {
        $url = $image->small_thumb->url;
        $height = $image->small_thumb->height;
        $width = $image->small_thumb->width;
        
        $title = $image->title;
        
        echo "<li><img src=\"{$url}\" alt=\"{$title}\" height=\"{$height}\" width=\"{$width}\" /> - {$title}</li>";
    }
    echo '</ul>';
}

// example - fetch all categories
include 'src/service/CategoryService.php';
$request = new BigstockAPI\Service\CategoryService($account);
$response = $request->fetchJSON();

$json = json_decode($response);
if ($json->response_code == 200 && $json->message == 'success') {
    echo '<h1>Category Fetch Result</h1>';
    echo '<ul>';
    foreach ($json->data as $category) {
        echo "<li>{$category->name}</li>";
    }
    echo '</ul>';
}
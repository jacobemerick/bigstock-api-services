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

// example - fetch details on a single image
$image = 5633507;

include 'src/service/ImageDetailService.php';
$request = new BigstockAPI\Service\ImageDetailService($account);
$request->setImage($image);
$response = $request->fetchJSON();

$json = json_decode($response);
if ($json->response_code == 200 && $json->message == 'success') {
    echo '<h1>Image Detail Fetch Result</h1>';
    
    $image_data = $json->data->image;
    echo '<dl>';
    echo '<dt>ID</dt>';
    echo "<dd>{$image}</dd>";
    echo '<dt>Title</dt>';
    echo "<dd>{$image_data->title}</dd>";
    echo '<dt>Preview</dt>';
    echo "<dd><img src=\"{$image_data->preview->url}\" alt=\"{$image_data->description}\" height=\"{$image_data->preview->height}\" width=\"{$image_data->preview->width}\" /></dd>";
    echo '<dt>Keywords</dt>';
    echo '<dd>';
    echo '<ul>';
    
    $keyword_list = $image_data->keywords;
    $keyword_list = explode(',', $keyword_list);
    
    foreach ($keyword_list as $keyword) {
        echo "<li>{$keyword}</li>";
    }
    
    echo '</ul>';
    echo '</dd>';
    echo '</dl>';
}

// example - purchase an image
$secret_key = ''; // put your secret key from the /partners page here
$image = 5633507;

include 'src/service/PurchaseService.php';
$request = new BigstockAPI\Service\PurchaseService($account, $secret_key);
$request->setImage($image);
$request->setSizeCode('m');
$response = $request->fetchJSON();

$json = json_decode($response);
if ($json->response_code == 200 && $json->message == 'success') {
    echo '<h1>Image Purchase Request</h1>';
    
    $purchase_data = $json->data;
    echo '<dl>';
    echo '<dt>ID</dt>';
    echo "<dd>{$image}</dd>";
    echo '<dt>Amount</dt>';
    echo "<dd>{$purchase_data->currency_amount} ({$purchase_data->currency_code})</dd>";
    echo '<dt>Download Key</dt>';
    echo "<dd>{$purchase_data->download_id}</dd>";
    echo '</dl>';
}
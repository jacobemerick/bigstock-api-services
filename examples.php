<?php

$account = 0; // YOUR ACCOUNT ID HERE


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

if ($response->response_code == 200 && $response->message == 'success') {
    echo '<h1>Image Search Result</h1>';
    
    $images = $response->data->images;
    
    echo '<ul>';
    foreach ($images as $image) {
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

if ($response->response_code == 200 && $response->message == 'success') {
    echo '<h1>Category Fetch Result</h1>';
    echo '<ul>';
    foreach ($response->data as $category) {
        echo "<li>{$category->name}</li>";
    }
    echo '</ul>';
}


// example - fetch details on a single image
$image = 5633507; // IMAGE ID HERE

include 'src/service/ImageDetailService.php';
$request = new BigstockAPI\Service\ImageDetailService($account);
$request->setImage($image);
$response = $request->fetchJSON();

if ($response->response_code == 200 && $response->message == 'success') {
    echo '<h1>Image Detail Fetch Result</h1>';
    
    $image_data = $response->data->image;
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
$secret_key = ''; // YOUR SECRET KEY HERE
$image = 5633507; // IMAGE ID HERE

include 'src/service/PurchaseService.php';
$request = new BigstockAPI\Service\PurchaseService($account, $secret_key);
$request->setImage($image);
$request->setSizeCode('m');
$response = $request->fetchJSON();

if ($response->response_code == 200 && $response->message == 'success') {
    echo '<h1>Image Purchase Request</h1>';
    
    $purchase_data = $response->data;
    echo '<dl>';
    echo '<dt>ID</dt>';
    echo "<dd>{$image}</dd>";
    echo '<dt>Amount</dt>';
    echo "<dd>{$purchase_data->currency_amount} ({$purchase_data->currency_code})</dd>";
    echo '<dt>Download Key</dt>';
    echo "<dd>{$purchase_data->download_id}</dd>";
    echo '</dl>';
}


// example - download an image
$secret_key = ''; // YOUR SECRET KEY HERE
$download_id = 0; // DOWNLOAD ID HERE

include 'src/service/DownloadService.php';
$request = new BigstockAPI\Service\DownloadService($account, $secret_key);
$request->setDownload($download_id);
$response = $request->fetchResponse();

// if the request fails, it returns as json
$json_response = json_decode($response);
if ($json_response === null) {
    $image_data = base64_encode($response);
    
    echo '<h1>Image Download Request</h1>';
    echo "<img src=\"data:image/jpeg;base64,{$image_data}\" />";
}


// example - look up a private lightbox
$secret_key = ''; // YOUR SECRET KEY HERE
$lightbox_id = 0; // LIGHTBOX ID HERE

include 'src/service/LightboxService.php';
$request = new BigstockAPI\Service\LightboxService($account);
$request->setSecretKey($secret_key);
$request->setLightbox($lightbox_id);
$response = $request->fetchJSON();

if ($response->response_code == 200 && $response->message = 'success') {
    echo '<h1>Lightbox Request</h1>';
    
    $lightbox = $response->data->lightbox;
    
    echo '<dl>';
    echo '<dt>Title</dt>';
    echo "<dd>{$lightbox->name}</dd>";
    echo '<dt>Items</dt>';
    echo "<dd>{$lightbox->items}</dd>";
    echo '</dl>';
    
    $images = $response->data->images;
    
    echo '<ul>';
    foreach ($images as $image) {
        $url = $image->small_thumb->url;
        $height = $image->small_thumb->height;
        $width = $image->small_thumb->width;
        
        $title = $image->title;
        
        echo "<li><img src=\"{$url}\" alt=\"{$title}\" height=\"{$height}\" width=\"{$width}\" /> - {$title}</li>";
    }
}

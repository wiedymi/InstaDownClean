<?php

require_once 'InstagramDownload.php';
use WiedyMi\InstagramDownload as InstagramDownload;

if(!isset($_POST)){
    $DownloadLink = [
        "status" => false,
    ];

    $DownloadLink = json_encode($DownloadLink);
    echo $DownloadLink;
}

$urlast = $_POST["source_url"];
$url = $_POST["source_url"];
    
try {
$client = new InstagramDownload($url);
$url = $client->getDownloadUrl(); // Returns the download URL.
$type = $client->getType(); // Returns "image" or "video" depending on the media type.
}
catch (\InvalidArgumentException $exception) {
/*
* \InvalidArgumentException exceptions will be thrown if there is a validation 
* error in the URL. You might want to break the code flow and report the error 
* to your form handler at this point.
*/
$error = $exception->getMessage();
}
catch (\RuntimeException $exception) {
/*
* \RuntimeException exceptions will be thrown if the URL could not be 
* fetched, parsed, or a media could not be extracted from the URL. 
*/
$error = $exception->getMessage();
}
if($url === $urlast){
    $status = false;
}
else{
    $status = true;
}
$DownloadLink = [
    "url" => $url,
    "status" => $status,
];

$DownloadLink = json_encode($DownloadLink);
echo $DownloadLink;
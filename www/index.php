<?php
if (!session_id()) @session_start();

include_once "autoload.php";

header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Origin: *');
header('Content-Type:application/json');

$inputData = file_get_contents('php://input');

$inputDataParsed = json_decode($inputData, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSON is not VALID\n";
    exit;
}

$dfltClass  = "tran";
$className  = $inputDataParsed['provider'];

if (class_exists($className)){
    $provider = new $className($inputDataParsed);    
} else {
    $provider = new $dfltClass($inputDataParsed);
}

    echo json_encode($provider->providerData);
    http_response_code(200);

?>
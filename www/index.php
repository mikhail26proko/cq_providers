<?php
if (!session_id()) @session_start();

header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Origin: *');
header('Content-Type:application/json');

$inputData = file_get_contents('php://input');

$inputDataParsed = json_decode($inputData, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSON is not VALID\n";
    exit;
}

    echo json_encode($inputDataParsed);
    http_response_code(200);

?>
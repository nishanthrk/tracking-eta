<?php
//$data = [
//    'orderId' => 1,
//    'status' => 'Normal'
//];
//print_r(json_encode($data));die;
require_once '../vendor/autoload.php';
include_once '../config/ApiResponse.php';
include_once '../config/Utility.php';

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$apiResponse = new ApiResponse();
$utility = new Utility();

$data = json_decode(file_get_contents("php://input"));

if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST'
    && !empty($data->originLat) && !empty($data->originLng) && !empty($data->destinationLat) && !empty($data->destinationLng)) {

    $origin = $data->originLat . ',' . $data->originLng;
    $destination = $data->destinationLat . ',' . $data->destinationLng;
    $response = $utility->fetch($origin, $destination);

    if (!$response['status']) {
        $apiResponse->unprocessableEntity(-2, $response['data']);
    } else {
        $element = reset($response['data']['rows']);
        $distance = reset($element['elements']);

        $apiResponse->ok(1, $distance);
    }

} else {
    $apiResponse->unprocessableEntity(-1, 'Unable to create product. Data is incomplete.');
}
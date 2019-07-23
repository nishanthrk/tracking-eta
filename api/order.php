<?php
//$data = [
//    'orderId' => 1,
//    'status' => 'Normal'
//];
//print_r(json_encode($data));die;
require_once '../vendor/autoload.php';
include_once '../config/ApiResponse.php';
include_once '../config/Database.php';

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$apiResponse = new ApiResponse();
$client = new Predis\Client();
$database = new Database();

$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'GET') {

    $result = $database->getOrder();

    $apiResponse->ok(1, $result);

} elseif(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST' && !empty($data->orderId) && !empty($data->status)) {

    $orderId = 'ORDER::'.  $data->orderId;

    try {
        $order = json_decode($client->get($orderId), true);

        $order['status'] = $data->status;

        $client->set($orderId, json_encode($order));

        if ($data->status == 'FINISHED_DELIVERY') {
            $client->set('LAST_LOCATION', json_encode(['lat' => $order['lat'], 'lng' => $order['lng']]));
        }

        $result = $database->getOrder();

        $apiResponse->ok(1, $result);

    } catch (\Exception $e) {
        $apiResponse->unprocessableEntity(-1, 'Internal Server Error');
    }

} else {
    $apiResponse->unprocessableEntity(-1, 'Unable to create product. Data is incomplete.');
}
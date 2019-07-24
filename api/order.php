<?php
//$data = [
//    'orderId' => 1,
//    'status' => 'Normal'
//];
//print_r(json_encode($data));die;
require_once '../vendor/autoload.php';
include_once '../config/ApiResponse.php';
include_once '../config/Database.php';
include_once '../config/Utility.php';

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$apiResponse = new ApiResponse();
$client = new Predis\Client();
$database = new Database();
$utility = new Utility();

$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'GET') {

    $result = $database->getOrder();

    $apiResponse->ok(1, $result);

} elseif(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST' && !empty($data->orderId) && !empty($data->status)
    && !empty($data->originLat) && !empty($data->originLng) && !empty($data->destinationLat) && !empty($data->destinationLng)) {

    $orderId = 'ORDER::'.  $data->orderId;

    try {

        $order = json_decode($client->get($orderId), true);

        if ($data->status == 'FINISHED_DELIVERY') {
            $radius = $utility->haversineGreatCircleDistance($data->originLat, $data->originLng, $data->destinationLat, $data->destinationLng);
            if ($radius > 0.2) {
                $apiResponse->unprocessableEntity(-2, 'You cannot end because youre too far from the delivery location');
            }
            $client->set('LAST_LOCATION', json_encode(['lat' => $order['lat'], 'lng' => $order['lng']]));
        }

        $order['status'] = $data->status;

        $client->set($orderId, json_encode($order));

        $result = $database->getOrder();

        $apiResponse->ok(1, $result);

    } catch (\Exception $e) {
        $apiResponse->unprocessableEntity(-1, 'Internal Server Error');
    }

} else {
    $apiResponse->unprocessableEntity(-1, 'Unable to create product. Data is incomplete.');
}
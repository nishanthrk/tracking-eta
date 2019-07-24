<?php

class Utility {

    public $key;

    public function __construct()
    {
        $this->key = 'AIzaSyCGbjymNN5vlknRZgGz8PBbKd7Sulf_bBc';
    }

    public function fetch($origin, $destination)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=". $origin ."&destinations=". $destination ."&key=" . $this->key;
        $httpHeader = array(
            'Content-Type: application/json',
        );
        $method = 'GET';
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $httpHeader,
        );

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if (!empty($error)) {
            $data = ['status' => false, 'data' => 'Internal Server Error'];
        } else {
            $data = ['status' => true, 'data' => json_decode($response, true)];
        }

        return $data;
    }

    public function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
    {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);
        $latDelta = $latTo - $latFrom;
        $lonDelta= $lonTo - $lonFrom;
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

}

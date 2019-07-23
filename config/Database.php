<?php

class Database
{
    public $client;

    public function __construct()
    {
        $this->client = new Predis\Client();;
    }

    public function getOrder()
    {
        $client = $this->client;
        $query = $client->keys('ORDER::*');
        if (empty($query)) {

            $sample = [
                [
                    'id' => 1,
                    'address' => 'Mangalam Paradise, Rohini Sec 3, New Delhi',
                    'lat' => '28.6980571',
                    'lng' => '77.1125758',
                    'status' => 'ON_DELIVERY'
                ],
                [
                    'id' => 2,
                    'address' => 'Qutub Minar, Mehrauli, New Delhi',
                    'lat' => '28.5244801',
                    'lng' => '77.1833319',
                    'status' => 'ON_DELIVERY'
                ],
                [
                    'id' => 3,
                    'address' => 'Connaught Place, New Delhi',
                    'lat' => '28.6289332',
                    'lng' => '77.2065322',
                    'status' => 'ON_DELIVERY'
                ],
                [
                    'id' => 4,
                    'address' => 'Ajmal Khan Road, Karol Bagh, New Delhi',
                    'lat' => '28.6477322',
                    'lng' => '77.1881408',
                    'status' => 'ON_DELIVERY'
                ],
                [
                    'id' => 5,
                    'address' => 'Cyber Hub, Gurgaon, Haryana',
                    'lat' => '28.4946493',
                    'lng' => '77.0866532',
                    'status' => 'ON_DELIVERY'
                ]
            ];

            foreach ($sample as $key => $value) {
                $client->set('ORDER::' . $value['id'], json_encode($value));
            }

            $client->del(['LAST_LOCATION']);
            $query = $client->keys('ORDER::*');
        }

        $result = $current = [];
        foreach ($query as $order) {
            $result[] = json_decode($client->get($order), true);
        }

        usort($result, function ($a ,$b) {
            return $a['id'] >= $b['id'] ? 1 : -1;
        });

        $active = array_search('ACTIVE_DELIVERY', array_column($result, 'status'));

        if (isset($result[$active]) && $result[$active]['status'] == 'ACTIVE_DELIVERY') {
            $current = $result[$active];
            $current['nextStatus'] = 'FINISHED_DELIVERY';
            $current['display'] = 'End';
        } else {
            foreach ($result as $key => $value) {
                if ($value['status'] == 'ON_DELIVERY') {
                    $current = $value;
                    $current['nextStatus'] = 'ACTIVE_DELIVERY';
                    $current['display'] = 'Start';
                    break;
                }
            }
        }


        $response = [];
        $response['orders'] = $result;

        $lastLocation = json_decode($client->get('LAST_LOCATION'), true);


        if (!empty($current)) {
            $current['nextLat'] = (float) $current['lat'];
            $current['nextLng'] = (float) $current['lng'];
            $current['lat'] = $lastLocation['lat'] ?? 28.7041;
            $current['lng'] = $lastLocation['lng'] ?? 77.1025;
            $response['current'] = $current;
        }

        return $response;
    }
}
<?php

# $url = 'http://api.onecms/v1/order/test/?id=33';
$url = 'http://api.onecms/v1/order/total/';

$request = [
    'datebirth' => '1990-01-01',
    'gender' => 'woman',
    'datedelivery' => '2024-07-01 18:30:00',
    'product' => [
        [
            'id' => 1,
            'amount' => 10,
            'price' => 1000.50,
        ],
        [
            'id' => 5,
            'amount' => 5,
            'price' => 199, 99,
        ],
        [
            'id' => 25,
            'amount' => 1,
            'price' => 300.99,
        ],
    ],
];



$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request, '', '&'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
$response = curl_exec($ch);
curl_close($ch);

print $response;



?>
<?php

// Simple test script to debug API registration
$url = 'http://localhost:8081/api/v1/auth/register';
$data = [
    'name' => 'Test User',
    'email' => 'test' . time() . '@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'phone' => '+1234567890'
];

$options = [
    'http' => [
        'header' => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        'method' => 'POST',
        'content' => json_encode($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo "Error making request\n";
    print_r($http_response_header);
} else {
    echo "Response: " . $result . "\n";
}

<?php
// Quick API test
$baseUrl = 'http://localhost:8000/api/task';

// Test create
$data = ['title' => 'Test Task from PHP'];
$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
    ],
];
$context  = stream_context_create($options);
$result = file_get_contents($baseUrl . '/create', false, $context);
echo "Create Response: " . $result . "\n";

// Test list
$tasks = file_get_contents($baseUrl . '/list');
echo "List Response: " . $tasks . "\n";
?>
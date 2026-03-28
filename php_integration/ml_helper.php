<?php
/**
 * ml_helper.php — Shared HTTP client for all ML service calls
 * Include this file in any PHP page that needs ML features.
 * 
 * Usage:
 *   require_once 'ml_helper.php';
 *   $result = callMLService('http://localhost:5001/health');
 */

function callMLService(string $url, string $method = 'GET', array $data = []): ?array {
    $options = [
        'http' => [
            'method' => $method,
            'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
            'timeout' => 10,
            'ignore_errors' => true,
        ]
    ];

    if ($method === 'POST' && !empty($data)) {
        $options['http']['content'] = json_encode($data);
    }

    $ctx = stream_context_create($options);
    $response = @file_get_contents($url, false, $ctx);

    if ($response === false) {
        error_log("ML Service unreachable: $url");
        return null;
    }

    return json_decode($response, true);
}
?>

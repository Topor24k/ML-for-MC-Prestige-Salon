<?php
/**
 * feature_3_forecast.php — Revenue Forecasting Integration
 * Feature 3: Time-series revenue prediction for financial planning
 */

require_once 'ml_helper.php';

/**
 * Get revenue forecast for next N months
 * Display on Financial Dashboard (owner/controller view only)
 */
function getRevenueForecast(int $months = 3): array {
    $result = callMLService(
        "http://localhost:5003/forecast?periods=$months"
    );

    if ($result === null) {
        return [];
    }

    // Format for display
    $formatted = [];
    foreach ($result as $month) {
        $formatted[] = [
            'month'       => date('F Y', strtotime($month['ds'])),
            'month_iso'   => $month['ds'],
            'predicted'   => '₱' . number_format($month['yhat'], 2),
            'predicted_raw' => $month['yhat'],
            'range_low'   => '₱' . number_format($month['yhat_lower'], 2),
            'range_low_raw' => $month['yhat_lower'],
            'range_high'  => '₱' . number_format($month['yhat_upper'], 2),
            'range_high_raw' => $month['yhat_upper'],
            'raw'         => $month
        ];
    }

    return $formatted;
}

/**
 * Get model accuracy stats for validation
 * December 2025 actual vs predicted
 */
function getForecastAccuracy(): ?array {
    return callMLService('http://localhost:5003/accuracy');
}

/**
 * Get historical data + predictions (for chart)
 */
function getHistoricalForecast(): ?array {
    return callMLService('http://localhost:5003/historical');
}

/**
 * Get forecast system health
 */
function getForecastHealth(): ?array {
    return callMLService('http://localhost:5003/health');
}

/**
 * Format forecast for dashboard display
 */
function formatForecastForDashboard(array $forecast): string {
    $html = '<table class="forecast-table">';
    $html .= '<thead><tr><th>Month</th><th>Predicted</th><th>Range (Low-High)</th></tr></thead>';
    $html .= '<tbody>';
    
    foreach ($forecast as $f) {
        $html .= sprintf(
            '<tr><td>%s</td><td>%s</td><td>%s — %s</td></tr>',
            htmlspecialchars($f['month']),
            htmlspecialchars($f['predicted']),
            htmlspecialchars($f['range_low']),
            htmlspecialchars($f['range_high'])
        );
    }
    
    $html .= '</tbody></table>';
    return $html;
}

// ============================================
// HTTP Endpoint Handler
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'forecast') {
    header('Content-Type: application/json');
    
    $months = (int)($_GET['months'] ?? 3);
    $forecast = getRevenueForecast($months);
    
    echo json_encode([
        'forecast' => $forecast,
        'count' => count($forecast)
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'accuracy') {
    header('Content-Type: application/json');
    $accuracy = getForecastAccuracy();
    echo json_encode($accuracy ?? ['error' => 'unavailable']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'historical') {
    header('Content-Type: application/json');
    $historical = getHistoricalForecast();
    echo json_encode($historical ?? ['error' => 'unavailable']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'health') {
    header('Content-Type: application/json');
    $health = getForecastHealth();
    echo json_encode($health ?? ['status' => 'error']);
    exit;
}
?>

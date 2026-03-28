<?php
/**
 * feature_2_recommender.php — Service Recommender Integration
 * Feature 2: Collaborative Filtering-based service recommendations
 */

require_once 'ml_helper.php';

/**
 * Get service recommendations for a customer
 * Called after booking confirmation or appointment completion
 */
function getServiceRecommendations(int $customerId, int $topN = 3): array {
    $result = callMLService(
        'http://localhost:5001/recommend',
        'POST',
        ['customer_id' => $customerId, 'top_n' => $topN]
    );

    if ($result === null || empty($result['recommendations'])) {
        // Fallback: show most popular services
        $popular = callMLService('http://localhost:5001/popular');
        return $popular['popular_services'] ?? [];
    }

    return $result['recommendations'];
}

/**
 * Get most popular services (for new customers)
 */
function getPopularServices(): ?array {
    return callMLService('http://localhost:5001/popular');
}

/**
 * Get recommender system health status
 */
function getRecommenderHealth(): ?array {
    return callMLService('http://localhost:5001/health');
}

/**
 * Format recommendations for display on UI
 */
function formatRecommendationsForUI(array $recommendations): string {
    $html = '<div class="recommendations">';
    foreach ($recommendations as $service) {
        $html .= sprintf(
            '<div class="recommendation-chip" data-service="%s">Try: <strong>%s</strong></div>',
            htmlspecialchars($service),
            htmlspecialchars($service)
        );
    }
    $html .= '</div>';
    return $html;
}

// ============================================
// HTTP Endpoint Handler
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $customerId = $_POST['customer_id'] ?? $_GET['customer_id'] ?? null;
    $topN = (int)($_POST['top_n'] ?? $_GET['top_n'] ?? 3);
    
    if ($customerId === null) {
        echo json_encode(['error' => 'customer_id required']);
        exit;
    }
    
    $recommendations = getServiceRecommendations((int)$customerId, $topN);
    echo json_encode([
        'customer_id' => $customerId,
        'recommendations' => $recommendations,
        'count' => count($recommendations)
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'health') {
    header('Content-Type: application/json');
    $health = getRecommenderHealth();
    echo json_encode($health ?? ['status' => 'error']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'popular') {
    header('Content-Type: application/json');
    $popular = getPopularServices();
    echo json_encode($popular ?? ['error' => 'unavailable']);
    exit;
}
?>

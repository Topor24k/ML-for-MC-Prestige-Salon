<?php
/**
 * feature_4_sentiment.php — Review Sentiment Analysis Integration
 * Feature 4: Automated sentiment classification for customer feedback
 */

require_once 'ml_helper.php';

/**
 * Analyze a single customer review
 * Called immediately after feedback submission
 */
function analyzeFeedback(int $appointmentId, int $staffId, string $reviewText): array {
    $result = callMLService(
        'http://localhost:5004/analyze',
        'POST',
        [
            'review'         => $reviewText,
            'appointment_id' => $appointmentId,
            'staff_id'       => $staffId
        ]
    );

    if ($result === null) {
        return ['label' => 'Unknown', 'score' => 0, 'error' => true];
    }

    // Save to DB
    saveSentimentRecord($appointmentId, $staffId, $result['label'], $result['score']);

    // If negative, flag for owner attention
    if ($result['label'] === 'Negative') {
        flagForOwnerReview($appointmentId, $reviewText, $result['score']);
    }

    return $result;
}

/**
 * Analyze multiple reviews in batch
 */
function analyzeFeedbackBatch(array $reviews): ?array {
    return callMLService(
        'http://localhost:5004/analyze/batch',
        'POST',
        ['reviews' => $reviews]
    );
}

/**
 * Save sentiment record to database
 */
function saveSentimentRecord(int $apptId, int $staffId, string $label, float $score): void {
    // TODO: Implement DB save
    // INSERT INTO sentiment_results (appointment_id, staff_id, label, score, analyzed_at)
    // VALUES (?, ?, ?, ?, NOW())
    error_log("Sentiment - Appt: $apptId | Staff: $staffId | Label: $label | Score: $score");
}

/**
 * Flag negative reviews for owner attention
 */
function flagForOwnerReview(int $apptId, string $review, float $score): void {
    // TODO: Implement flagging and notification
    // INSERT INTO owner_flags (appointment_id, review_text, sentiment_score, flagged_at, resolved)
    // VALUES (?, ?, ?, NOW(), 0)
    // Then: send email/notification to Karen
    error_log("⚠️ FLAGGED NEGATIVE REVIEW - Appt: $apptId | Score: $score | Review: " . substr($review, 0, 50));
}

/**
 * Get monthly sentiment report
 */
function getMonthlySentimentReport(string $yearMonth): ?array {
    // TODO: Query DB directly for monthly stats
    // SELECT label, COUNT(*) as count, AVG(score) as avg_score
    // FROM sentiment_results
    // WHERE DATE_FORMAT(analyzed_at, '%Y-%m') = ?
    // GROUP BY label
    return null;
}

/**
 * Get sentiment system health
 */
function getSentimentHealth(): ?array {
    return callMLService('http://localhost:5004/health');
}

/**
 * Format sentiment for display (badge/label)
 */
function formatSentimentBadge(string $label, float $score): string {
    $color = match($label) {
        'Positive' => 'success',
        'Negative' => 'danger',
        'Neutral' => 'secondary',
        default => 'light'
    };
    
    $icon = match($label) {
        'Positive' => '✓',
        'Negative' => '✕',
        'Neutral' => '—',
        default => '?'
    };
    
    return sprintf(
        '<span class="badge bg-%s">%s %s (%.2f)</span>',
        $color,
        $icon,
        htmlspecialchars($label),
        $score
    );
}

// ============================================
// HTTP Endpoint Handler
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'analyze') {
    header('Content-Type: application/json');
    
    $apptId = (int)($_POST['appointment_id'] ?? 0);
    $staffId = (int)($_POST['staff_id'] ?? 0);
    $review = $_POST['review'] ?? '';
    
    if (empty($review)) {
        echo json_encode(['error' => 'Review text required']);
        exit;
    }
    
    $result = analyzeFeedback($apptId, $staffId, $review);
    echo json_encode($result);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'batch') {
    header('Content-Type: application/json');
    
    $data = json_decode(file_get_contents('php://input'), true);
    $reviews = $data['reviews'] ?? [];
    
    if (empty($reviews)) {
        echo json_encode(['error' => 'Reviews array required']);
        exit;
    }
    
    $result = analyzeFeedbackBatch($reviews);
    echo json_encode($result ?? ['error' => 'Service unavailable']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'report') {
    header('Content-Type: application/json');
    
    $yearMonth = $_GET['month'] ?? date('Y-m');
    $report = getMonthlySentimentReport($yearMonth);
    
    echo json_encode($report ?? ['message' => 'No data available']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'health') {
    header('Content-Type: application/json');
    $health = getSentimentHealth();
    echo json_encode($health ?? ['status' => 'error']);
    exit;
}
?>

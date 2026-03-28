<?php
/**
 * feature_1_chatbot.php — Customer Help Guide Integration
 * Feature 1: AI Chatbot for FAQ answering
 */

require_once 'ml_helper.php';

/**
 * Handle a customer chat message
 * POST /api/chat.php?message=...
 */
function handleChatMessage(string $userMessage): array {
    $result = callMLService(
        'http://localhost:5002/chat',
        'POST',
        ['message' => $userMessage]
    );

    if ($result === null) {
        return [
            'answer' => 'Our chat assistant is currently unavailable. Please message us on Facebook or call us at [phone number]!',
            'resolved_by_bot' => false,
            'error' => true
        ];
    }

    // Log to DB for analytics
    logChatSession($userMessage, $result['answer'], $result['resolved_by_bot']);

    return $result;
}

/**
 * Log chat sessions to database for analytics
 */
function logChatSession(string $question, string $answer, bool $resolved): void {
    // TODO: Implement DB logging
    // INSERT INTO chat_logs (question, answer, resolved_by_bot, created_at)
    // VALUES (?, ?, ?, NOW())
    error_log("Chat - Q: $question | Resolved: " . ($resolved ? 'Yes' : 'No'));
}

/**
 * Get chatbot health status
 */
function getChatbotHealth(): ?array {
    return callMLService('http://localhost:5002/health');
}

// ============================================
// HTTP Endpoint Handler
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $message = $_GET['message'] ?? $_POST['message'] ?? '';
    
    if (empty($message)) {
        echo json_encode(['error' => 'No message provided']);
        exit;
    }
    
    $result = handleChatMessage($message);
    echo json_encode($result);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'health') {
    header('Content-Type: application/json');
    $health = getChatbotHealth();
    echo json_encode($health ?? ['status' => 'error']);
    exit;
}
?>

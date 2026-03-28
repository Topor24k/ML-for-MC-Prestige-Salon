<?php
/**
 * READ_ME.php — PHP Integration Quick Start Guide
 * 
 * This folder contains all PHP integration files for the 4 ML services.
 * Each file handles integration with one ML feature.
 */

?>
<!DOCTYPE html>
<html>
<head>
    <title>MC Prestige Salon ML Integration — PHP Quick Start</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; }
        h1 { color: #333; }
        h2 { color: #666; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New'; }
        pre { background: #f0f0f0; padding: 15px; border-left: 4px solid #0066cc; overflow-x: auto; }
        .feature { border-left: 4px solid #0066cc; padding-left: 15px; margin: 15px 0; }
        .warning { background: #fff3cd; padding: 10px; border-left: 4px solid #ff9800; margin: 10px 0; }
        .success { background: #d4edda; padding: 10px; border-left: 4px solid #28a745; margin: 10px 0; }
    </style>
</head>
<body>

<h1>🎨 MC Prestige Salon — ML Integration (PHP)</h1>

<div class="section">
    <h2>📁 File Structure</h2>
    <pre>php_integration/
├── ml_helper.php              ← Shared HTTP client (required by all features)
├── feature_1_chatbot.php      ← Chat widget integration
├── feature_2_recommender.php  ← Service recommendations
├── feature_3_forecast.php     ← Revenue forecasting dashboard
├── feature_4_sentiment.php    ← Feedback sentiment analysis
└── READ_ME.php                ← This file
    </pre>
</div>

<div class="section">
    <h2>🚀 Quick Start (5 minutes)</h2>
    
    <div class="success">
        <strong>✓ Prerequisites:</strong> Python ML services must be running on ports 5001-5004
    </div>

    <h3>Step 1: Include the helper in your PHP files</h3>
    <pre><?php echo htmlspecialchars("<?php
require_once 'path/to/ml_helper.php';
?>"); ?></pre>

    <h3>Step 2: Use Feature Functions</h3>
    <pre><?php echo htmlspecialchars("<?php
// Feature 1: Ask chatbot a question
require_once 'feature_1_chatbot.php';
\$answer = handleChatMessage('Magkano ang rebond?');

// Feature 2: Get recommendations
require_once 'feature_2_recommender.php';
\$recs = getServiceRecommendations(\$customerId, 3);

// Feature 3: Get forecast
require_once 'feature_3_forecast.php';
\$forecast = getRevenueForecast(3);

// Feature 4: Analyze review
require_once 'feature_4_sentiment.php';
\$sentiment = analyzeFeedback(\$apptId, \$staffId, \$reviewText);
?>"); ?></pre>
</div>

<div class="section">
    <h2>🔧 Feature 1: Chatbot (Customer Help Guide)</h2>
    <div class="feature">
        <h3>Function: <code>handleChatMessage(string \$message)</code></h3>
        <p>Send customer question to chatbot. Returns answer + confidence score.</p>
        <pre><?php echo htmlspecialchars("<?php
\$result = handleChatMessage('How long does rebond take?');

// Response:
// [
//   'answer' => 'A rebond treatment typically takes 3 to 5 hours...',
//   'matched_question' => 'How long does rebond take?',
//   'confidence' => 0.85,
//   'resolved_by_bot' => true
// ]
?>"); ?></pre>
    </div>
    
    <div class="feature">
        <h3>HTTP Endpoint: POST /feature_1_chatbot.php?message=...</h3>
        <pre><?php echo htmlspecialchars("curl -X POST 'http://yoursite.com/api/feature_1_chatbot.php?message=Magkano%20ang%20rebond'"); ?></pre>
    </div>
</div>

<div class="section">
    <h2>🔧 Feature 2: Recommender (Service Suggestions)</h2>
    <div class="feature">
        <h3>Function: <code>getServiceRecommendations(int \$customerId, int \$topN = 3)</code></h3>
        <p>Get recommended services for a customer based on similar customers' behavior.</p>
        <pre><?php echo htmlspecialchars("<?php
\$recommendations = getServiceRecommendations(42, 3);

// Response: ['Rebond', 'Hair Treatment', 'Color']
?>"); ?></pre>
    </div>
    
    <div class="feature">
        <h3>HTTP Endpoint: POST /feature_2_recommender.php</h3>
        <pre><?php echo htmlspecialchars("curl -X POST 'http://yoursite.com/api/feature_2_recommender.php' \
  -d 'customer_id=42&top_n=3'"); ?></pre>
    </div>
</div>

<div class="section">
    <h2>🔧 Feature 3: Forecast (Revenue Prediction)</h2>
    <div class="feature">
        <h3>Function: <code>getRevenueForecast(int \$months = 3)</code></h3>
        <p>Get revenue forecast for next N months with confidence intervals.</p>
        <pre><?php echo htmlspecialchars("<?php
\$forecast = getRevenueForecast(3);

// Response:
// [
//   [
//     'month' => 'January 2026',
//     'predicted' => '₱125,340.50',
//     'predicted_raw' => 125340.50,
//     'range_low' => '₱100,000.00',
//     'range_high' => '₱150,000.00'
//   ],
//   ...
// ]
?>"); ?></pre>
    </div>
    
    <div class="feature">
        <h3>HTTP Endpoint: GET /feature_3_forecast.php?action=forecast&months=3</h3>
        <pre><?php echo htmlspecialchars("curl 'http://yoursite.com/api/feature_3_forecast.php?action=forecast&months=3'"); ?></pre>
    </div>

    <div class="feature">
        <h3>Get Model Accuracy: <code>getForecastAccuracy()</code></h3>
        <pre><?php echo htmlspecialchars("<?php
\$accuracy = getForecastAccuracy();
// Shows: actual Dec 2025 revenue vs Prophet's prediction + MAPE error
?>"); ?></pre>
    </div>
</div>

<div class="section">
    <h2>🔧 Feature 4: Sentiment (Review Analysis)</h2>
    <div class="feature">
        <h3>Function: <code>analyzeFeedback(int \$apptId, int \$staffId, string \$review)</code></h3>
        <p>Analyze customer review sentiment. Positive/Neutral/Negative classification.</p>
        <pre><?php echo htmlspecialchars("<?php
\$sentiment = analyzeFeedback(101, 2, 'Super happy with my hair! Very satisfied.');

// Response:
// [
//   'appointment_id' => 101,
//   'staff_id' => 2,
//   'label' => 'Positive',
//   'score' => 0.87,
//   'details' => [
//     'positive' => 0.72,
//     'neutral' => 0.28,
//     'negative' => 0.0
//   ]
// ]
?>"); ?></pre>
    </div>
    
    <div class="feature">
        <h3>HTTP Endpoint: POST /feature_4_sentiment.php?action=analyze</h3>
        <pre><?php echo htmlspecialchars("curl -X POST 'http://yoursite.com/api/feature_4_sentiment.php?action=analyze' \
  -d 'appointment_id=101&staff_id=2&review=Great service!'"); ?></pre>
    </div>

    <div class="feature">
        <h3>Batch Analysis: <code>analyzeFeedbackBatch(array \$reviews)</code></h3>
        <pre><?php echo htmlspecialchars("<?php
\$reviews = [
  ['appointment_id' => 1, 'review' => 'Good!'],
  ['appointment_id' => 2, 'review' => 'Not satisfied.']
];
\$batchResult = analyzeFeedbackBatch(\$reviews);
?>"); ?></pre>
    </div>
</div>

<div class="section">
    <h2>🔍 Health Check Endpoints</h2>
    <p>Verify that all ML services are running:</p>
    <pre><?php echo htmlspecialchars("curl http://localhost:5001/health   # Recommender
curl http://localhost:5002/health   # Chatbot
curl http://localhost:5003/health   # Forecast
curl http://localhost:5004/health   # Sentiment"); ?></pre>
</div>

<div class="section">
    <h2>⚠️ Important Notes</h2>
    <div class="warning">
        <strong>1. Python Services Must Be Running</strong>
        <br/>Start all 4 services before using PHP integration:
        <pre>cd ml_services && python start_all.py</pre>
    </div>
    
    <div class="warning">
        <strong>2. Data Files Required</strong>
        <br/>Ensure these CSV/JSON files exist in <code>ml_services/data/</code>:
        <ul>
            <li><code>faqs.json</code> — FAQ knowledge base (for chatbot)</li>
            <li><code>revenue.csv</code> — Historical revenue data (for forecast)</li>
            <li><code>customer_services.csv</code> — Customer-service matrix (for recommender)</li>
            <li><code>feedback.csv</code> — Initial feedback data (for sentiment training)</li>
        </ul>
    </div>

    <div class="warning">
        <strong>3. TODO: Database Implementation</strong>
        <br/>Each feature has TODO comments for database integration:
        <ul>
            <li>Feature 1: Implement <code>logChatSession()</code> to log chat history</li>
            <li>Feature 3: Implement monthly forecast dashboard query</li>
            <li>Feature 4: Implement <code>saveSentimentRecord()</code> and <code>flagForOwnerReview()</code></li>
        </ul>
    </div>
</div>

<div class="section">
    <h2>📊 Integration Examples</h2>
    
    <h3>Example 1: Show Chatbot on Booking Confirmation</h3>
    <pre><?php echo htmlspecialchars("<?php
require_once 'feature_1_chatbot.php';

if (\$_POST['chat_message']) {
    \$response = handleChatMessage(\$_POST['chat_message']);
    echo 'Bot: ' . \$response['answer'];
}
?>

<form method='POST'>
    <input name='chat_message' placeholder='Ask about our services...'>
    <button>Ask</button>
</form>"); ?></pre>

    <h3>Example 2: Show Recommendations After Service</h3>
    <pre><?php echo htmlspecialchars("<?php
require_once 'feature_2_recommender.php';

\$recommendations = getServiceRecommendations(\$customerId, 3);
echo 'Based on similar customers, you might like:';
echo formatRecommendationsForUI(\$recommendations);
?>"); ?></pre>

    <h3>Example 3: Revenue Forecast Dashboard</h3>
    <pre><?php echo htmlspecialchars("<?php
require_once 'feature_3_forecast.php';

\$forecast = getRevenueForecast(3);
echo formatForecastForDashboard(\$forecast);

\$accuracy = getForecastAccuracy();
echo 'Model Accuracy: ' . \$accuracy['mape_percent'] . '% MAPE';
?>"); ?></pre>

    <h3>Example 4: After Feedback Form Submission</h3>
    <pre><?php echo htmlspecialchars("<?php
require_once 'feature_4_sentiment.php';

if (\$_POST['review']) {
    \$sentiment = analyzeFeedback(
        \$appointmentId,
        \$staffId,
        \$_POST['review']
    );
    
    echo 'Customer sentiment: ' . 
         formatSentimentBadge(\$sentiment['label'], \$sentiment['score']);
}
?>"); ?></pre>
</div>

<div class="section">
    <h2>📞 Support</h2>
    <p>For issues:</p>
    <ul>
        <li>Check Python service logs: <code>python start_all.py</code></li>
        <li>Test endpoints manually with curl (see Health Check section)</li>
        <li>Verify CSV/JSON files exist in <code>ml_services/data/</code></li>
        <li>Check firewall: services run on ports 5001-5004</li>
    </ul>
</div>

</body>
</html>

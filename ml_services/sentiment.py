"""
sentiment.py — MC Prestige Salon Review Sentiment Analysis
Port: 5004
Algorithm A (Fast): VADER — rule-based, works without training data
Algorithm B (Deep Learning/Thesis): Multilingual BERT via HuggingFace
Note: Use multilingual model for Taglish (Filipino-English mixed) reviews.
"""

from vaderSentiment.vaderSentiment import SentimentIntensityAnalyzer
from flask import Flask, request, jsonify

app = Flask(__name__)
vader = SentimentIntensityAnalyzer()

def classify_sentiment(compound_score):
    if compound_score >= 0.05:
        return 'Positive'
    elif compound_score <= -0.05:
        return 'Negative'
    else:
        return 'Neutral'

@app.route('/analyze', methods=['POST'])
def analyze():
    data = request.get_json()
    review = data.get('review', '')
    appointment_id = data.get('appointment_id')
    staff_id = data.get('staff_id')

    if not review.strip():
        return jsonify({'error': 'Empty review text'}), 400

    scores = vader.polarity_scores(review)
    label = classify_sentiment(scores['compound'])

    return jsonify({
        'appointment_id': appointment_id,
        'staff_id': staff_id,
        'label': label,
        'score': round(scores['compound'], 3),
        'details': {
            'positive': round(scores['pos'], 3),
            'neutral': round(scores['neu'], 3),
            'negative': round(scores['neg'], 3)
        }
    })

@app.route('/analyze/batch', methods=['POST'])
def analyze_batch():
    """Analyze multiple reviews at once — for bulk import of existing feedback."""
    data = request.get_json()
    reviews = data.get('reviews', [])
    results = []

    for item in reviews:
        review = item.get('review', '')
        scores = vader.polarity_scores(review)
        results.append({
            'appointment_id': item.get('appointment_id'),
            'staff_id': item.get('staff_id'),
            'label': classify_sentiment(scores['compound']),
            'score': round(scores['compound'], 3)
        })

    summary = {
        'total': len(results),
        'positive': sum(1 for r in results if r['label'] == 'Positive'),
        'neutral': sum(1 for r in results if r['label'] == 'Neutral'),
        'negative': sum(1 for r in results if r['label'] == 'Negative'),
    }

    return jsonify({'results': results, 'summary': summary})

@app.route('/report/monthly', methods=['GET'])
def monthly_report():
    """
    Placeholder — in production, query your DB for this month's reviews
    and return aggregated sentiment stats for the owner dashboard.
    """
    return jsonify({
        'message': 'Connect this to your DB to return monthly sentiment summary',
        'expected_shape': {
            'month': '2026-01',
            'total_reviews': 42,
            'positive': 30,
            'neutral': 8,
            'negative': 4,
            'avg_score': 0.61,
            'flagged_for_review': ['appointment_id: 123', 'appointment_id: 87']
        }
    })

@app.route('/health', methods=['GET'])
def health():
    return jsonify({'status': 'ok', 'service': 'sentiment', 'engine': 'VADER'})

if __name__ == '__main__':
    app.run(port=5004, debug=False)

"""
recommender.py — MC Prestige Salon Service Recommender
Port: 5001
Algorithm: Cosine similarity on customer-service binary matrix (Collaborative Filtering)
Thesis upgrade: Replace with SVD/Matrix Factorization using sklearn.decomposition.TruncatedSVD
"""

import pandas as pd
from sklearn.metrics.pairwise import cosine_similarity
from flask import Flask, request, jsonify
import numpy as np

app = Flask(__name__)

# Load customer-service matrix (exported from Subsystem 3 SQL)
# Rows = customers, Columns = services, Values = 0 or 1
df = pd.read_csv('data/customer_services.csv', index_col='customer_id')
SERVICE_COLUMNS = df.columns.tolist()

# Precompute similarity matrix
similarity_matrix = cosine_similarity(df)
sim_df = pd.DataFrame(similarity_matrix, index=df.index, columns=df.index)

def get_recommendations(customer_id, top_n=3):
    """
    Returns top_n service recommendations for a given customer_id.
    Logic: Find the most similar customers → average their service usage 
    → recommend services the target customer hasn't used yet.
    """
    if customer_id not in sim_df.index:
        # New customer: recommend most popular services overall
        popularity = df.mean().sort_values(ascending=False)
        return popularity.head(top_n).index.tolist()

    # Find top 5 most similar customers (excluding self)
    similar_customers_scores = sim_df[customer_id].sort_values(ascending=False)[1:6]
    similar_customers = df.loc[similar_customers_scores.index]

    # Weight by similarity score
    weights = similar_customers_scores.values
    weighted_scores = similar_customers.T.dot(weights) / weights.sum()

    # Filter out services the customer already uses
    already_used = df.loc[customer_id]
    new_services = weighted_scores[already_used == 0]
    recommendations = new_services.sort_values(ascending=False).head(top_n)

    return recommendations.index.tolist()

@app.route('/recommend', methods=['POST'])
def recommend():
    data = request.get_json()
    customer_id = data.get('customer_id')
    top_n = data.get('top_n', 3)

    try:
        customer_id = int(customer_id)
    except (TypeError, ValueError):
        return jsonify({'error': 'Invalid customer_id'}), 400

    recommendations = get_recommendations(customer_id, top_n)
    return jsonify({
        'customer_id': customer_id,
        'recommendations': recommendations,
        'count': len(recommendations)
    })

@app.route('/popular', methods=['GET'])
def popular():
    """Fallback: returns most popular services overall (for new/unknown customers)."""
    popularity = df.mean().sort_values(ascending=False)
    return jsonify({
        'popular_services': popularity.head(5).index.tolist(),
        'uptake_rates': {k: round(v, 2) for k, v in popularity.head(5).items()}
    })

@app.route('/health', methods=['GET'])
def health():
    return jsonify({
        'status': 'ok',
        'service': 'recommender',
        'customers_in_matrix': len(df),
        'services_tracked': SERVICE_COLUMNS
    })

if __name__ == '__main__':
    app.run(port=5001, debug=False)

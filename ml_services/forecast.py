"""
forecast.py — MC Prestige Salon Revenue Forecasting
Port: 5003
Algorithm A (Simple): Facebook Prophet — time series with seasonality
Algorithm B (Deep Learning/Thesis): LSTM via TensorFlow — use for thesis chapter
Real data: Jul–Dec 2025 (6 months). Use Dec 2025 as test set, withhold during training.
MAPE target: ≤15% error
"""

import pandas as pd
import numpy as np
from prophet import Prophet
from flask import Flask, jsonify, request

app = Flask(__name__)

# --- Load and prepare data ---
df = pd.read_csv('data/revenue.csv')
df.columns = ['ds', 'y']           # Prophet requires exactly these column names
df['ds'] = pd.to_datetime(df['ds'])

# Split: train on Jul–Nov, test on Dec
train_df = df[df['ds'] < '2025-12-01']
test_df = df[df['ds'] >= '2025-12-01']

# --- Train Prophet model ---
model = Prophet(
    yearly_seasonality=False,   # Too little data for yearly patterns
    weekly_seasonality=True,    # Salons have weekly patterns (weekends busier)
    daily_seasonality=False,
    changepoint_prior_scale=0.3  # Allow some trend flexibility
)
model.fit(train_df)

def compute_mape(actual, predicted):
    """Mean Absolute Percentage Error."""
    actual, predicted = np.array(actual), np.array(predicted)
    mask = actual != 0
    return round(np.mean(np.abs((actual[mask] - predicted[mask]) / actual[mask])) * 100, 2)

@app.route('/forecast', methods=['GET'])
def forecast():
    periods = request.args.get('periods', 3, type=int)

    future = model.make_future_dataframe(periods=periods, freq='MS')  # Monthly
    prediction = model.predict(future)

    result = prediction[['ds', 'yhat', 'yhat_lower', 'yhat_upper']].tail(periods)
    result['ds'] = result['ds'].dt.strftime('%Y-%m-%d')
    result['yhat'] = result['yhat'].round(2)
    result['yhat_lower'] = result['yhat_lower'].round(2)
    result['yhat_upper'] = result['yhat_upper'].round(2)

    return jsonify(result.to_dict(orient='records'))

@app.route('/accuracy', methods=['GET'])
def accuracy():
    """
    Returns model accuracy by comparing Dec 2025 prediction vs actual.
    Use this in your thesis to prove the model works on real data.
    """
    future = model.make_future_dataframe(periods=1, freq='MS')
    prediction = model.predict(future)
    dec_pred = prediction[prediction['ds'] >= '2025-12-01']['yhat'].values[0]
    dec_actual = test_df['y'].sum()  # Actual December 2025 revenue

    mape = compute_mape([dec_actual], [dec_pred])

    return jsonify({
        'month': 'December 2025',
        'actual_revenue': round(dec_actual, 2),
        'predicted_revenue': round(dec_pred, 2),
        'mape_percent': mape,
        'within_target': mape <= 15.0
    })

@app.route('/historical', methods=['GET'])
def historical():
    """Returns all historical data + in-sample predictions for dashboard chart."""
    prediction = model.predict(model.make_future_dataframe(periods=0))
    result = []
    for _, row in df.iterrows():
        pred_row = prediction[prediction['ds'] == row['ds']]
        result.append({
            'date': row['ds'].strftime('%Y-%m'),
            'actual': round(row['y'], 2),
            'predicted': round(pred_row['yhat'].values[0], 2) if len(pred_row) else None
        })
    return jsonify(result)

@app.route('/health', methods=['GET'])
def health():
    return jsonify({
        'status': 'ok',
        'service': 'forecast',
        'training_rows': len(train_df),
        'date_range': f"{df['ds'].min().strftime('%Y-%m-%d')} to {df['ds'].max().strftime('%Y-%m-%d')}"
    })

if __name__ == '__main__':
    app.run(port=5003, debug=False)

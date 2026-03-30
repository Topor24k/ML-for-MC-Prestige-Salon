"""
start_all.py — Starts all 4 ML services simultaneously.
Run: python start_all.py
Each service runs on its own port.
"""

import subprocess
import sys
import time

services = [
    {'file': 'recommender.py', 'port': 5001, 'name': 'Service Recommender'},
    {'file': 'chatbot.py',     'port': 5002, 'name': 'Customer Help Guide'},
    {'file': 'forecast.py',    'port': 5003, 'name': 'Revenue Forecasting'},
    {'file': 'sentiment.py',   'port': 5004, 'name': 'Sentiment Analysis'},
]

procs = []
for svc in services:
    print(f"Starting {svc['name']} on port {svc['port']}...")
    p = subprocess.Popen([sys.executable, svc['file']])
    procs.append(p)
    time.sleep(1)  # Stagger startup

print("\n✅ All services running. Press Ctrl+C to stop all.\n")
try:
    for p in procs:
        p.wait()
except KeyboardInterrupt:
    print("\n\nShutting down all services...")
    for p in procs:
        p.terminate()
    print("✅ All services stopped.")

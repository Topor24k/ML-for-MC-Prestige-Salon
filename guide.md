# MC Prestige Salon ML Project - What Is Still Lacking

This is the only markdown file kept in the workspace.

## 1. Production Readiness Gaps (High Priority)

- Python runtime version alignment:
  - Current environment is Python 3.14, which can break some ML packages (especially TensorFlow and some pinned versions).
  - Recommended: lock to Python 3.10 or 3.11 in a dedicated project environment.

- Forecast API JSON bug:
  - `/accuracy` in `ml_services/forecast.py` can throw JSON serialization error (`numpy.bool_` not serializable).
  - Fix needed: cast values to native Python types before `jsonify`.

- No process manager for ML services:
  - `start_all.py` is fine for development but not for production uptime.
  - Needed: run with a process manager (PM2/supervisor/systemd/windows service) and restart policy.

- Development servers are still used:
  - Flask and PHP built-in servers are for development only.
  - Needed: production deployment stack (Gunicorn/Waitress + reverse proxy, hardened PHP web server).

## 2. Data and Database Integration Gaps (High Priority)

- PHP integration still has placeholder DB functions:
  - `saveSentimentRecord()`
  - `flagForOwnerReview()`
  - `logChatSession()`
  - `getMonthlySentimentReport()`
  - These must be connected to real tables.

- Missing finalized DB schema for ML outputs:
  - Needed tables at minimum:
    - `chat_logs`
    - `sentiment_results`
    - `owner_flags`
    - `recommendation_events` (impression/click/accepted)
    - `forecast_runs` (prediction snapshots + model metrics)

- Data refresh jobs not automated:
  - Revenue CSV export and retraining are not yet scheduled.
  - Needed: cron/task scheduler jobs for export + retrain + health checks.

## 3. Chatbot Intelligence Gaps (Medium Priority)

- Service catalog is now integrated, but intent coverage can still be improved:
  - More Taglish/Visaya variations
  - More typo tolerance
  - Better synonym handling (`hm`, `how much`, `magkano`, etc.)

- No conversation memory/session context yet:
  - Bot answers one-turn queries only.
  - Needed if you want multi-turn smart behavior (for example: "how about long hair?").

- No confidence/escalation policy in PHP layer yet:
  - Needed routing rule to transfer low-confidence queries to staff chat.

## 4. Recommender Gaps (Medium Priority)

- Current recommender is memory-based collaborative filtering only.
  - Good for MVP, but limited for sparse/new data.

- No explicit retrain trigger pipeline in place.
  - Needed: retrain after new completed appointments or on schedule.

- No business filters yet:
  - Availability filter (only recommend services currently offered)
  - Price-aware filter (customer budget)
  - Staff specialization filter

## 5. Forecasting Gaps (Medium Priority)

- Dataset is still small for robust forecasting.
  - More months of data will improve confidence.

- LSTM path is not fully production-ready in current environment.
  - Needs compatible Python version and TensorFlow verification.

- No model version tracking:
  - Needed: log model version, train window, and metrics per run.

## 6. Sentiment Analysis Gaps (Medium Priority)

- VADER is running, but domain adaptation is not done.
  - Salon-specific phrases and Taglish nuance can still be improved.

- No human-review workflow yet:
  - Negative/critical feedback should have assigned owner/staff action and resolution SLA.

- No trend dashboard yet:
  - Needed monthly trend charts by staff, service, and sentiment category.

## 7. Security and Compliance Gaps (High Priority)

- No authentication between PHP and ML endpoints.
  - Needed: API key or internal network restriction.

- No rate limiting.
  - Needed to prevent abuse of chatbot/sentiment endpoints.

- Input validation and logging policy not fully hardened.
  - Needed: strict validation, safe error messages, PII-safe logs.

- No backup/recovery procedure documented for ML data files and outputs.

## 8. QA and Testing Gaps (High Priority)

- No automated test suite committed for CI.
  - Needed tests:
    - Endpoint contract tests
    - Service startup tests
    - Regression tests for pricing answers
    - Integration tests (PHP -> Python)

- No acceptance test checklist for business users (owners/staff).

## 9. Deployment and Operations Gaps (High Priority)

- No `.env`-based centralized configuration yet.
  - Needed variables for ports, URLs, keys, paths, thresholds.

- No monitoring/alerting setup.
  - Needed:
    - health check monitor
    - uptime alerts
    - error alerts

- No structured logs aggregation.
  - Needed for debugging and audit trail.

## 10. Product/UX Gaps (Medium Priority)

- Dashboard exists for testing, but customer-facing production UI is not finalized.

- No owner KPI dashboard yet for:
  - chatbot resolution rate
  - recommendation acceptance rate
  - forecast error trend (MAPE)
  - sentiment trend and flagged issues

## Suggested Next Order of Work

1. Fix `/accuracy` JSON type issue and lock Python version to 3.10/3.11.
2. Implement real DB writes for all PHP placeholder functions.
3. Add auth + rate limit for ML endpoints.
4. Add automated jobs for exports/retrain/health checks.
5. Build KPI dashboard for owners.
6. Add automated regression tests and CI.

---

Project status summary: core MVP works, but production hardening, data plumbing, and operations readiness are still the main missing pieces.

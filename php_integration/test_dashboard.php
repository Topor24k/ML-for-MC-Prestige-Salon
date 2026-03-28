<?php
// Test Dashboard for all 4 ML features
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MC Prestige Salon ML Test Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f2efe9;
            --card: #fffdf8;
            --ink: #1f2623;
            --muted: #5a6660;
            --line: #d7d3ca;
            --brand: #0f766e;
            --brand-2: #d97706;
            --ok: #0f766e;
            --warn: #b45309;
            --err: #b91c1c;
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            --radius: 18px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Manrope", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 15% 20%, rgba(15, 118, 110, 0.16), transparent 40%),
                radial-gradient(circle at 85% 10%, rgba(217, 119, 6, 0.14), transparent 35%),
                linear-gradient(180deg, #f8f5ef 0%, #efe9df 100%);
            min-height: 100vh;
        }

        .wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 28px 16px 40px;
        }

        .hero {
            background: linear-gradient(130deg, #0f766e 0%, #115e59 45%, #7c2d12 100%);
            color: #f8fafc;
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            margin-bottom: 18px;
        }

        .hero::after {
            content: "";
            position: absolute;
            right: -40px;
            top: -40px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
        }

        h1, h2, h3 {
            font-family: "Space Grotesk", sans-serif;
            margin: 0;
        }

        h1 {
            font-size: clamp(1.45rem, 2.4vw, 2rem);
        }

        .sub {
            margin-top: 8px;
            opacity: 0.92;
            max-width: 780px;
            font-size: 0.98rem;
            line-height: 1.45;
        }

        .toolbar {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 10px;
            margin-top: 16px;
        }

        .endpoint {
            width: 100%;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 14px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 16px;
            animation: rise 0.45s ease;
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card h2 {
            font-size: 1.05rem;
            margin-bottom: 4px;
        }

        .meta {
            color: var(--muted);
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        label {
            display: block;
            font-size: 0.82rem;
            font-weight: 700;
            margin: 8px 0 4px;
            color: #2f3a35;
        }

        input, textarea, select, button {
            font: inherit;
        }

        input, textarea, select {
            width: 100%;
            background: #ffffff;
            border: 1px solid #cfd4cf;
            border-radius: 11px;
            padding: 10px 11px;
            color: var(--ink);
        }

        textarea {
            min-height: 84px;
            resize: vertical;
        }

        button {
            border: 0;
            border-radius: 11px;
            padding: 10px 13px;
            font-weight: 700;
            color: #fff;
            background: var(--brand);
            cursor: pointer;
            transition: transform 0.15s ease, filter 0.15s ease;
        }

        button.alt {
            background: var(--brand-2);
        }

        button.ghost {
            background: #334155;
        }

        button:hover {
            filter: brightness(1.04);
            transform: translateY(-1px);
        }

        .status-list {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-top: 14px;
        }

        .pill {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: #f8f7f2;
            padding: 7px 10px;
            font-size: 0.85rem;
            color: #1f2a24;
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #9ca3af;
            margin-left: 8px;
        }

        .dot.ok { background: var(--ok); }
        .dot.err { background: var(--err); }

        .out {
            margin-top: 10px;
            background: #0f172a;
            color: #d1fae5;
            border-radius: 12px;
            min-height: 92px;
            max-height: 260px;
            overflow: auto;
            padding: 10px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 0.84rem;
            white-space: pre-wrap;
        }

        .hint {
            margin-top: 12px;
            color: #394540;
            font-size: 0.87rem;
        }

        .fade {
            animation: fade 0.35s ease;
        }

        @keyframes fade {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-width: 960px) {
            .grid {
                grid-template-columns: 1fr;
            }
            .status-list {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .toolbar {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 560px) {
            .row {
                grid-template-columns: 1fr;
            }
            .status-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="wrap">
    <section class="hero">
        <h1>MC Prestige Salon ML Test Dashboard</h1>
        <p class="sub">
            Use this page to test all 4 machine learning features: Chatbot, Recommender, Revenue Forecast, and Sentiment Analysis.
            Start the ML services first, then use the forms below.
        </p>
        <div class="toolbar">
            <input id="baseUrl" class="endpoint" type="text" value="http://localhost:8000/php_integration" />
            <button class="ghost" id="checkAllBtn">Check All Health</button>
            <button class="alt" id="clearBtn">Clear Outputs</button>
        </div>
        <div class="status-list">
            <div class="pill">Feature 1 Chatbot <span class="dot" id="dot1"></span></div>
            <div class="pill">Feature 2 Recommender <span class="dot" id="dot2"></span></div>
            <div class="pill">Feature 3 Forecast <span class="dot" id="dot3"></span></div>
            <div class="pill">Feature 4 Sentiment <span class="dot" id="dot4"></span></div>
        </div>
    </section>

    <section class="grid">
        <article class="card fade">
            <h2>Feature 1: Customer Help Guide</h2>
            <p class="meta">Ask a customer question and get chatbot answer.</p>

            <label for="chatMessage">Message</label>
            <textarea id="chatMessage">Magkano ang rebond?</textarea>

            <div class="row">
                <button id="chatBtn">Send to Chatbot</button>
                <button class="alt" id="chatHealthBtn">Check Health</button>
            </div>

            <div class="out" id="out1">Waiting for test...</div>
        </article>

        <article class="card fade">
            <h2>Feature 2: Service Recommender</h2>
            <p class="meta">Get recommended services for a customer ID.</p>

            <div class="row">
                <div>
                    <label for="custId">Customer ID</label>
                    <input id="custId" type="number" value="1" min="1" />
                </div>
                <div>
                    <label for="topN">Top N</label>
                    <input id="topN" type="number" value="3" min="1" max="10" />
                </div>
            </div>

            <div class="row">
                <button id="recoBtn">Get Recommendations</button>
                <button class="alt" id="recoHealthBtn">Check Health</button>
            </div>

            <div class="out" id="out2">Waiting for test...</div>
        </article>

        <article class="card fade">
            <h2>Feature 3: Revenue Forecasting</h2>
            <p class="meta">Forecast future revenue and check model accuracy.</p>

            <div class="row">
                <div>
                    <label for="months">Forecast Months</label>
                    <input id="months" type="number" value="3" min="1" max="12" />
                </div>
                <div>
                    <label>&nbsp;</label>
                    <button class="alt" id="accBtn">Check Accuracy</button>
                </div>
            </div>

            <div class="row">
                <button id="forecastBtn">Get Forecast</button>
                <button class="alt" id="forecastHealthBtn">Check Health</button>
            </div>

            <div class="out" id="out3">Waiting for test...</div>
        </article>

        <article class="card fade">
            <h2>Feature 4: Sentiment Analysis</h2>
            <p class="meta">Classify review text as Positive, Neutral, or Negative.</p>

            <label for="review">Review</label>
            <textarea id="review">Super happy with my hair! Sobrang ganda ng result.</textarea>

            <div class="row">
                <div>
                    <label for="apptId">Appointment ID</label>
                    <input id="apptId" type="number" value="101" min="1" />
                </div>
                <div>
                    <label for="staffId">Staff ID</label>
                    <input id="staffId" type="number" value="2" min="1" />
                </div>
            </div>

            <div class="row">
                <button id="sentBtn">Analyze Review</button>
                <button class="alt" id="sentHealthBtn">Check Health</button>
            </div>

            <div class="out" id="out4">Waiting for test...</div>
        </article>
    </section>

    <p class="hint">
        Tip: keep this tab open while your ML services run. If a test fails, click Check All Health first.
    </p>
</div>

<script>
    function base() {
        return document.getElementById("baseUrl").value.replace(/\/$/, "");
    }

    function setOutput(id, data) {
        document.getElementById(id).textContent =
            typeof data === "string" ? data : JSON.stringify(data, null, 2);
    }

    function setDot(dotId, ok) {
        const dot = document.getElementById(dotId);
        dot.classList.remove("ok", "err");
        dot.classList.add(ok ? "ok" : "err");
    }

    async function req(url, options) {
        const res = await fetch(url, options);
        const text = await res.text();
        try {
            return JSON.parse(text);
        } catch {
            return { raw: text, status: res.status };
        }
    }

    async function checkHealthAll() {
        const b = base();
        const map = [
            { file: "feature_1_chatbot.php", dot: "dot1", out: "out1" },
            { file: "feature_2_recommender.php", dot: "dot2", out: "out2" },
            { file: "feature_3_forecast.php", dot: "dot3", out: "out3" },
            { file: "feature_4_sentiment.php", dot: "dot4", out: "out4" }
        ];

        for (const item of map) {
            try {
                const data = await req(`${b}/${item.file}?action=health`);
                const ok = data && (data.status === "ok");
                setDot(item.dot, ok);
                setOutput(item.out, data);
            } catch (err) {
                setDot(item.dot, false);
                setOutput(item.out, `Health check failed: ${err.message}`);
            }
        }
    }

    document.getElementById("chatBtn").addEventListener("click", async () => {
        const b = base();
        const message = document.getElementById("chatMessage").value;
        try {
            const body = new URLSearchParams({ message });
            const data = await req(`${b}/feature_1_chatbot.php`, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body
            });
            setOutput("out1", data);
            setDot("dot1", !data.error);
        } catch (err) {
            setOutput("out1", `Chat test failed: ${err.message}`);
            setDot("dot1", false);
        }
    });

    document.getElementById("chatHealthBtn").addEventListener("click", async () => {
        const b = base();
        try {
            const data = await req(`${b}/feature_1_chatbot.php?action=health`);
            setOutput("out1", data);
            setDot("dot1", data.status === "ok");
        } catch (err) {
            setOutput("out1", `Health check failed: ${err.message}`);
            setDot("dot1", false);
        }
    });

    document.getElementById("recoBtn").addEventListener("click", async () => {
        const b = base();
        const customer_id = document.getElementById("custId").value;
        const top_n = document.getElementById("topN").value;
        try {
            const body = new URLSearchParams({ customer_id, top_n });
            const data = await req(`${b}/feature_2_recommender.php`, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body
            });
            setOutput("out2", data);
            setDot("dot2", !data.error);
        } catch (err) {
            setOutput("out2", `Recommendation test failed: ${err.message}`);
            setDot("dot2", false);
        }
    });

    document.getElementById("recoHealthBtn").addEventListener("click", async () => {
        const b = base();
        try {
            const data = await req(`${b}/feature_2_recommender.php?action=health`);
            setOutput("out2", data);
            setDot("dot2", data.status === "ok");
        } catch (err) {
            setOutput("out2", `Health check failed: ${err.message}`);
            setDot("dot2", false);
        }
    });

    document.getElementById("forecastBtn").addEventListener("click", async () => {
        const b = base();
        const months = document.getElementById("months").value;
        try {
            const data = await req(`${b}/feature_3_forecast.php?action=forecast&months=${encodeURIComponent(months)}`);
            setOutput("out3", data);
            setDot("dot3", !data.error);
        } catch (err) {
            setOutput("out3", `Forecast test failed: ${err.message}`);
            setDot("dot3", false);
        }
    });

    document.getElementById("accBtn").addEventListener("click", async () => {
        const b = base();
        try {
            const data = await req(`${b}/feature_3_forecast.php?action=accuracy`);
            setOutput("out3", data);
            setDot("dot3", !data.error);
        } catch (err) {
            setOutput("out3", `Accuracy test failed: ${err.message}`);
            setDot("dot3", false);
        }
    });

    document.getElementById("forecastHealthBtn").addEventListener("click", async () => {
        const b = base();
        try {
            const data = await req(`${b}/feature_3_forecast.php?action=health`);
            setOutput("out3", data);
            setDot("dot3", data.status === "ok");
        } catch (err) {
            setOutput("out3", `Health check failed: ${err.message}`);
            setDot("dot3", false);
        }
    });

    document.getElementById("sentBtn").addEventListener("click", async () => {
        const b = base();
        const appointment_id = document.getElementById("apptId").value;
        const staff_id = document.getElementById("staffId").value;
        const review = document.getElementById("review").value;
        try {
            const body = new URLSearchParams({ appointment_id, staff_id, review });
            const data = await req(`${b}/feature_4_sentiment.php?action=analyze`, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body
            });
            setOutput("out4", data);
            setDot("dot4", !data.error);
        } catch (err) {
            setOutput("out4", `Sentiment test failed: ${err.message}`);
            setDot("dot4", false);
        }
    });

    document.getElementById("sentHealthBtn").addEventListener("click", async () => {
        const b = base();
        try {
            const data = await req(`${b}/feature_4_sentiment.php?action=health`);
            setOutput("out4", data);
            setDot("dot4", data.status === "ok");
        } catch (err) {
            setOutput("out4", `Health check failed: ${err.message}`);
            setDot("dot4", false);
        }
    });

    document.getElementById("clearBtn").addEventListener("click", () => {
        setOutput("out1", "Waiting for test...");
        setOutput("out2", "Waiting for test...");
        setOutput("out3", "Waiting for test...");
        setOutput("out4", "Waiting for test...");
        setDot("dot1", false);
        setDot("dot2", false);
        setDot("dot3", false);
        setDot("dot4", false);
    });

    document.getElementById("checkAllBtn").addEventListener("click", checkHealthAll);

    checkHealthAll();
</script>
</body>
</html>

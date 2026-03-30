"""
chatbot.py — MC Prestige Salon Customer Help Guide
Port: 5002
Algorithm: Sentence-BERT semantic similarity against FAQ knowledge base
Handles: Filipino-English (Taglish) questions reasonably well
"""

from sentence_transformers import SentenceTransformer, util
from flask import Flask, request, jsonify
import json
import re

app = Flask(__name__)

# Load multilingual model — handles Taglish better than English-only
model = SentenceTransformer('paraphrase-multilingual-MiniLM-L12-v2')

# Load FAQ knowledge base
with open('data/faqs.json', encoding='utf-8') as f:
    faqs = json.load(f)

# Load full service catalog for deterministic pricing answers
with open('data/services_catalog.json', encoding='utf-8') as f:
    catalog = json.load(f)

questions = [faq['question'] for faq in faqs]
question_embeddings = model.encode(questions, convert_to_tensor=True)


def normalize_text(text):
    normalized = text.lower()
    normalized = normalized.replace("'", "")
    normalized = re.sub(r'[^a-z0-9\s+/-]', ' ', normalized)
    normalized = re.sub(r'\s+', ' ', normalized).strip()
    return normalized


def detect_length(query):
    q = normalize_text(query)
    if any(token in q for token in ["short", "maikli", "short hair"]):
        return "short"
    if any(token in q for token in ["medium", "katamtaman", "mid", "medium hair"]):
        return "medium"
    if any(token in q for token in ["long", "mahaba", "long hair"]):
        return "long"
    return None


def flatten_catalog_items():
    items = []
    for section in catalog.get("sections", []):
        for group in section.get("groups", []):
            for item in group.get("items", []):
                item_copy = {
                    "section": section.get("name", ""),
                    "group": group.get("name", ""),
                    "name": item.get("name", ""),
                    "prices": item.get("prices", {}),
                    "aliases": item.get("aliases", [])
                }
                items.append(item_copy)
    return items


CATALOG_ITEMS = flatten_catalog_items()


def build_search_keys(item):
    keys = [item["name"]] + item.get("aliases", [])
    return [normalize_text(k) for k in keys if k]


def format_currency(amount):
    return f"PHP {amount:,}"


def format_price_line(name, prices):
    if set(prices.keys()) == {"regular"}:
        return f"{name}: {format_currency(prices['regular'])}"

    parts = []
    for key in ["short", "medium", "long"]:
        if key in prices:
            parts.append(f"{key.title()}: {format_currency(prices[key])}")
    return f"{name} -> " + " | ".join(parts)


def section_summary(section_id):
    section = next((s for s in catalog.get("sections", []) if s.get("id") == section_id), None)
    if not section:
        return None

    lines = [f"{section['name']}:"]
    for group in section.get("groups", []):
        lines.append(f"- {group.get('name', 'Items')}")
        for item in group.get("items", []):
            lines.append(f"  - {format_price_line(item['name'], item['prices'])}")

    lines.append(catalog.get("notice", ""))
    return "\n".join(lines)


def catalog_overview():
    names = [section.get("name", "") for section in catalog.get("sections", [])]
    return (
        "We offer these menus: "
        + ", ".join(names)
        + ". Ask me a specific service like 'Balayage medium price' or 'Rebond + Cystiene long'."
    )


def match_item(query):
    q = normalize_text(query)
    best_item = None
    best_len = 0

    for item in CATALOG_ITEMS:
        for key in build_search_keys(item):
            if key and key in q and len(key) > best_len:
                best_item = item
                best_len = len(key)

    return best_item


def catalog_response(user_input):
    q = normalize_text(user_input)

    # General catalog intents
    if any(token in q for token in ["all services", "list of services", "service list", "menu", "services offered", "anong services", "ano services"]):
        return {
            "answer": catalog_overview(),
            "confidence": 0.99,
            "resolved_by_bot": True,
            "source": "catalog_overview"
        }

    if any(token in q for token in ["hair services", "hair menu"]):
        response = section_summary("hair_services")
        if response:
            return {"answer": response, "confidence": 0.99, "resolved_by_bot": True, "source": "catalog_section"}

    if any(token in q for token in ["nail services", "nail menu"]):
        response = section_summary("nail_services")
        if response:
            return {"answer": response, "confidence": 0.99, "resolved_by_bot": True, "source": "catalog_section"}

    if any(token in q for token in ["promo color", "color promo", "promo packages color"]):
        response = section_summary("promo_color")
        if response:
            return {"answer": response, "confidence": 0.99, "resolved_by_bot": True, "source": "catalog_section"}

    if any(token in q for token in ["promo rebond", "rebond promo", "promo packages rebond"]):
        response = section_summary("promo_rebond")
        if response:
            return {"answer": response, "confidence": 0.99, "resolved_by_bot": True, "source": "catalog_section"}

    if any(token in q for token in ["add on", "add-ons", "addon"]):
        response = section_summary("promo_color")
        if response:
            return {"answer": response, "confidence": 0.95, "resolved_by_bot": True, "source": "catalog_section"}

    # Specific service intent
    matched = match_item(user_input)
    if not matched:
        return None

    prices = matched.get("prices", {})
    requested_length = detect_length(user_input)

    if requested_length and requested_length in prices:
        answer = (
            f"{matched['name']} ({requested_length.title()}) is {format_currency(prices[requested_length])}. "
            f"{catalog.get('notice', '')}"
        )
    elif "regular" in prices:
        answer = f"{matched['name']} is {format_currency(prices['regular'])}. {catalog.get('notice', '')}"
    else:
        answer = (
            f"{matched['name']} rates: "
            f"Short {format_currency(prices.get('short', 0))}, "
            f"Medium {format_currency(prices.get('medium', 0))}, "
            f"Long {format_currency(prices.get('long', 0))}. "
            f"{catalog.get('notice', '')}"
        )

    return {
        "answer": answer,
        "confidence": 0.99,
        "resolved_by_bot": True,
        "source": "catalog_match",
        "matched_service": matched["name"],
        "matched_group": matched["group"],
        "matched_section": matched["section"]
    }


def generate_response(user_input):
    # 1) Deterministic service catalog response (highest precision)
    catalog_hit = catalog_response(user_input)
    if catalog_hit:
        return catalog_hit

    # 2) Semantic FAQ fallback for general questions
    user_embedding = model.encode(user_input, convert_to_tensor=True)
    scores = util.cos_sim(user_embedding, question_embeddings)[0]
    best_idx = scores.argmax().item()
    confidence = round(scores[best_idx].item(), 3)

    if confidence >= 0.50:
        return {
            'answer': faqs[best_idx]['answer'],
            'matched_question': faqs[best_idx]['question'],
            'confidence': confidence,
            'resolved_by_bot': True,
            'source': 'faq_similarity'
        }

    return {
        'answer': "I can help with salon services, prices, packages, hours, booking, and location. Try asking like: 'Balayage long price' or 'Show nail services'.",
        'confidence': confidence,
        'resolved_by_bot': False,
        'source': 'fallback'
    }

@app.route('/chat', methods=['POST'])
def chat():
    data = request.get_json()
    user_input = data.get('message', '')

    if not user_input.strip():
        return jsonify({'answer': 'Please type your question.', 'confidence': 0})

    return jsonify(generate_response(user_input))

@app.route('/health', methods=['GET'])
def health():
    return jsonify({
        'status': 'ok',
        'service': 'chatbot',
        'faq_count': len(faqs),
        'catalog_items': len(CATALOG_ITEMS)
    })

if __name__ == '__main__':
    app.run(port=5002, debug=False)

#!/usr/bin/env bash
# Smoke test for the API. Usage: ./backend/tests/api-smoke.sh [base-url]
URL="${1:-http://localhost:8080}/api.php"

echo "1. GET default data (expect max_fed 12)"
curl -s "$URL" | grep -o '"max_fed":[0-9]*'

echo "2. POST custom stock (expect max_fed 2)"
curl -s -X POST "$URL" -H "Content-Type: application/json" \
  -d '{"ingredients":{"Cucumber":0,"Olives":0,"Lettuce":0,"Meat":4,"Tomato":0,"Cheese":0,"Dough":4}}' \
  | grep -o '"max_fed":[0-9]*'

echo "3. POST negative quantity (expect 400 and a Dough error)"
curl -s -w '\nHTTP %{http_code}\n' -X POST "$URL" -H "Content-Type: application/json" \
  -d '{"ingredients":{"Dough":-1}}'

echo "4. POST unknown ingredient (expect 400)"
curl -s -w '\nHTTP %{http_code}\n' -X POST "$URL" -H "Content-Type: application/json" \
  -d '{"ingredients":{"Chocolate":1}}'

echo "5. DELETE (expect 405)"
curl -s -w '\nHTTP %{http_code}\n' -X DELETE "$URL"
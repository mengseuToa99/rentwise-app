#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${BASE_URL:-http://127.0.0.1:8000}"
DURATION="${DURATION:-2m}"
VUS="${VUS:-200}"

if command -v k6 >/dev/null 2>&1; then
  echo "Running k6 load test: BASE_URL=${BASE_URL} VUS=${VUS} DURATION=${DURATION}"
  BASE_URL="${BASE_URL}" VUS="${VUS}" DURATION="${DURATION}" k6 run scripts/k6-load-test.js
  exit 0
fi

if command -v hey >/dev/null 2>&1; then
  echo "k6 not found. Falling back to hey..."
  hey -z "${DURATION}" -c "${VUS}" "${BASE_URL}/login"
  exit 0
fi

if command -v ab >/dev/null 2>&1; then
  echo "k6/hey not found. Falling back to ab..."
  ab -k -c "${VUS}" -n 20000 "${BASE_URL}/login"
  exit 0
fi

if command -v wrk >/dev/null 2>&1; then
  echo "k6/hey/ab not found. Falling back to wrk..."
  wrk -t 8 -c "${VUS}" -d "${DURATION}" "${BASE_URL}/login"
  exit 0
fi

echo "No load-test tool found. Install one of: k6, hey, ab, wrk"
exit 1

#!/usr/bin/env bash
cd "$(dirname "$0")"
# Activate virtualenv if present
if [ -f venv/bin/activate ]; then
  source venv/bin/activate
fi
# Start the FastAPI app
exec uvicorn app:app --host 0.0.0.0 --port 8000 --workers 1

## Context

We are migrating the backend APIs from PHP script execution to a persistent Python Flask application. This provides a unified Python ecosystem, simplifying the database handling and preparing the system to load ML model files in Phase 3.

## Goals / Non-Goals

**Goals:**
- Port database logic (SQLite3 connect, table creation, trigger setup, insert, query) from PHP to Python.
- Deploy a lightweight Python Flask web server.
- Automatically route the static frontend page from the Flask instance.
- Allow cross-origin requests (CORS) from local clients during migration.

**Non-Goals:**
- Developing, training, or loading any machine learning prediction models (Phase 3).

## Decisions

### 1. Flask Web Framework
We choose Python's Flask framework over Django or FastAPI.
* *Rationale*: Flask is a minimal micro-framework that requires very little boilerplate code. It allows us to serve the REST API endpoints and direct static assets in a single file (`app.py`), mimicking the simple script structure of Phase 1.
* *Alternatives considered*: FastAPI (requires extra libraries for static routing and setup), Django (too heavy for this micro-service).

### 2. Embedded Database Helper
The Flask server will handle database setup inside `app.py` upon startup.
* *Rationale*: Ensures that the database schema is verified/initialized automatically every time the server starts, preventing database-missing errors.

## Risks / Trade-offs

- **Python Dependency requirements**: Flask is not built into the Python standard library.
  * *Mitigation*: Ensure `flask` is installed via `pip install flask` before running the server.

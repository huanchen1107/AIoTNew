## Why

Migrate the backend infrastructure from PHP to Python Flask. This establishes a Python-based server environment, which is required to load and serve machine learning prediction models in Phase 3.

## What Changes

- Implement a Flask application in `app.py` that serves the SQLite3 database operations.
- Map the endpoint `addData.php` to `/api/add_data` (POST) and `getData.php` to `/api/get_data` (GET) inside Flask.
- Update `index.html` to automatically route requests to the Flask endpoints.
- Serve `index.html` as the default static homepage `/` of the Flask server.

## Capabilities

### New Capabilities
- `flask-backend-api`: Serves JSON-based REST APIs and handles frontend asset routing using Python Flask.

### Modified Capabilities
- `sensor-data-storage`: Migrates database access from PHP PDO to Python SQLite3, maintaining the same database schema (`aiotdb.db`) and timestamp trigger.

## Impact

- Adds `app.py` to the workspace.
- Requires Python package dependencies (`flask`).

## ADDED Requirements

### Requirement: Flask Home Page Route
The Flask application SHALL serve the dashboard `index.html` file (along with the phase-specific pages `index_phase2.html` and `index_phase3.html`) at the root URL `/`.

#### Scenario: User visits homepage
- **WHEN** a browser requests GET `/` from the Flask server
- **THEN** the server SHALL return `index.html` (or the corresponding phase-specific HTML file) with a HTTP 200 OK status

### Requirement: Flask Sensor Insertion Route
The Flask application SHALL expose a POST endpoint at `/api/add_data` that receives temperature and humidity readings and stores them in the database.

#### Scenario: Successful data POST
- **WHEN** a client POSTs a JSON payload `{"temperature": 23.5, "humidity": 60.1}` to `/api/add_data`
- **THEN** the server SHALL insert the reading into `aiotdb.db` and return HTTP 201 Created with JSON success payload

### Requirement: Flask Sensor Retrieval Route
The Flask application SHALL expose a GET endpoint at `/api/get_data` that retrieves the latest 100 records from the `sensors` table and returns them chronologically.

#### Scenario: Requesting sensor data history
- **WHEN** a client performs GET `/api/get_data`
- **THEN** the server SHALL return a HTTP 200 OK status containing a JSON list of the latest 100 database readings

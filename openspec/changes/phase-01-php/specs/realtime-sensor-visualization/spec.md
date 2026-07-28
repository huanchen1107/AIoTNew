## ADDED Requirements

### Requirement: Historical Data Retrieval API
The backend SHALL expose a `getData.php` endpoint that retrieves the latest 100 entries from the `sensors` table and returns them as a JSON list sorted chronologically by ID.

#### Scenario: Retrieving sensor history
- **WHEN** a client performs a GET request on `getData.php`
- **THEN** the system SHALL return a HTTP 200 response with a JSON array of the latest 100 sensor readings

### Requirement: Interactive Highcharts Dashboard
The dashboard `index.html` SHALL display a dual-axis Highcharts.js line chart mapping temperature and humidity trends.

#### Scenario: Rendering sensor data on load
- **WHEN** the dashboard page `index.html` loads in the browser
- **THEN** it SHALL fetch historical readings and render them on the chart

### Requirement: Mock Sensor Simulation
The dashboard SHALL include a simulation panel to allow manually submitting data or enabling automatic periodic submission.

#### Scenario: Auto simulation active
- **WHEN** the user enables the "Auto-simulate Readings" toggle on the UI
- **THEN** the browser SHALL submit a mock data reading to the active endpoint every 4 seconds and refresh the chart

## ADDED Requirements

### Requirement: Database Initialization and Schema
The system SHALL initialize an SQLite3 database named `aiotdb.db` containing a table named `sensors` with the following columns:
- `id` (INTEGER PRIMARY KEY AUTOINCREMENT)
- `humidity` (REAL, default 0.0)
- `temperature` (REAL, default 0.0)
- `time` (DATETIME DEFAULT CURRENT_TIMESTAMP)

#### Scenario: Database is missing on startup
- **WHEN** the addData.php script is executed and the database file `aiotdb.db` is missing
- **THEN** the system SHALL create the database file and the `sensors` table automatically

### Requirement: Automatic Timestamp Update Trigger
The database SHALL include an update trigger `update_sensor_time` that automatically updates the `time` column to the current timestamp whenever a row in the `sensors` table is updated.

#### Scenario: Updating an existing sensor reading
- **WHEN** an update query is run on a row in the `sensors` table
- **THEN** the trigger SHALL set the `time` column of that row to the current date and time

### Requirement: Storing Sensor Readings
The system SHALL accept temperature and humidity inputs and insert them into the `sensors` table.

#### Scenario: Successful data insertion
- **WHEN** a client POSTs or GETs a valid temperature and humidity reading to `addData.php`
- **THEN** the system SHALL insert the reading into the `sensors` table and return a success JSON response

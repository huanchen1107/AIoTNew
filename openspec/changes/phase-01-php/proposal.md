## Why

Introduce the initial foundation for the AIoT project by creating a lightweight sensor data collection and visualization dashboard. This enables manual and automatic simulation of temperature and humidity readings, stored locally in SQLite3, before migrating to a Flask backend in later phases.

## What Changes

- Create SQLite3 database `aiotdb.db` containing a `sensors` table with fields for ID, temperature, humidity, and timestamp.
- Implement a database trigger on the `sensors` table to automatically update the timestamp column upon any UPDATE query.
- Create `addData.php` to accept data parameters and insert them into the database.
- Create `getData.php` to query the database and output the latest 100 entries as JSON.
- Create a modern, responsive, and aesthetically rich `index_phase1.html` frontend dashboard (along with root landing page `index.html`) featuring Highcharts.js line charts and data simulation controls.
- Enforce cross-platform shell script line endings (`LF`) using a `.gitattributes` configuration.

## Capabilities

### New Capabilities
- `sensor-data-storage`: Handles auto-initialization of SQLite3 database, tables, and timestamp trigger for sensor tracking.
- `realtime-sensor-visualization`: Serves a dark-mode glassmorphic user interface displaying real-time trends using Highcharts.js.

### Modified Capabilities
<!-- None -->

## Impact

- Adds new PHP entrypoint scripts `addData.php` and `getData.php`.
- Adds the dashboard visualizers `index.html` and `index_phase1.html`, and `.gitattributes` to the project root.
- Initializes local database file `aiotdb.db`.

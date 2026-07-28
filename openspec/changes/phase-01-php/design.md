## Context

We are bootstrapping a lightweight, zero-configuration local server environment to collect, store, and visualize temperature and humidity data. The initial implementation must work out of the box using PHP and SQLite3, serving a high-fidelity frontend dashboard.

## Goals / Non-Goals

**Goals:**
- Design a relational SQLite3 table schema for storing sensor measurements (humidity, temperature, time).
- Create decoupled backend APIs in PHP for insert/select operations.
- Create a modern, dark-themed responsive frontend dashboard using Highcharts.js.
- Ensure that the dashboard functions as a controller, letting the user inject data or start automatic simulation loops.

**Non-Goals:**
- Creating a Python Flask backend (reserved for Phase 2).
- Integrating Machine Learning models (reserved for Phase 3).
- Connecting real hardware sensors (we will use mock simulation only).

## Decisions

### 1. SQLite3 Database File Storage
We choose SQLite3 (`aiotdb.db`) as our persistence layer.
* *Rationale*: Zero setup or server administration required. The database is represented as a single file in the workspace, making it highly portable.
* *Alternatives considered*: MySQL/PostgreSQL (overkill for this prototype, requires server setup).

### 2. Decoupled JSON API Architecture
The frontend (`index_phase1.html` and `index.html`) communicates with the backend scripts (`addData.php` and `getData.php`) exclusively via asynchronous HTTP requests (`Fetch API`) exchanging JSON.
* *Rationale*: This decoupling separates concerns and makes migrating to a Python Flask API in Phase 2 seamless—we will only need to redirect endpoints in the frontend rather than rebuilding the view.
* *Alternatives considered*: Server-side rendered PHP (would require major refactoring when moving to Flask).

### 3. Database Update Trigger
We implement a SQL trigger (`update_sensor_time`) to automatically update the timestamp column `time` upon any UPDATE statement.
* *Rationale*: Enforces data integrity at the database layer without requiring the application code to track or pass update timestamps.

## Risks / Trade-offs

- **SQLite Concurrent Access Limit**: SQLite locks the database file during write transactions, which can cause locks if multiple clients write concurrently.
  * *Mitigation*: Our dashboard runs locally with a single simulated sensor, so concurrency is not a bottleneck.
- **Cross-Platform Shell Compatibility**: Scripts run on Windows but are written in Bash (`.sh`), leading to potential CRLF line-ending syntax failures on checkout.
  * *Mitigation*: Enforce LF line endings globally for all shell scripts in `.gitattributes`.

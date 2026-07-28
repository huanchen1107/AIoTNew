## ADDED Requirements

### Requirement: Model Training Script
The system SHALL include a Python script `train_model.py` that loads a CSV file named `health_data.csv` containing columns for `temperature`, `humidity`, and `health_status`, trains a classification model, and saves it as `model.pkl`.

#### Scenario: Running model training
- **WHEN** the user executes `python train_model.py` and `health_data.csv` is present
- **THEN** the script SHALL successfully train a machine learning model and write `model.pkl` to the disk

### Requirement: Health Classification API Endpoint
The Flask application `/api/get_data` endpoint SHALL predict the health comfort status ("Safe", "Caution", "Danger") of each sensor record and return it in the JSON payload.

#### Scenario: Fetching classified readings
- **WHEN** GET `/api/get_data` is requested and `model.pkl` is loaded
- **THEN** the system SHALL return sensor records, each containing `health_status` and `health_description` fields

### Requirement: Frontend Comfort Status Badge
The dashboard `index.html` SHALL display a color-coded status badge and textual recommendation matching the classification returned by the backend.

#### Scenario: Displaying comfort warnings
- **WHEN** the latest reading has a `health_status` of "Danger"
- **THEN** the comfort status badge SHALL display "DANGER" styled in red with advice to stay hydrated and cool

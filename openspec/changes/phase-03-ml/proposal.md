## Why

Deploy machine learning (AI/ML) functionality to predict environmental safety or human comfort status based on temperature and humidity reading combinations. This fulfills the third and final phase of the AIoT project.

## What Changes

- Train a classification model (Decision Tree or similar classifier) on a custom CSV file containing historical combinations of temperature, humidity, and health status classes.
- Save the trained model to `model.pkl`.
- Update the Flask server in `app.py` to load `model.pkl` on startup and append the classification label ("Safe", "Caution", "Danger") to each sensor reading returned by `/api/get_data`.
- Update the dashboard `index.html` to display the real-time health status badge and corresponding comfort recommendations.

## Capabilities

### New Capabilities
- `health-status-classification`: Uses a trained scikit-learn machine learning model to classify temperature/humidity combinations.

### Modified Capabilities
- `flask-backend-api`: Augments JSON records retrieved from `/api/get_data` with predicted health status labels.
- `realtime-sensor-visualization`: Displays the predicted health status badge and descriptions on the dashboard.

## Impact

- Adds `train_model.py` and the serialized model `model.pkl` to the project structure.
- Modifies `app.py` and `index.html`.
- Requires Python ML libraries (`scikit-learn`, `pandas`).

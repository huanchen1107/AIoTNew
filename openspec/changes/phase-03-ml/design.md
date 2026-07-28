## Context

We are introducing machine learning classification to map combinations of temperature and humidity to comfort safety levels. This replaces static rules with a trained model that can adapt to custom datasets.

## Goals / Non-Goals

**Goals:**
- Implement a model training pipeline (`train_model.py`) that exports a serialized classifier.
- Load the classifier inside Flask (`app.py`) on server boot.
- Perform real-time inference on database records on fetch requests.
- Provide a robust rule-based fallback if `model.pkl` is missing.

**Non-Goals:**
- Hosting an independent ML microservice (model inference is done inline in Flask).

## Decisions

### 1. Model Choice: Decision Tree Classifier
We choose `scikit-learn`'s `DecisionTreeClassifier`.
* *Rationale*: Decision trees represent transparent, human-readable logic rules which align well with environmental indexes (like the Heat Index). They are extremely fast to execute during real-time requests and have low resource overhead.
* *Alternatives considered*: Support Vector Machines (SVM) or Neural Networks (overkill, slower to train, more complex parameter tuning).

### 2. Startup Fallback Logic
If `model.pkl` is not present, Flask will fallback to a static rule-based helper function (Heat Index classification) instead of failing to start.
* *Rationale*: Ensures high availability of the server before the training script is run.

## Risks / Trade-offs

- **Model Stale State**: If the training data changes, the model needs to be retrained and the server restarted.
  * *Mitigation*: The server will check for and load the model on startup. We can trigger training manually as needed.

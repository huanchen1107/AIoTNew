## 1. Model Development & Training

- [x] 1.1 Create mock `health_data.csv` training dataset
- [x] 1.2 Implement `train_model.py` to train a Decision Tree model and save it to `model.pkl`

## 2. Integration

- [x] 2.1 Update `app.py` to load `model.pkl` and output predicted status in `/api/get_data`
- [x] 2.2 Update `index.html` to dynamic health badge style and recommendations

## 3. Verification

- [x] 3.1 Run training script and check that model is generated
- [x] 3.2 Run Flask server and verify comfort badge matches predictions on simulated inputs

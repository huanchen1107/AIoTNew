# AIoT Temperature & Humidity Monitor (AIoTNew)

Welcome to **AIoTNew**! This is a modern, high-fidelity AIoT environmental tracking system. It persistence-tracks temperature and humidity combinations in an SQLite3 database and visualizes them on a glassmorphic dashboard with Highcharts.js.

🚀 **Live Demo**: [https://aiot0.vercel.app/](https://aiot0.vercel.app/) 

---

## 🛠️ Project Phases Summary

### 📂 Phase 1: PHP + SQLite3 + Highcharts.js
* **Backend**: Developed lightweight PHP scripts ([addData.php](addData.php) and [getData.php](getData.php)) to handle database writes and reads from a local SQLite3 database (`aiotdb.db`). 
* **Database**: Configured a `sensors` table with an automatic SQL trigger (`update_sensor_time`) that updates the time column on any update query.
* **Frontend**: Built [index.html](index.html), a modern dark-themed dashboard using Highcharts.js to plot real-time trends. Included a simulation panel to inject mock sensor readings manually or in an automatic interval loop.

### 📂 Phase 2: Python Flask Migration
* **Backend**: Replaced the PHP scripts with a persistent Python Flask server ([app.py](app.py)) exposing `/api/add_data` and `/api/get_data` endpoints.
* **Frontend Compatibility**: Configured Flask to serve `index.html` as the default static homepage. Added dynamic client-side `Content-Type` headers checking in `index.html` to automatically route requests to Flask if available, failing back to PHP scripts gracefully if hosted on a PHP server.
* **Hosting Configuration**: Prepared the project for Vercel deployment by adding [vercel.json](vercel.json) and [requirements.txt](requirements.txt), including dynamic database path routing to `/tmp/aiotdb.db` to prevent read-only filesystem errors.

### 📂 Phase 3: AI/ML Comfort Analysis
* **Dataset**: Created a training dataset ([health_data.csv](health_data.csv)) mapping temperature/humidity combinations to environmental comfort/safety classifications (`Safe`, `Caution`, `Danger`).
* **Training Pipeline**: Wrote [train_model.py](train_model.py) using `scikit-learn` to train a `DecisionTreeClassifier` and export it to a serialized binary `model.pkl`.
* **API & UI Integration**: Updated `app.py` to load the model on startup and predict the comfort safety level of each reading. Updated the frontend dashboard to display a real-time, color-coded health badge (Green for Safe, Yellow for Caution, Red for Danger) along with health recommendations.

---

## 📂 Project Structure

```text
├── .agents/              # Cursor / Gemini Agent customizations & skills
│   └── skills/grill-me/  # Stress-test review skill prompt
├── openspec/             # OpenSpec specifications
│   └── changes/          # OpenSpec changes specs (phase-01-php, phase-02-flask, phase-03-ml)
├── addData.php           # Phase 1: Insert sensor data (PHP)
├── getData.php           # Phase 1: Retrieve sensor history (PHP)
├── app.py                # Phase 2 & 3: Flask Web server & ML inference engine
├── train_model.py        # Phase 3: Classifier training pipeline
├── health_data.csv       # Phase 3: Classifier training dataset
├── model.pkl             # Phase 3: Exported ML model file (gitignored/local)
├── index.html            # Unified glassmorphic UI visualizer
├── vercel.json           # Vercel serverless deployment config
├── requirements.txt      # Python runtime dependencies
├── startup.sh / ending.sh# Session check / wrap-up scripts
└── .gitignore            # Workspace gitignore rules
```

---

## 🚀 Getting Started

### 1. Clone the Repository
```bash
git clone https://github.com/huanchen1107/AIoTNew.git
cd AIoTNew
```

### 2. Train the ML Model
Make sure Python is installed, then run the training pipeline to generate the model binary `model.pkl`:
```bash
pip install -r requirements.txt
python train_model.py
```

### 3. Run the Flask Web Application
Start the Flask dev server to host the APIs and frontend:
```bash
python app.py
```
Open **[http://127.0.0.1:5000/](http://127.0.0.1:5000/)** in your browser. Turn on **"Auto-simulate Readings"** to see live graphs plotting and the AI comfort badge changing in real time!


Last Updated: 2026-07-28 15:15:27

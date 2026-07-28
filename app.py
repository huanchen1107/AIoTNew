from flask import Flask, request, jsonify, send_from_directory
import sqlite3
import os
import pickle

app = Flask(__name__, static_folder='.', static_url_path='')
DB_FILE = 'aiotdb.db'

def init_db():
    """Initialize the SQLite database and create the schema and trigger if not exists."""
    conn = sqlite3.connect(DB_FILE)
    cursor = conn.cursor()
    
    # 1. Create table
    cursor.execute("""
    CREATE TABLE IF NOT EXISTS sensors (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        humidity REAL DEFAULT 0.0,
        temperature REAL DEFAULT 0.0,
        time DATETIME DEFAULT CURRENT_TIMESTAMP
    )
    """)
    
    # 2. Create trigger to update timestamp on UPDATE
    cursor.execute("""
    CREATE TRIGGER IF NOT EXISTS update_sensor_time 
    AFTER UPDATE ON sensors 
    BEGIN
        UPDATE sensors SET time = CURRENT_TIMESTAMP WHERE id = new.id;
    END;
    """)
    
    conn.commit()
    conn.close()

# Initialize DB on startup
init_db()

# Comfort status classification helper (fallback/default)
def calculate_health_status(temperature, humidity):
    """Fallback rule-based comfort calculator."""
    temp = float(temperature)
    hum = float(humidity)
    if temp > 35 or (temp > 32 and hum > 75):
        return "Danger", "Extreme environment detected! High threat of heat stress. Stay hydrated and use cooling systems."
    elif temp > 28 or hum > 70:
        return "Caution", "Elevated heat index or humidity. Limit strenuous outdoor work and take frequent breaks."
    else:
        return "Safe", "Comfortable and healthy indoor environmental conditions."

# Load pre-trained machine learning model
model = None
try:
    with open('model.pkl', 'rb') as f:
        model = pickle.load(f)
    print("AI ML Model loaded successfully from model.pkl.")
except Exception as e:
    print(f"Warning: Could not load model.pkl ({str(e)}). Running on fallback rule-based classification.")

@app.after_request
def add_cors_headers(response):
    """Enable CORS support manually to allow frontend calls from any origin if testing."""
    response.headers['Access-Control-Allow-Origin'] = '*'
    response.headers['Access-Control-Allow-Methods'] = 'GET, POST, OPTIONS'
    response.headers['Access-Control-Allow-Headers'] = 'Content-Type'
    return response

@app.route('/')
def index():
    """Serve the index.html file from the root directory."""
    return send_from_directory('.', 'index.html')

@app.route('/api/add_data', methods=['POST', 'OPTIONS'])
def add_data():
    if request.method == 'OPTIONS':
        return jsonify({"status": "success"}), 200
        
    # Read parameters from JSON body or Form data
    data = request.get_json(silent=True)
    if data:
        temperature = data.get('temperature')
        humidity = data.get('humidity')
    else:
        temperature = request.form.get('temperature')
        humidity = request.form.get('humidity')
        
    if temperature is None or humidity is None:
        return jsonify({
            "status": "error",
            "message": "Missing 'temperature' or 'humidity'."
        }), 400
        
    try:
        temp_val = float(temperature)
        hum_val = float(humidity)
    except ValueError:
        return jsonify({
            "status": "error",
            "message": "Parameters 'temperature' and 'humidity' must be numeric values."
        }), 400

    try:
        conn = sqlite3.connect(DB_FILE)
        cursor = conn.cursor()
        cursor.execute(
            "INSERT INTO sensors (temperature, humidity) VALUES (?, ?)",
            (temp_val, hum_val)
        )
        conn.commit()
        last_id = cursor.lastrowid
        conn.close()
        
        return jsonify({
            "status": "success",
            "message": "Data inserted successfully",
            "data": {
                "id": last_id,
                "temperature": temp_val,
                "humidity": hum_val
            }
        }), 201
    except Exception as e:
        return jsonify({
            "status": "error",
            "message": f"Database insertion failed: {str(e)}"
        }), 500

@app.route('/api/get_data', methods=['GET'])
def get_data():
    try:
        conn = sqlite3.connect(DB_FILE)
        conn.row_factory = sqlite3.Row  # to get columns by name
        cursor = conn.cursor()
        
        # Check if table exists
        cursor.execute("SELECT name FROM sqlite_master WHERE type='table' AND name='sensors'")
        if not cursor.fetchone():
            conn.close()
            return jsonify([]), 200
            
        # Get the latest 100 entries ordered chronologically
        cursor.execute("SELECT * FROM (SELECT id, temperature, humidity, time FROM sensors ORDER BY id DESC LIMIT 100) ORDER BY id ASC")
        rows = cursor.fetchall()
        conn.close()
        
        # Convert sqlite3.Row objects to list of dicts
        readings = []
        for r in rows:
            temp = float(r["temperature"])
            hum = float(r["humidity"])
            
            # Predict health status using ML model or fallback
            status = None
            description = None
            
            if model is not None:
                try:
                    pred = model.predict([[temp, hum]])[0]
                    status = str(pred)
                    
                    if status == "Danger":
                        description = "Predicted danger warning. Please check air conditioning and drink water."
                    elif status == "Caution":
                        description = "Predicted caution comfort level. Keep an eye on humidity and ventilation."
                    else:
                        description = "Predicted safe and healthy environmental conditions."
                except Exception as ex:
                    # Fallback on inference failure
                    status, description = calculate_health_status(temp, hum)
            else:
                status, description = calculate_health_status(temp, hum)

            readings.append({
                "id": r["id"],
                "temperature": temp,
                "humidity": hum,
                "time": r["time"],
                "health_status": status,
                "health_description": description
            })
            
        return jsonify(readings), 200
    except Exception as e:
        return jsonify({
            "status": "error",
            "message": f"Database retrieval failed: {str(e)}"
        }), 500

if __name__ == '__main__':
    # Print custom helper instruction to terminal
    print("==================================================")
    print("Flask Server Running: http://127.0.0.1:5000/")
    print("==================================================")
    app.run(host='127.0.0.1', port=5000, debug=True)

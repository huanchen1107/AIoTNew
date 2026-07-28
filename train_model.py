import pandas as pd
from sklearn.tree import DecisionTreeClassifier
import pickle
import os

def train():
    csv_file = 'health_data.csv'
    model_file = 'model.pkl'
    
    if not os.path.exists(csv_file):
        print(f"Error: {csv_file} not found.")
        return
        
    print(f"Loading training data from {csv_file}...")
    df = pd.read_csv(csv_file)
    
    # Check that required columns exist
    required_cols = {'temperature', 'humidity', 'health_status'}
    if not required_cols.issubset(df.columns):
        print(f"Error: CSV must contain columns: {required_cols}")
        return
        
    X = df[['temperature', 'humidity']].values
    y = df['health_status']
    
    print("Training Decision Tree Classifier model...")
    # Initialize and train Decision Tree model
    model = DecisionTreeClassifier(max_depth=4, random_state=42)
    model.fit(X, y)
    
    print(f"Saving trained model to {model_file}...")
    with open(model_file, 'wb') as f:
        pickle.dump(model, f)
        
    print("Model training completed successfully!")

if __name__ == '__main__':
    train()

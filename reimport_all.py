import requests
import json
import time

# Function to import data
def import_data(sheet_url, data_type):
    url = 'https://ap7affiliates-backend.onrender.com/api/raw-data/import-from-google-sheets'
    payload = {
        'sheet_url': sheet_url,
        'data_type': data_type
    }
    try:
        res = requests.post(url, json=payload, timeout=30)
        return res.status_code, res.json() if res.status_code == 200 else res.text
    except Exception as e:
        return None, str(e)

# Import registrations
print("Step 1: Importing registrations...")
reg_url = 'https://docs.google.com/spreadsheets/d/1obOiP3Szeg_MMANRprkFMUiRTkyXy3XSNaZUETNwxEc/edit?gid=1476902641#gid=1476902641'
status, result = import_data(reg_url, 'registration')
print(f"  Status: {status}")
if status == 200:
    print(f"  Imported: {result.get('imported_count', 0)} registrations")
    print(f"  Message: {result.get('message', '')}")
else:
    print(f"  Error: {result}")
print()

# Small delay
time.sleep(2)

# Import transactions
print("Step 2: Importing transactions...")
trans_url = 'https://docs.google.com/spreadsheets/d/1obOiP3Szeg_MMANRprkFMUiRTkyXy3XSNaZUETNwxEc/edit?gid=408856058#gid=408856058'
status, result = import_data(trans_url, 'transaction')
print(f"  Status: {status}")
if status == 200:
    print(f"  Imported: {result.get('imported_count', 0)} transactions")
else:
    print(f"  Error: {result}")
print()

# Small delay
time.sleep(2)

# Check dashboard
print("Step 3: Checking dashboard...")
try:
    res = requests.get('https://ap7affiliates-backend.onrender.com/api/dashboard')
    if res.status_code == 200:
        data = res.json()
        print(f"  Total Players: {data.get('total_players', 0)}")
        print(f"  Total Deposits: ₹{data.get('total_deposits', 0)}")
        print(f"  Total Withdrawals: ₹{data.get('total_withdrawals', 0)}")
        print(f"  Total Bonuses: ₹{data.get('total_bonuses', 0)}")
        print(f"  Total Revenue: ₹{data.get('total_revenue', 0)}")
        print(f"  Total Commission: ₹{data.get('total_commission', 0)}")
    else:
        print(f"  Error: {res.status_code} - {res.text}")
except Exception as e:
    print(f"  Error: {e}")

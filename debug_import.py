import requests
import time

print("Step 1: Import registrations...")
url = 'https://ap7affiliates-backend.onrender.com/api/raw-data/import-from-google-sheets'
payload = {
    'sheet_url': 'https://docs.google.com/spreadsheets/d/1obOiP3Szeg_MMANRprkFMUiRTkyXy3XSNaZUETNwxEc/edit?gid=1476902641#gid=1476902641',
    'data_type': 'registration'
}
res = requests.post(url, json=payload)
print(f"Response: {res.status_code}")
print(f"Body: {res.json()}")

time.sleep(2)

print("\nStep 2: Check raw-data after import...")
headers = {'Authorization': 'Bearer master-token-chris9742'}
res = requests.get('https://ap7affiliates-backend.onrender.com/api/raw-data', headers=headers)
print(f"Raw data records: {len(res.json())}")

time.sleep(2)

print("\nStep 3: Import transactions...")
payload['sheet_url'] = 'https://docs.google.com/spreadsheets/d/1obOiP3Szeg_MMANRprkFMUiRTkyXy3XSNaZUETNwxEc/edit?gid=408856058#gid=408856058'
payload['data_type'] = 'transaction'
res = requests.post(url, json=payload)
print(f"Response: {res.status_code}")
print(f"Body: {res.json()}")

time.sleep(2)

print("\nStep 4: Check raw-data after transaction import...")
res = requests.get('https://ap7affiliates-backend.onrender.com/api/raw-data', headers=headers)
records = res.json()
print(f"Raw data records: {len(records)}")
if records:
    print(f"First record: {records[0]}")

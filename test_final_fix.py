import requests
import time
import json

# Wait for Render to deploy
print("Waiting for Render to deploy (40 seconds)...")
for i in range(8):
    time.sleep(5)
    print(f"  {i*5} seconds...")

# Re-import data
print("\nRe-importing registrations...")
url = 'https://ap7affiliates-backend.onrender.com/api/raw-data/import-from-google-sheets'
payload = {
    'sheet_url': 'https://docs.google.com/spreadsheets/d/1obOiP3Szeg_MMANRprkFMUiRTkyXy3XSNaZUETNwxEc/edit?gid=1476902641#gid=1476902641',
    'data_type': 'registration'
}
res = requests.post(url, json=payload)
print(f"Status: {res.status_code}, Imported: {res.json().get('imported_count', 0)}")

time.sleep(2)

print("Re-importing transactions...")
payload['sheet_url'] = 'https://docs.google.com/spreadsheets/d/1obOiP3Szeg_MMANRprkFMUiRTkyXy3XSNaZUETNwxEc/edit?gid=408856058#gid=408856058'
payload['data_type'] = 'transaction'
res = requests.post(url, json=payload)
print(f"Status: {res.status_code}, Imported: {res.json().get('imported_count', 0)}")

time.sleep(2)

# Test Reports API with new fields
print("\n✅ Testing Reports API with new fields...")
url = 'https://ap7affiliates-backend.onrender.com/api/reports'
headers = {'Authorization': 'Bearer master-token-chris9742'}
res = requests.get(url, headers=headers)
data = res.json()

print(f"Total players: {len(data)}")

# Find player with transactions
player_with_data = next((r for r in data if float(r.get('deposit', 0)) > 0), None)

if player_with_data:
    print(f"\n✅ Sample Player (with deposits):")
    print(f"  Name: {player_with_data.get('player_username')}")
    print(f"  Registration Date: {player_with_data.get('registration_date')}")
    print(f"  FTD Date: {player_with_data.get('ftd_date')}")
    print(f"  First Deposit Amount: ₹{player_with_data.get('first_deposit_amount')}")
    print(f"  Deposit Count: {player_with_data.get('deposit_count')} transactions")
    print(f"  Total Deposits: ₹{player_with_data.get('deposit')}")
    print(f"  Total Withdrawals: ₹{player_with_data.get('withdrawal')}")
    print(f"  Total Bonus: ₹{player_with_data.get('bonus')}")
    print(f"  Revenue (dep - wd - bonus): ₹{player_with_data.get('revenue')}")
    print(f"  Commission: ₹{player_with_data.get('commission')}")

# Find player with multiple deposits
multi_deposit = next((r for r in data if int(r.get('deposit_count', 0)) > 1), None)
if multi_deposit:
    print(f"\n✅ Player with multiple deposits:")
    print(f"  Name: {multi_deposit.get('player_username')}")
    print(f"  Deposit Count: {multi_deposit.get('deposit_count')} times")
    print(f"  Total Deposits: ₹{multi_deposit.get('deposit')}")

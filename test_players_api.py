import requests
import time
import json

# Wait for Render to deploy
print("Waiting for Render to deploy (40 seconds)...")
for i in range(8):
    time.sleep(5)
    print(f"  {i*5} seconds...")

# Re-import data
print("\nRe-importing data...")
url = 'https://ap7affiliates-backend.onrender.com/api/raw-data/import-from-google-sheets'
payload = {
    'sheet_url': 'https://docs.google.com/spreadsheets/d/1obOiP3Szeg_MMANRprkFMUiRTkyXy3XSNaZUETNwxEc/edit?gid=1476902641#gid=1476902641',
    'data_type': 'registration'
}
res = requests.post(url, json=payload)
print(f"Registrations: {res.json().get('imported_count', 0)}")

time.sleep(2)

payload['sheet_url'] = 'https://docs.google.com/spreadsheets/d/1obOiP3Szeg_MMANRprkFMUiRTkyXy3XSNaZUETNwxEc/edit?gid=408856058#gid=408856058'
payload['data_type'] = 'transaction'
res = requests.post(url, json=payload)
print(f"Transactions: {res.json().get('imported_count', 0)}")

time.sleep(2)

# Test Players API (no filter)
print("\n✅ Testing Players API (all players)...")
url = 'https://ap7affiliates-backend.onrender.com/api/players'
res = requests.get(url)
players = res.json()
print(f"Total players: {len(players)}")

# Find player with data
player_with_data = next((p for p in players if float(p.get('deposit', 0)) > 0), None)
if player_with_data:
    print(f"\nSample player with data:")
    print(f"  Name: {player_with_data.get('player_username')}")
    print(f"  Deposits: ₹{player_with_data.get('deposit')}")
    print(f"  Deposit Count: {player_with_data.get('deposit_count')}")
    print(f"  Affiliate: {player_with_data.get('affiliate_id')}")

# Test Players API with affiliate filter
print(f"\n✅ Testing Players API with affiliate_id filter (AFF-1001)...")
url = 'https://ap7affiliates-backend.onrender.com/api/players?affiliate_id=AFF-1001'
res = requests.get(url)
filtered_players = res.json()
print(f"Players for AFF-1001: {len(filtered_players)}")

# All should be AFF-1001
for p in filtered_players[:3]:
    print(f"  - {p.get('player_username')}: {p.get('affiliate_id')}")

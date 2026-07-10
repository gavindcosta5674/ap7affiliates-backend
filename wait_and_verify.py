import requests
import time

# Wait 35 seconds for Render to fully deploy
print("Waiting for Render to deploy (35 seconds)...")
for i in range(7):
    print(f"  {i*5} seconds...")
    time.sleep(5)

# Test Reports API
print("\nTesting Reports API...")
url = 'https://ap7affiliates-backend.onrender.com/api/reports'
headers = {'Authorization': 'Bearer master-token-chris9742'}
res = requests.get(url, headers=headers)

data = res.json()

# Find first player with deposits
player_with_deposits = next((r for r in data if float(r.get('deposit', 0)) > 0), None)

if player_with_deposits:
    print(f"✅ SUCCESS! Reports now have transaction data!")
    print(f"\nPlayer: {player_with_deposits.get('player_username')}")
    print(f"  Deposits: ₹{player_with_deposits.get('deposit')}")
    print(f"  Withdrawals: ₹{player_with_deposits.get('withdrawal')}")
    print(f"  Bonuses: ₹{player_with_deposits.get('bonus')}")
    print(f"  Revenue: ₹{player_with_deposits.get('revenue')}")
    print(f"  FTD Date: {player_with_deposits.get('ftd_date')}")
    print(f"  Registration Date: {player_with_deposits.get('registration_date')}")
else:
    print(f"❌ Still showing 0 values. Render may not have deployed yet.")
    print(f"   Total reports: {len(data)}")

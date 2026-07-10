import requests
import time

# Small delay to let Render deploy
print("Waiting for Render to deploy...")
time.sleep(5)

# Test Reports API
url = 'https://ap7affiliates-backend.onrender.com/api/reports'
headers = {'Authorization': 'Bearer master-token-chris9742'}
res = requests.get(url, headers=headers)

print(f"Status: {res.status_code}")
data = res.json()

print(f"Total reports: {len(data)}")
print()

# Show first report with details
if data:
    r = data[0]
    print(f"First player: {r.get('player_username')}")
    print(f"  FTD Date: {r.get('ftd_date')}")
    print(f"  Registration Date: {r.get('registration_date')}")
    print(f"  Deposits: ₹{r.get('deposit')}")
    print(f"  Withdrawals: ₹{r.get('withdrawal')}")
    print(f"  Bonuses: ₹{r.get('bonus')}")
    print(f"  Revenue: ₹{r.get('revenue')}")
    print(f"  Commission: ₹{r.get('commission')}")
    print()
    
    # Find a player with deposits
    for r in data:
        if float(r.get('deposit', 0)) > 0:
            print(f"Player WITH transactions: {r.get('player_username')}")
            print(f"  Deposits: ₹{r.get('deposit')}")
            print(f"  Withdrawals: ₹{r.get('withdrawal')}")
            print(f"  Revenue: ₹{r.get('revenue')}")
            break

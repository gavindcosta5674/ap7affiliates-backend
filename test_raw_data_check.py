import requests

# Check raw-data endpoint
url = 'https://ap7affiliates-backend.onrender.com/api/raw-data'
headers = {'Authorization': 'Bearer master-token-chris9742'}
res = requests.get(url, headers=headers)

print(f"Status: {res.status_code}")
data = res.json()
print(f"Total raw_data records: {len(data)}")

if data:
    print(f"\nFirst record:")
    print(f"  Player: {data[0].get('player_username')}")
    print(f"  Affiliate: {data[0].get('affiliate_id')}")
    print(f"  Deposit: {data[0].get('deposit_amount')}")
else:
    print("❌ No data in raw_data_db!")

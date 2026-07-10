import requests
import json

# Get the raw-data to see what we have
url = 'https://ap7affiliates-backend.onrender.com/api/raw-data'
headers = {'Authorization': 'Bearer master-token-chris9742'}
res = requests.get(url, headers=headers)
data = res.json()

print(f"Total raw_data records: {len(data)}")
print()

# Count registration vs transaction records
reg_count = sum(1 for r in data if r.get('deposit_amount') == 0.0 and r.get('withdrawal_amount') == 0.0 and r.get('bonus_amount') == 0.0)
trans_count = sum(1 for r in data if r.get('deposit_amount') > 0 or r.get('withdrawal_amount') > 0 or r.get('bonus_amount') > 0)
zero_count = sum(1 for r in data if r.get('deposit_amount') == 0.0 and r.get('withdrawal_amount') == 0.0 and r.get('bonus_amount') == 0.0)

print(f"Records with all zeros: {zero_count}")
print(f"Records with amounts: {trans_count}")
print()

# Show some records with amounts
with_amounts = [r for r in data if r.get('deposit_amount') > 0 or r.get('withdrawal_amount') > 0 or r.get('bonus_amount') > 0]
if with_amounts:
    print(f"First record with amounts: {with_amounts[0]}")
else:
    print("NO records with amounts!")
    print()
    print("All records are like this:")
    print(json.dumps(data[0], indent=2))

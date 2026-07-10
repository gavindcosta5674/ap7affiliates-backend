import requests
import json

# Get reports
url = 'https://ap7affiliates-backend.onrender.com/api/reports'
headers = {'Authorization': 'Bearer master-token-chris9742'}
res = requests.get(url, headers=headers)

print(f"Status: {res.status_code}")
print()

data = res.json()
print(f"Total reports: {len(data)}")
print()

# Show first 3 reports with details
print("First 3 reports:")
for i, report in enumerate(data[:3]):
    print(f"\nReport {i+1}:")
    print(f"  Player: {report.get('player_username')}")
    print(f"  FTD Date: {report.get('ftd_date')}")
    print(f"  Registration Date: {report.get('registration_date')}")
    print(f"  Deposits: {report.get('deposit')}")
    print(f"  Withdrawals: {report.get('withdrawal')}")
    print(f"  Bonuses: {report.get('bonus')}")
    print(f"  Revenue: {report.get('revenue')}")
    print(f"  Commission: {report.get('commission')}")

# Count reports with actual transactions
with_amounts = [r for r in data if float(r.get('deposit', 0) or 0) > 0 or float(r.get('withdrawal', 0) or 0) > 0 or float(r.get('bonus', 0) or 0) > 0]
print(f"\nReports with transaction amounts: {len(with_amounts)}")
if with_amounts:
    print(f"First one with amounts: {with_amounts[0]}")

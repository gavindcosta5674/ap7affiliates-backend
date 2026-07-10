import requests
import csv
import io

# The transaction sheet URL
sheet_url = 'https://docs.google.com/spreadsheets/d/1obOiP3Szeg_MMANRprkFMUiRTkyXy3XSNaZUETNwxEc/edit?gid=408856058#gid=408856058'

# Convert to CSV export URL
import re
match = re.search(r'/spreadsheets/d/([a-zA-Z0-9-_]+)', sheet_url)
sheet_id = match.group(1)
gid_match = re.search(r'[#&]gid=([0-9]+)', sheet_url)
gid = gid_match.group(1) if gid_match else '0'

csv_url = f"https://docs.google.com/spreadsheets/d/{sheet_id}/export?format=csv&gid={gid}"
print(f"CSV URL: {csv_url}")
print()

# Fetch the CSV
try:
    response = requests.get(csv_url, timeout=10)
    response.raise_for_status()
    csv_text = response.text
    
    print("First 1000 characters of CSV:")
    print(csv_text[:1000])
    print()
    
    # Parse it to see what columns we have
    reader = csv.reader(io.StringIO(csv_text))
    headers = next(reader, None)
    print(f"Headers: {headers}")
    print()
    
    # Show first 5 data rows
    print("First 5 data rows:")
    for i, row in enumerate(reader):
        if i >= 5:
            break
        print(f"Row {i+1}: {row}")
        
except Exception as e:
    print(f"Error: {e}")

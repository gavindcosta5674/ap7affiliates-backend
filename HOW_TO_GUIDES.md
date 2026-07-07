# Affiliate Panel - How-To Guides

## 1. HOW TO UPLOAD RAW TRANSACTION DATA

### Step-by-Step Instructions:

#### **Step 1: Login to Admin Panel**
- Go to your Master Affiliate Management Panel
- Enter your username and password
- Click "Sign In"

#### **Step 2: Navigate to Daily Raw Data**
- Once logged in, look at the **left sidebar**
- Click on **"Daily Raw Data"** (you'll see an icon with an upload symbol)

#### **Step 3: Prepare Your Data**
Your data should be in one of these formats:
- **Tab-separated** (recommended)
- **Comma-separated (CSV)**

**Required Columns (in this order):**
1. DateTime (e.g., `27/06/2026 15:56:39`)
2. Deposit (numeric amount, e.g., `200`)
3. Withdraw (numeric amount, e.g., `50`)
4. Bonus (numeric amount, e.g., `10`)
5-6. (Skip columns)
7. Player Username (e.g., `jagdheesh`)

**Example Data to Paste:**
```
27/06/2026 15:56:39	0	0	0	jagdheesh
26/06/2026 23:07:41	0	0	20	lalit26
26/06/2026 23:06:36	200	0	0	lalit26
```

#### **Step 4: Paste Your Data**
- In the "Paste Raw Transaction Data" section, you'll see a large text box
- **Copy and Paste** your transaction data into this text area
- You can paste multiple rows at once

#### **Step 5: Select the Affiliate**
- Below the text area, there's a dropdown that says "Select Affiliate"
- **Click the dropdown** and choose the affiliate this data belongs to
- The affiliate's name and ID will show next to the dropdown

#### **Step 6: Import the Data**
- Click the **"Parse & Import"** button (blue button with lightning icon)
- The system will automatically:
  - Extract the columns from your data
  - Parse DateTime, Deposit, Withdraw, Bonus, and Player Username
  - Create entries in the database

#### **Step 7: Review the Results**
- Below the import button, you'll see a "Import Preview" section
- A table will show all the entries that were imported
- **Green checkmark** = Successfully imported
- Each row shows: DateTime, Player, Deposit, Withdrawal, Bonus, Status

#### **Step 8: Affiliates Can Now See Their Data**
- Affiliates can login to their partner panel
- They click on **"Daily Raw Data"** tab
- They'll see all the transaction data you imported for them

---

## 2. HOW TO REGISTER/ADD A NEW USER (AFFILIATE OR PLAYER)

### A. ADD A NEW AFFILIATE

#### **Step 1: Login to Admin Panel**
- Enter your admin credentials and click "Sign In"

#### **Step 2: Navigate to Manage Affiliates**
- In the left sidebar, click **"Manage Affiliates"**
- You'll see a form at the top called "Add New Affiliate"

#### **Step 3: Fill in the Affiliate Details**

Fill in these fields:

| Field | Example | Description |
|-------|---------|-------------|
| Affiliate ID | `AFF-1005` | Unique ID for the affiliate |
| First Name | `John` | Affiliate's first name |
| Last Name | `Smith` | Affiliate's last name |
| Username | `jsmith` | Login username (must be unique) |
| Password | `SecurePass123` | Secure password for login |
| Commission % | `25` | Percentage commission they earn (e.g., 25%) |

#### **Step 4: Submit the Form**
- Click the **"Add Affiliate"** button
- You'll see a success message at the bottom right
- The new affiliate will appear in the "Affiliates Table" below

#### **Step 5: Share Affiliate Panel Link**
- After adding, give the affiliate this link: `https://ap7affiliates.online/affiliate.html`
- They'll login using their username and password

---

### B. ADD A NEW PLAYER

#### **Step 1: Navigate to Players**
- In the left sidebar, click **"Players"**
- You'll see a form at the top called "Add New Player"

#### **Step 2: Fill in Player Details**

| Field | Example | Description |
|-------|---------|-------------|
| Player Username | `jagdheesh` | Player's unique username |
| Affiliate ID | `AFF-1001` | Which affiliate recruited this player |
| Registration Date | `2026-06-27` | Date player joined |

#### **Step 3: Submit**
- Click **"Add Player"**
- Player will appear in the Players Table below

#### **Step 4: Link to Your Raw Data**
- When you import raw transaction data for a player
- Use their exact username in the "To" column
- The system will automatically link transactions to this player

---

## 3. QUICK REFERENCE - DATA FORMAT

### Tab-Separated Format (Easiest):
```
DateTime              Deposit  Withdraw  Bonus  balance  remark              To
27/06/2026 15:56:39  0        0         0      0        Initial Deposit     jagdheesh
26/06/2026 23:07:41  0        0         20     268.2    10% bonus Added     lalit26
26/06/2026 23:06:36  200      0         0      248.2    Deposited           lalit26
```

### Comma-Separated Format (CSV):
```
27/06/2026 15:56:39,0,0,0,jagdheesh
26/06/2026 23:07:41,0,0,20,lalit26
26/06/2026 23:06:36,200,0,0,lalit26
```

---

## 4. TROUBLESHOOTING

### Issue: Import shows "No valid rows found"
**Solution:** 
- Check that each row has at least 5 columns
- Verify DateTime is in format: DD/MM/YYYY HH:MM:SS
- Make sure Player Username is the last column

### Issue: Data not showing in affiliate panel
**Solution:**
- Make sure the affiliate selected during import is correct
- Check that the Player Username matches exactly (case-sensitive)
- Wait a moment for the page to refresh

### Issue: Affiliate can't login
**Solution:**
- Verify the username and password you entered
- Check that "Active" status is enabled for that affiliate

---

## 5. HOW TO VIEW AFFILIATE PERFORMANCE

### Step 1: View Affiliate List
- Click **"Manage Affiliates"** in sidebar
- See all affiliates with their stats

### Step 2: Open Affiliate's Partner Panel
- Find the affiliate in the list
- Click the **blue arrow button** (↗) on the far right
- This opens their dedicated dashboard in a new window

### Step 3: View Reports
- As admin, click **"Reports"** in sidebar
- Filter by:
  - **Affiliate** - which affiliate's data to show
  - **Period** - Today, This Week, This Month, Custom Date Range
- Export to Excel if needed

### Step 4: Check Commission
- Click **"Commission"** in sidebar
- Shows commission calculation details
- See how much each affiliate has earned

---

## QUICK START CHECKLIST

- [ ] Login to Admin Panel
- [ ] Add your first Affiliate (using Manage Affiliates)
- [ ] Add some Players linked to that affiliate
- [ ] Prepare your transaction data in the format shown
- [ ] Go to Daily Raw Data tab
- [ ] Paste the data and select the affiliate
- [ ] Click "Parse & Import"
- [ ] Share the affiliate panel link with them
- [ ] They login and see their data in the "Daily Raw Data" tab

---

**Need Help?** Check the panel tooltips (hover over fields for hints) or contact support.

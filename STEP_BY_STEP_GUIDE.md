# 🎯 COMPLETE STEP-BY-STEP SETUP GUIDE (For Beginners)

---

# PART 1: FIX CACHE ISSUE (DO THIS FIRST - 2 MINUTES)

## Step 1.1: Clear Browser Cache

**On Windows (Chrome/Edge/Firefox):**

1. Open your browser (Chrome, Edge, or Firefox)
2. Press these keys together: `Ctrl + Shift + Delete`
3. A "Clear Browsing Data" window will open
4. At the top, select: **"All time"** (from the dropdown)
5. Check these boxes:
   - ☑ Cookies and other site data
   - ☑ Cached images and files
6. Click **"Clear data"** button
7. Wait for it to finish
8. Close the window

**On Mac:**
- Press: `Command + Shift + Delete`
- Follow same steps above

---

## Step 1.2: Clear LocalStorage

1. Go to your affiliate panel website
2. Press `F12` to open Developer Tools (black panel appears at bottom)
3. Find the **Console** tab at the top
4. Click inside the text field at the very bottom
5. Type exactly: `localStorage.clear()`
6. Press `Enter`
7. You'll see: `undefined` (that's normal - means it worked)

---

## Step 1.3: Hard Refresh Your Page

1. Press these keys together: `Ctrl + F5` (Windows) or `Command + Shift + R` (Mac)
2. Wait for page to load
3. Try generating a tracking link again
4. **You should now see:** `http://ap7affiliates.online/?aff=...` ✓

---

# PART 2: DEPLOY BACKEND TO RENDER.COM (15 MINUTES)

## Step 2.1: Create Render.com Account

1. Open: https://render.com
2. Click **"Sign Up"** (top right)
3. Click **"Sign up with GitHub"** (easiest option)
4. Click **"Authorize render-rac"** button
5. Enter your email
6. Create a password
7. Click **"Continue"**
8. Done! You're logged in

---

## Step 2.2: Create New Web Service

1. You're on Render dashboard
2. Look for **"New +"** button (top area)
3. Click it
4. Select **"Web Service"**
5. You'll see "Connect a repository" section

---

## Step 2.3: Connect Your GitHub Repository

1. If your files are already on GitHub:
   - Click the repository name that appears
   - Skip to Step 2.4

2. If files are NOT on GitHub:
   - Go to: https://github.com (create account if needed)
   - Click **"+"** (top right) → **"New repository"**
   - Name it: `affiliate-panel`
   - Upload your files:
     - `app.py`
     - `requirements.txt`
   - Click **"Create repository"**
   - Go back to Render.com and select it

---

## Step 2.4: Configure Deployment Settings

After selecting repository, you'll see a form:

| Field | Enter |
|-------|-------|
| **Name** | `affiliate-panel-api` |
| **Environment** | Select: **"Python 3"** |
| **Build Command** | `pip install -r requirements.txt` |
| **Start Command** | `python app.py` |
| **Instances** | Leave as default |
| **Plan** | Leave as default (free) |

---

## Step 2.5: Deploy

1. Scroll to bottom of form
2. Click **"Create Web Service"** button
3. Wait... Render will start building (takes 2-3 minutes)
4. You'll see a log showing build progress
5. When done, you'll see a **PUBLIC URL** like:
   ```
   https://affiliate-panel-api-xxxx.onrender.com
   ```
6. **SAVE THIS URL** - you'll need it next!

---

# PART 3: UPDATE YOUR HTML FILES (10 MINUTES)

## Step 3.1: Get Your Backend URL

From Render.com:
1. You'll see your service running
2. Copy the URL shown (something like `https://affiliate-panel-api-xxxx.onrender.com`)
3. This is your **API Base URL**

---

## Step 3.2: Update index.html (Admin Panel)

1. Open **index.html** in VS Code (or your editor)
2. Press `Ctrl + F` to find text
3. Search for: `API_BASE`
4. Find this line (around line 800):
   ```javascript
   const API_BASE = 'https://ap7affiliates-backend.onrender.com/api';
   ```
5. Replace with your URL:
   ```javascript
   const API_BASE = 'https://your-actual-url-here.onrender.com/api';
   ```
6. Add `/api` at the end
7. Save file: `Ctrl + S`

---

## Step 3.3: Update affiliate.html (Partner Panel)

1. Open **affiliate.html** in VS Code
2. Press `Ctrl + F` to find text
3. Search for: `API_BASE`
4. Find and replace the same line
5. Use the SAME URL as Step 3.2
6. Save file: `Ctrl + S`

---

# PART 4: UPLOAD TO HOSTINGER (10 MINUTES)

## Step 4.1: Login to Hostinger

1. Open: https://www.hostinger.com
2. Click **"Login"** (top right)
3. Enter your email and password
4. Click **"Sign In"**

---

## Step 4.2: Access File Manager

1. In Hostinger dashboard, find **"Websites"** or **"File Manager"**
2. Click on your domain (e.g., ap7affiliates.online)
3. Click **"File Manager"** or **"Manage Files"**

---

## Step 4.3: Upload HTML Files

1. Navigate to **public_html** folder (this is where websites live)
2. Look for **"Upload"** button
3. Click it
4. Upload these files one by one:
   - `index.html` ✓
   - `affiliate.html` ✓
   - `favicon.svg` ✓

---

## Step 4.4: Verify Files Uploaded

1. Refresh file manager
2. You should see all 3 files in `public_html`
3. Done! ✓

---

# PART 5: TEST EVERYTHING (5 MINUTES)

## Step 5.1: Test Admin Panel

1. Open browser
2. Go to: `https://ap7affiliates.online/index.html`
3. Login with:
   - Username: `Chris9742`
   - Password: `Chris9742`
4. Should see dashboard ✓

---

## Step 5.2: Test Tracking Link

1. In admin panel, click **"Manage Affiliates"**
2. Make sure at least 1 affiliate exists
3. Click **"Affiliates Overview"**
4. Find an affiliate, click the blue **"Open Partner Panel"** button
5. Now you're in affiliate panel
6. Click **"Create Link"** in sidebar
7. Fill in:
   - Campaign Name: `TestPromo`
   - Platform: `Instagram`
8. Click **"Generate Tracking URL"**
9. Check the URL shows: `http://ap7affiliates.online/?aff=...` ✓
10. NOT `https://brandtrack.com` ✓

---

## Step 5.3: Test Raw Data Import

1. Go back to admin panel (index.html)
2. Click **"Daily Raw Data"** in sidebar
3. Paste this data in the text box:
   ```
   27/06/2026 15:56:39	0	0	0	jagdheesh
   26/06/2026 23:07:41	0	0	20	lalit26
   26/06/2026 23:06:36	200	0	0	lalit26
   ```
4. Select an affiliate from dropdown
5. Click **"Parse & Import"**
6. You should see import preview table ✓
7. Status should show **"Imported"** ✓

---

## Step 5.4: Test Affiliate Sees Data

1. Click affiliate's blue arrow button to open their panel
2. Click **"Daily Raw Data"** tab
3. You should see the transactions you imported ✓

---

# ✅ CHECKLIST - EVERYTHING SHOULD NOW WORK

- [ ] Cache cleared
- [ ] Backend deployed to Render.com (have URL)
- [ ] HTML files updated with backend URL
- [ ] Files uploaded to Hostinger
- [ ] Admin panel loads
- [ ] Tracking URL shows `ap7affiliates.online`
- [ ] Reports show `₹` currency
- [ ] Can import raw data
- [ ] Affiliates see their data

---

# 🆘 IF SOMETHING DOESN'T WORK

## Problem: "Cannot connect to backend"
- Check your API_BASE URL is correct
- Make sure Render.com service is running
- Try refreshing page

## Problem: Tracking URL still shows old domain
- Clear cache: `Ctrl+Shift+Delete`
- Hard refresh: `Ctrl+F5`

## Problem: Files not showing on Hostinger
- Refresh file manager
- Make sure they're in `public_html` folder
- Wait 5 minutes for server to update

## Problem: Affiliate dropdown empty when importing data
- Create affiliates first in "Manage Affiliates"
- Then try importing again

---

# 📞 SUPPORT

If you get stuck on any step:
1. **Read the step again carefully**
2. **Check the exact text** you're supposed to enter
3. **Take a screenshot** of error message
4. **Try the fix** in the "IF SOMETHING DOESN'T WORK" section above

---

# QUICK REFERENCE - All URLs You Need

| Purpose | URL |
|---------|-----|
| Admin Panel | `https://ap7affiliates.online/index.html` |
| Affiliate Panel | `https://ap7affiliates.online/affiliate.html` |
| Render Backend | `https://affiliate-panel-api-xxxx.onrender.com` |

**Default Credentials:**
- Admin Username: `Chris9742`
- Admin Password: `Chris9742`

---

**YOU'VE GOT THIS! 🚀 Follow each step in order and everything will work perfectly!**

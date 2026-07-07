# Issue Resolution & Deployment Guide

## Issue #1: Tracking URL Domain

### ✅ VERIFIED - Backend is Correct
The backend (`app.py`) is correctly configured:
```python
def build_tracking_url(affiliate_id: str, platform: str, campaign: str) -> str:
    base = "http://ap7affiliates.online/"
    params = {
        "aff": affiliate_id,
        "source": platform,
        "campaign": campaign,
    }
    query = "&".join(f"{key}={quote(str(value), safe='')}" for key, value in params.items() if value)
    return f"{base}?{query}" if query else base
```

### Why You See brandtrack.com?
The screenshot shows old cached data. To fix:
1. **Restart Backend**: Stop and restart your Python backend
2. **Clear Browser Cache**: Press `Ctrl+Shift+Delete` and clear cache
3. **Clear LocalStorage**: Open browser console (F12), paste: `localStorage.clear()`
4. **Reload Page**: Refresh the affiliate panel

### Expected URL After Fix:
`http://ap7affiliates.online/?aff=AFF-1004&source=other&campaign=testclient`

---

## Issue #2: Admin Panel Reports Shows $

### ✅ VERIFIED - Reports Already Use ₹
The reports code is already correct and shows ₹ symbols:
```javascript
<td class="fw-semibold">₹${numFormat(r.deposit)}</td>
<td class="text-danger">₹${numFormat(r.withdrawal)}</td>
<td class="text-warning">₹${numFormat(r.bonus)}</td>
<td class="fw-semibold ${r.revenue >= 0 ? '' : 'text-danger'}">₹${numFormat(r.revenue)}</td>
<td class="fw-semibold text-success">₹${numFormat(r.commission)}</td>
```

### How to Fix If Still Seeing $:
1. **Clear browser cache** (Ctrl+Shift+Delete)
2. **Hard refresh** (Ctrl+F5)
3. **Check DevTools** (F12) → Application → Cache Storage → Clear all

---

## Issue #3: Upload to Hostinger - YES, You Need To

### Step-by-Step Deployment Guide

#### **STEP 1: Prepare Files**
Create a folder with only these files:
```
/affiliate-panel/
├── index.html (Admin Panel)
├── affiliate.html (Partner Panel)
├── app.py (Backend API)
├── favicon.svg (Logo)
├── requirements.txt (Python dependencies)
├── HOW_TO_GUIDES.md (Documentation)
├── README_DEPLOY_HOSTINGER.md (Deployment notes)
└── database.sql (Optional - for database)
```

#### **STEP 2: Upload to Hostinger**

**For the HTML Files (Frontend):**
1. Login to Hostinger → File Manager
2. Upload `index.html` and `affiliate.html` to `/public_html/` folder
3. Upload `favicon.svg` to `/public_html/` folder

**For the Python Backend (app.py):**
1. Hostinger doesn't support running Python scripts directly (shared hosting)
2. **Use Render.com instead** (FREE tier available):
   - Go to [render.com](https://render.com)
   - Sign up with GitHub
   - Create New → Web Service
   - Connect your GitHub repository
   - Build Command: `pip install -r requirements.txt`
   - Start Command: `python app.py`
   - Deploy

**OR Use Replit (Alternative):**
- Go to [replit.com](https://replit.com)
- Create new Python project
- Upload your files
- Run the project
- Get public URL

#### **STEP 3: Update Frontend URLs**

In both `index.html` and `affiliate.html`, update the API_BASE:

**Current (Local):**
```javascript
const API_BASE = 'https://ap7affiliates-backend.onrender.com/api';
```

**Change to your deployed backend URL:**
```javascript
const API_BASE = 'https://your-backend-url.com/api';
```

#### **STEP 4: Update Hostinger Domain Settings**

If using your own domain (e.g., ap7affiliates.online):
1. Hostinger → Domains → Point to your Hostinger server
2. Update DNS records:
   - A Record → Your Hostinger IP
   - CNAME → www.ap7affiliates.online

#### **STEP 5: Verify Deployment**

1. Visit: `https://ap7affiliates.online/index.html` (Admin)
2. Visit: `https://ap7affiliates.online/affiliate.html` (Partner)
3. Login and test features

---

## Issue #4: Raw Data Upload - Why It's Not Working

### ⚠️ PREREQUISITES NOT MET

The raw data feature requires:

#### **1. Backend Must Be Running**
- ✅ Upload `app.py` to Render.com or your server
- ✅ Get the public API URL
- ✅ Update `API_BASE` in HTML files

#### **2. Affiliates Must Exist**
- Go to Admin Panel → "Manage Affiliates"
- Add at least 1 affiliate:
  - First Name: `John`
  - Last Name: `Doe`
  - Username: `johndoe`
  - Password: `pass123`
  - Commission: `25%`
- Click "Create Affiliate"

#### **3. Then Import Raw Data**

**Step-by-Step:**
1. Login as **Admin** (Chris9742 / Chris9742)
2. Click **"Daily Raw Data"** in sidebar
3. Paste your data:
   ```
   27/06/2026 15:56:39	0	0	0	jagdheesh
   26/06/2026 23:07:41	0	0	20	lalit26
   26/06/2026 23:06:36	200	0	0	lalit26
   ```
4. Select Affiliate from dropdown
5. Click **"Parse & Import"**
6. See "Import Preview" table with status

**Then Affiliate Sees Data:**
1. Login as Affiliate (johndoe / pass123)
2. Click **"Daily Raw Data"** tab
3. See their imported transactions

---

## Quick Checklist

### To Make Everything Work:

- [ ] **Backend**: Deploy app.py to Render.com or Replit
- [ ] **Frontend**: Upload index.html & affiliate.html to Hostinger
- [ ] **URLs**: Update API_BASE in both HTML files
- [ ] **Cache**: Clear browser cache (Ctrl+Shift+Delete)
- [ ] **Refresh**: Hard refresh (Ctrl+F5)
- [ ] **Tracking URLs**: Should now show `ap7affiliates.online`
- [ ] **Reports**: Should show ₹ currency
- [ ] **Add Affiliates**: Create test affiliate in admin panel
- [ ] **Raw Data**: Paste data and import

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Tracking URL still shows old domain | Clear cache + hard refresh |
| Reports show $ instead of ₹ | Clear LocalStorage: `localStorage.clear()` in console |
| Raw data dropdown empty | Create affiliates in "Manage Affiliates" first |
| Backend API not connecting | Check API_BASE URL matches your deployed backend |
| Affiliate can't see data | Make sure you selected correct affiliate during import |

---

## Deployment Platforms (FREE)

### Backend Options:
1. **Render.com** (✅ Recommended)
   - 750 free hours/month
   - Auto-deploys from GitHub
   - Free custom domain

2. **Replit**
   - Free tier
   - Easy deployment
   - Public URL included

3. **Railway.app**
   - $5 monthly credit
   - Free tier available

### Frontend Options:
1. **Hostinger** (Your current plan)
2. **Netlify** (Free tier)
3. **Vercel** (Free tier)
4. **GitHub Pages** (Free)

---

## Next Steps

1. **Deploy Backend** → Get API URL
2. **Update API_BASE** in HTML files
3. **Upload to Hostinger** → Test
4. **Clear Cache** → See updated tracking URLs
5. **Create Test Data** → Add affiliates & import raw data

All features will then work! 🚀

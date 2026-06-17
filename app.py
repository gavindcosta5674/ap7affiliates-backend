from fastapi import FastAPI, HTTPException, Header
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import Optional, List
import uvicorn
import secrets

# ── FastAPI Setup ──────────────────────────────────────────────────────────
app = FastAPI(title="Master Affiliate Panel API")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# ── In-Memory Databases ───────────────────────────────────────────────────
affiliates_db = [
    {
        "id": "AFF-1001",
        "first_name": "John",
        "last_name": "Smith",
        "username": "jsmith",
        "password": "pass123",
        "commission_pct": 25.0,
        "status": "active",
        "total_players": 2,
        "total_deposits": 17500.00,
        "total_withdrawals": 5500.00,
        "total_bonuses": 1000.00,
        "revenue": 11000.00,
        "commission": 2750.00,
        "created_at": "2025-01-01 10:00:00",
    },
    {
        "id": "AFF-1002",
        "first_name": "Sarah",
        "last_name": "Johnson",
        "username": "sjohnson",
        "password": "pass123",
        "commission_pct": 30.0,
        "status": "active",
        "total_players": 1,
        "total_deposits": 8700.00,
        "total_withdrawals": 2100.00,
        "total_bonuses": 500.00,
        "revenue": 6100.00,
        "commission": 1830.00,
        "created_at": "2025-01-15 10:00:00",
    },
    {
        "id": "AFF-1003",
        "first_name": "Mike",
        "last_name": "Williams",
        "username": "mwilliams",
        "password": "pass123",
        "commission_pct": 20.0,
        "status": "active",
        "total_players": 2,
        "total_deposits": 21600.00,
        "total_withdrawals": 10000.00,
        "total_bonuses": 1350.00,
        "revenue": 10250.00,
        "commission": 2050.00,
        "created_at": "2025-02-01 10:00:00",
    },
]
affiliate_id_counter = 1003

# Links tracking
links_db = []
link_id_counter = 0

# Report data keyed by affiliate_id
reports_db = {
    "AFF-1001": [
        {"id": 1, "affiliate_id": "AFF-1001", "player_username": "player_one",  "ftd_date": "2025-01-15", "deposit": 5000.00,  "count": 3, "withdrawal": 1200.00, "bonus": 250.00,  "revenue": 3550.00,  "commission": 887.50, "commission_percentage": 25},
        {"id": 2, "affiliate_id": "AFF-1001", "player_username": "slot_king",    "ftd_date": "2025-02-03", "deposit": 12500.00, "count": 5, "withdrawal": 4300.00, "bonus": 750.00,  "revenue": 7450.00,  "commission": 1862.50, "commission_percentage": 25},
    ],
    "AFF-1002": [
        {"id": 3, "affiliate_id": "AFF-1002", "player_username": "roulette_pro",  "ftd_date": "2025-03-10", "deposit": 8700.00, "count": 4, "withdrawal": 2100.00, "bonus": 500.00,  "revenue": 6100.00,  "commission": 1830.00, "commission_percentage": 30},
    ],
    "AFF-1003": [
        {"id": 4, "affiliate_id": "AFF-1003", "player_username": "blackjack_ace", "ftd_date": "2025-04-22", "deposit": 3200.00, "count": 2, "withdrawal": 800.00,  "bonus": 150.00,  "revenue": 2250.00,  "commission": 450.00, "commission_percentage": 20},
        {"id": 5, "affiliate_id": "AFF-1003", "player_username": "lucky_seven",   "ftd_date": "2025-05-05", "deposit": 18400.00,"count": 6, "withdrawal": 9200.00, "bonus": 1200.00, "revenue": 8000.00,  "commission": 1600.00, "commission_percentage": 20},
    ],
}

# Shared media gallery
media_db = [
    {"id": 1, "title": "Summer Campaign Banner",   "media_type": "banner", "file_name": "summer_banner.jpg",  "file_path": "uploads/summer_banner.jpg",  "upload_date": "2025-06-01"},
    {"id": 2, "title": "Welcome Bonus Image",      "media_type": "image",  "file_name": "welcome_bonus.png",   "file_path": "uploads/welcome_bonus.png",   "upload_date": "2025-06-05"},
    {"id": 3, "title": "How to Play Guide",        "media_type": "pdf",    "file_name": "how_to_play.pdf",     "file_path": "uploads/how_to_play.pdf",     "upload_date": "2025-06-10"},
    {"id": 4, "title": "Promotional Video 2025",   "media_type": "video",  "file_name": "promo_2025.mp4",      "file_path": "uploads/promo_2025.mp4",      "upload_date": "2025-06-15"},
]

# Players database
players_db = [
    {"player_id": "PLR1001", "player_username": "player_one", "affiliate_id": "AFF-1001", "ftd_date": "2025-01-15", "deposit_total": 5000.00, "deposit_count": 3, "withdrawal_total": 1200.00, "bonus_total": 250.00, "revenue": 3550.00, "commission": 887.50},
    {"player_id": "PLR1002", "player_username": "slot_king", "affiliate_id": "AFF-1001", "ftd_date": "2025-02-03", "deposit_total": 12500.00, "deposit_count": 5, "withdrawal_total": 4300.00, "bonus_total": 750.00, "revenue": 7450.00, "commission": 1862.50},
    {"player_id": "PLR1003", "player_username": "roulette_pro", "affiliate_id": "AFF-1002", "ftd_date": "2025-03-10", "deposit_total": 8700.00, "deposit_count": 4, "withdrawal_total": 2100.00, "bonus_total": 500.00, "revenue": 6100.00, "commission": 1830.00},
    {"player_id": "PLR1004", "player_username": "blackjack_ace", "affiliate_id": "AFF-1003", "ftd_date": "2025-04-22", "deposit_total": 3200.00, "deposit_count": 2, "withdrawal_total": 800.00, "bonus_total": 150.00, "revenue": 2250.00, "commission": 450.00},
    {"player_id": "PLR1005", "player_username": "lucky_seven", "affiliate_id": "AFF-1003", "ftd_date": "2025-05-05", "deposit_total": 18400.00, "deposit_count": 6, "withdrawal_total": 9200.00, "bonus_total": 1200.00, "revenue": 8000.00, "commission": 1600.00},
]
player_id_counter = 1005

# ── Helper functions ──────────────────────────────────────────────────────
def find_affiliate(username: str, password: str):
    for aff in affiliates_db:
        if aff["username"] == username and aff["password"] == password and aff["status"] == "active":
            return aff
    return None


def find_affiliate_by_username(username: str):
    for aff in affiliates_db:
        if aff["username"] == username:
            return aff
    return None


def find_affiliate_by_id(aid: str):
    for aff in affiliates_db:
        if aff["id"] == aid:
            return aff
    return None

# ── Pydantic Models ───────────────────────────────────────────────────────
class LoginRequest(BaseModel):
    username: str
    password: str

class LoginResponse(BaseModel):
    success: bool
    token: str
    message: str
    affiliate: Optional[dict] = None

class CreateLinkRequest(BaseModel):
    name: str
    platform: str

# =========================================================================
# MASTER ADMIN ENDPOINTS
# =========================================================================

@app.post("/api/login")
def master_login(body: LoginRequest):
    if body.username == "Chris9742" and body.password == "Chris9742":
        return LoginResponse(success=True, token="master-token-chris9742", message="Login successful")
    raise HTTPException(status_code=401, detail="Invalid username or password")

@app.get("/api/affiliates")
def get_affiliates():
    safe = []
    for aff in affiliates_db:
        aff_copy = {k: v for k, v in aff.items() if k != "password"}
        # Recalculate
        aff_copy["revenue"] = round(aff_copy.get("total_deposits", 0) - aff_copy.get("total_withdrawals", 0) - aff_copy.get("total_bonuses", 0), 2)
        aff_copy["commission"] = round(aff_copy["revenue"] * aff_copy.get("commission_pct", 20) / 100, 2)
        safe.append(aff_copy)
    return safe

@app.post("/api/affiliates")
def create_affiliate(body: dict):
    global affiliate_id_counter
    affiliate_id_counter += 1
    new_id = f"AFF-{affiliate_id_counter}"
    pct = float(body.get("percentage", 20))
    record = {
        "id": new_id,
        "affiliate_id": new_id,
        "first_name": body.get("first_name", ""),
        "last_name": body.get("last_name", ""),
        "username": body.get("username", ""),
        "password": body.get("password", ""),
        "commission_pct": pct,
        "commission_percentage": pct,
        "status": "active",
        "total_players": 0,
        "total_deposits": 0,
        "total_withdrawals": 0,
        "total_bonuses": 0,
        "revenue": 0,
        "commission": 0,
        "created_at": "2025-06-07 12:00:00",
    }
    affiliates_db.append(record)
    return {k: v for k, v in record.items() if k != "password"}

@app.put("/api/affiliates")
def update_affiliate_status(id: str, status: str):
    aff = find_affiliate_by_id(id)
    if not aff:
        raise HTTPException(status_code=404, detail="Affiliate not found")
    if status not in ["active", "inactive"]:
        raise HTTPException(status_code=400, detail="Invalid status")
    aff["status"] = status
    return {"success": True, "message": f"Affiliate {id} status changed to {status}"}

@app.delete("/api/affiliates")
def delete_affiliate(id: str):
    global affiliates_db
    for i, aff in enumerate(affiliates_db):
        if aff["id"] == id:
            affiliates_db.pop(i)
            return {"success": True, "message": "Affiliate deleted"}
    raise HTTPException(status_code=404, detail="Affiliate not found")

@app.get("/api/dashboard")
def get_dashboard():
    total_affiliates = len(affiliates_db)
    active_affiliates = sum(1 for a in affiliates_db if a["status"] == "active")
    
    all_players = []
    for aff_id, rows in reports_db.items():
        for r in rows:
            all_players.append(r)
    
    total_players = len(all_players)
    total_deposits = sum(r["deposit"] for r in all_players)
    total_withdrawals = sum(r["withdrawal"] for r in all_players)
    total_bonuses = sum(r["bonus"] for r in all_players)
    total_revenue = round(total_deposits - total_withdrawals - total_bonuses, 2)
    
    avg_pct = sum(a["commission_pct"] for a in affiliates_db) / len(affiliates_db) if affiliates_db else 20
    total_commission = round(total_revenue * avg_pct / 100, 2)

    recent = []
    for aff in affiliates_db:
        aff_id = aff["id"]
        rows = reports_db.get(aff_id, [])
        dep = sum(r["deposit"] for r in rows)
        wd = sum(r["withdrawal"] for r in rows)
        bn = sum(r["bonus"] for r in rows)
        rev = round(dep - wd - bn, 2)
        comm = round(rev * aff["commission_pct"] / 100, 2)
        recent.append({
            "affiliate_id": aff_id,
            "first_name": aff["first_name"],
            "last_name": aff["last_name"],
            "total_players": len(rows),
            "total_deposits": dep,
            "total_withdrawals": wd,
            "total_bonuses": bn,
            "revenue": rev,
            "commission": comm,
            "commission_percentage": aff["commission_pct"],
        })

    return {
        "total_affiliates": total_affiliates,
        "active_affiliates": active_affiliates,
        "total_players": total_players,
        "total_deposits": round(total_deposits, 2),
        "total_withdrawals": round(total_withdrawals, 2),
        "total_bonuses": round(total_bonuses, 2),
        "total_revenue": total_revenue,
        "total_commission": total_commission,
        "recent_activity": recent,
    }

@app.get("/api/reports")
def get_reports(affiliate_id: str = None, date_from: str = None, date_to: str = None, player_username: str = None):
    all_reports = []
    for aff_id, rows in reports_db.items():
        for r in rows:
            r_copy = dict(r)
            r_copy["affiliate_id"] = aff_id
            # Get commission percentage from affiliate
            aff = find_affiliate_by_id(aff_id)
            r_copy["commission_percentage"] = aff["commission_pct"] if aff else 20
            r_copy["commission"] = round(r_copy["revenue"] * r_copy["commission_percentage"] / 100, 2)
            all_reports.append(r_copy)

    # Apply filters
    if affiliate_id:
        all_reports = [r for r in all_reports if r["affiliate_id"] == affiliate_id]
    if player_username:
        all_reports = [r for r in all_reports if player_username.lower() in r["player_username"].lower()]
    if date_from:
        all_reports = [r for r in all_reports if r["ftd_date"] >= date_from]
    if date_to:
        all_reports = [r for r in all_reports if r["ftd_date"] <= date_to]

    return all_reports

@app.get("/api/commission")
def get_commission():
    total_deposits = 0
    total_withdrawals = 0
    total_bonuses = 0
    for aff_id, rows in reports_db.items():
        for r in rows:
            total_deposits += r["deposit"]
            total_withdrawals += r["withdrawal"]
            total_bonuses += r["bonus"]
    total_revenue = round(total_deposits - total_withdrawals - total_bonuses, 2)
    avg_pct = sum(a["commission_pct"] for a in affiliates_db) / len(affiliates_db) if affiliates_db else 20
    total_commission = round(total_revenue * avg_pct / 100, 2)

    aff_data = []
    for aff in affiliates_db:
        aff_id = aff["id"]
        rows = reports_db.get(aff_id, [])
        dep = sum(r["deposit"] for r in rows)
        wd = sum(r["withdrawal"] for r in rows)
        bn = sum(r["bonus"] for r in rows)
        rev = round(dep - wd - bn, 2)
        comm = round(rev * aff["commission_pct"] / 100, 2)
        aff_data.append({
            "affiliate_id": aff_id,
            "first_name": aff["first_name"],
            "last_name": aff["last_name"],
            "total_players": len(rows),
            "total_deposits": dep,
            "total_withdrawals": wd,
            "total_bonuses": bn,
            "revenue": rev,
            "commission_percentage": aff["commission_pct"],
            "commission": comm,
        })

    return {
        "total_revenue": total_revenue,
        "total_commission": total_commission,
        "average_percentage": round(avg_pct, 2),
        "affiliates": aff_data,
    }

@app.post("/api/commission")
def update_commission(body: dict):
    global affiliates_db
    if "global_percentage" in body:
        pct = float(body["global_percentage"])
        for aff in affiliates_db:
            aff["commission_pct"] = pct
        return {"success": True, "message": "Global commission updated", "percentage": pct}
    if "affiliate_id" in body and "percentage" in body:
        aff = find_affiliate_by_id(body["affiliate_id"])
        if not aff:
            raise HTTPException(status_code=404, detail="Affiliate not found")
        aff["commission_pct"] = float(body["percentage"])
        return {"success": True, "message": f"Commission updated for {body['affiliate_id']}"}
    raise HTTPException(status_code=400, detail="Invalid request")

@app.get("/api/media")
def get_media():
    return media_db

@app.post("/api/media")
def upload_media():
    return {"error": "File upload not supported via in-memory API", "media_db": media_db}

@app.delete("/api/media")
def delete_media(id: int):
    global media_db
    for i, m in enumerate(media_db):
        if m["id"] == id:
            media_db.pop(i)
            return {"success": True, "message": "Media deleted"}
    raise HTTPException(status_code=404, detail="Media not found")

@app.get("/api/players")
def get_players():
    result = []
    for p in players_db:
        aff = find_affiliate_by_id(p["affiliate_id"])
        pct = aff["commission_pct"] if aff else 20
        p_copy = dict(p)
        p_copy["commission_percentage"] = pct
        result.append(p_copy)
    return result

@app.post("/api/players")
def create_player(body: dict):
    global player_id_counter, players_db
    player_id_counter += 1
    player_id = f"PLR{player_id_counter}"
    aff_id = body.get("affiliate_id", "")
    
    aff = find_affiliate_by_id(aff_id)
    if not aff:
        raise HTTPException(status_code=404, detail="Affiliate not found")
    
    deposit = float(body.get("deposit_amount", 0))
    withdrawal = float(body.get("withdrawal_amount", 0))
    bonus = float(body.get("bonus_amount", 0))
    revenue = round(deposit - withdrawal - bonus, 2)
    commission = round(revenue * aff["commission_pct"] / 100, 2)
    
    record = {
        "player_id": player_id,
        "player_username": body.get("player_username", ""),
        "affiliate_id": aff_id,
        "ftd_date": body.get("ftd_date", "2025-06-07"),
        "deposit_total": deposit,
        "deposit_count": 1 if deposit > 0 else 0,
        "withdrawal_total": withdrawal,
        "bonus_total": bonus,
        "revenue": revenue,
        "commission": commission,
    }
    players_db.append(record)
    
    # Also add to reports_db
    if aff_id not in reports_db:
        reports_db[aff_id] = []
    reports_db[aff_id].append({
        "id": player_id_counter + 100,
        "affiliate_id": aff_id,
        "player_username": body.get("player_username", ""),
        "ftd_date": body.get("ftd_date", "2025-06-07"),
        "deposit": deposit,
        "count": 1 if deposit > 0 else 0,
        "withdrawal": withdrawal,
        "bonus": bonus,
        "revenue": revenue,
        "commission": commission,
        "commission_percentage": aff["commission_pct"],
    })
    
    return {"success": True, "player_id": player_id, **record}

@app.delete("/api/players")
def delete_player(id: str):
    global players_db
    for i, p in enumerate(players_db):
        if p["player_id"] == id:
            players_db.pop(i)
            return {"success": True, "message": "Player deleted"}
    raise HTTPException(status_code=404, detail="Player not found")

# =========================================================================
# AFFILIATE PARTNER ENDPOINTS
# =========================================================================

SESSION_TOKENS = {}

def get_affiliate_id_from_header(authorization: Optional[str] = Header(None, alias="Authorization")) -> str:
    if not authorization:
        raise HTTPException(status_code=401, detail="Missing Authorization header")
    token = authorization
    if authorization.lower().startswith("bearer "):
        token = authorization[7:]
    affiliate_id = SESSION_TOKENS.get(token)
    if not affiliate_id:
        raise HTTPException(status_code=401, detail="Invalid or expired token")
    return affiliate_id

@app.post("/api/affiliate/login")
def affiliate_login(body: LoginRequest):
    aff = find_affiliate_by_username(body.username)
    if not aff:
        raise HTTPException(status_code=401, detail="Incorrect Username")
    if aff["password"] != body.password:
        raise HTTPException(status_code=401, detail="Incorrect Password")
    if aff["status"] != "active":
        raise HTTPException(status_code=401, detail="Account is inactive")
    token = secrets.token_hex(16)
    SESSION_TOKENS[token] = aff["id"]
    safe = {k: v for k, v in aff.items() if k != "password"}
    return {"success": True, "token": token, "message": "Affiliate login successful", "affiliate": safe}

@app.get("/api/affiliate/dashboard")
def affiliate_dashboard(authorization: Optional[str] = Header(None, alias="Authorization")):
    affiliate_id = get_affiliate_id_from_header(authorization)
    aff = find_affiliate_by_id(affiliate_id)
    if not aff:
        raise HTTPException(status_code=404, detail="Affiliate not found")
    rows = reports_db.get(affiliate_id, [])
    total_deposits = sum(r["deposit"] for r in rows)
    total_withdrawals = sum(r["withdrawal"] for r in rows)
    total_bonuses = sum(r["bonus"] for r in rows)
    total_revenue = total_deposits - total_withdrawals - total_bonuses
    total_commission = sum(r["commission"] for r in rows)
    return {
        "affiliate_id": affiliate_id,
        "first_name": aff["first_name"],
        "last_name": aff["last_name"],
        "commission_pct": aff["commission_pct"],
        "total_players": len(rows),
        "total_deposits": round(total_deposits, 2),
        "total_withdrawals": round(total_withdrawals, 2),
        "total_bonuses": round(total_bonuses, 2),
        "total_revenue": round(total_revenue, 2),
        "total_commission": round(total_commission, 2),
        "status": aff["status"],
    }

@app.get("/api/affiliate/reports")
def affiliate_reports(authorization: Optional[str] = Header(None, alias="Authorization")):
    affiliate_id = get_affiliate_id_from_header(authorization)
    rows = reports_db.get(affiliate_id, [])
    result = []
    for r in rows:
        r_copy = dict(r)
        r_copy["affiliate_id"] = affiliate_id
        result.append(r_copy)
    return result

@app.get("/api/affiliate/media")
def affiliate_get_media(authorization: Optional[str] = Header(None, alias="Authorization")):
    get_affiliate_id_from_header(authorization)
    return media_db

@app.get("/api/links")
def get_links():
    return links_db

@app.post("/api/links")
def create_link(body: CreateLinkRequest, authorization: Optional[str] = Header(None, alias="Authorization")):
    global link_id_counter, links_db
    affiliate_id = get_affiliate_id_from_header(authorization)
    link_id_counter += 1
    safe_name = body.name.lower().replace(" ", "_")
    safe_platform = body.platform.lower().replace(" ", "_")
    tracking_url = f"https://brandtrack.com/register?aff={affiliate_id}&source={safe_platform}&campaign={safe_name}"
    record = {
        "id": link_id_counter,
        "name": body.name,
        "platform": body.platform,
        "affiliate_id": affiliate_id,
        "tracking_url": tracking_url,
        "created_at": "2025-06-07 12:00:00",
    }
    links_db.append(record)
    return record

@app.delete("/api/links/{link_id}")
def delete_link(link_id: int, authorization: Optional[str] = Header(None, alias="Authorization")):
    affiliate_id = get_affiliate_id_from_header(authorization)
    global links_db
    for i, link in enumerate(links_db):
        if link["id"] == link_id:
            if link["affiliate_id"] != affiliate_id:
                raise HTTPException(status_code=403, detail="Not authorized to delete this link")
            links_db.pop(i)
            return {"success": True, "message": "Link deleted"}
    raise HTTPException(status_code=404, detail="Link not found")

@app.get("/api/affiliate/links")
def get_affiliate_links(authorization: Optional[str] = Header(None, alias="Authorization")):
    affiliate_id = get_affiliate_id_from_header(authorization)
    return [l for l in links_db if l["affiliate_id"] == affiliate_id]

# ── Entry Point ───────────────────────────────────────────────────────────
if __name__ == "__main__":
    uvicorn.run("app:app", host="0.0.0.0", port=8000, reload=True)
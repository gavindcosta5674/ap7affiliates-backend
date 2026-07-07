Hostinger deployment steps (quick)

This project is configured for live deployment using:
- `admin.ap7affiliates.online` for the master admin site and API
- `affiliate.ap7affiliates.online` for the affiliate partner site

Prerequisites
- SSH access to the Hostinger server at `145.79.26.40` on port `65002`.
- DNS A records created for both subdomains pointing to `145.79.26.40`.

1) Upload project files
- From your local machine run:
```bash
scp -P 65002 -r ./MasterPanel u837790764@145.79.26.40:/home/u837790764/MasterPanel
```

2) SSH into server
```bash
ssh -p 65002 u837790764@145.79.26.40
```

3) Create Python environment and install dependencies
```bash
cd /home/u837790764/MasterPanel
python3 -m venv venv
source venv/bin/activate
pip install -U pip
pip install -r requirements.txt
chmod +x start.sh
```

4) Test the app locally
```bash
source venv/bin/activate
./start.sh
```

Verify in another terminal or browser:
```bash
curl -I http://127.0.0.1:8000/docs
```

Stop it after testing and continue to production setup.

5) Create a systemd service
```bash
sudo tee /etc/systemd/system/masterpanel.service > /dev/null <<'EOF'
[Unit]
Description=Master Panel FastAPI
After=network.target

[Service]
User=u837790764
WorkingDirectory=/home/u837790764/MasterPanel
ExecStart=/home/u837790764/MasterPanel/start.sh
Restart=always
RestartSec=5s

[Install]
WantedBy=multi-user.target
EOF

sudo systemctl daemon-reload
sudo systemctl enable masterpanel
sudo systemctl start masterpanel
sudo systemctl status masterpanel
```

6) Install Nginx and Certbot
```bash
sudo apt update
sudo apt install -y nginx certbot python3-certbot-nginx
```

7) Create static folders and copy front-end files
```bash
sudo mkdir -p /var/www/admin
sudo mkdir -p /var/www/affiliate
sudo chown -R u837790764:u837790764 /var/www/admin /var/www/affiliate
cp /home/u837790764/MasterPanel/index.html /var/www/admin/index.html
cp /home/u837790764/MasterPanel/affiliate.html /var/www/affiliate/affiliate.html
```

8) Use the included sample Nginx configs
- `nginx/admin.ap7affiliates.online.conf`
- `nginx/affiliate.ap7affiliates.online.conf`

Deploy them with:
```bash
sudo ln -sf /home/u837790764/MasterPanel/nginx/admin.ap7affiliates.online.conf /etc/nginx/sites-enabled/admin.ap7affiliates.online.conf
sudo ln -sf /home/u837790764/MasterPanel/nginx/affiliate.ap7affiliates.online.conf /etc/nginx/sites-enabled/affiliate.ap7affiliates.online.conf
sudo nginx -t
sudo systemctl reload nginx
```

9) Enable HTTPS
```bash
sudo certbot --nginx -d admin.ap7affiliates.online -d affiliate.ap7affiliates.online
```

Choose the redirect option when prompted so both sites use HTTPS.

10) Verify live sites
- `https://admin.ap7affiliates.online`
- `https://affiliate.ap7affiliates.online`
- `https://admin.ap7affiliates.online/docs`

Notes
- `affiliate.html` already points to `https://admin.ap7affiliates.online/api`, so the affiliate panel will work once the admin API is live.
- `index.html` uses the current host for `/api`, which is correct for the admin domain.

If your Hostinger plan does not allow custom nginx/systemd, use hPanel Python Apps instead and point it to `app:app`.

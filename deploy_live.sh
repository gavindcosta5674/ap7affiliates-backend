#!/usr/bin/env bash
# Usage: run this on the Hostinger server after uploading the project.

set -e
cd /home/u837790764/MasterPanel
python3 -m venv venv
source venv/bin/activate
pip install -U pip
pip install -r requirements.txt
chmod +x start.sh

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

sudo apt update
sudo apt install -y nginx certbot python3-certbot-nginx
sudo mkdir -p /var/www/admin /var/www/affiliate
sudo chown -R u837790764:u837790764 /var/www/admin /var/www/affiliate
sudo cp /home/u837790764/MasterPanel/index.html /var/www/admin/index.html
sudo cp /home/u837790764/MasterPanel/affiliate.html /var/www/affiliate/affiliate.html
sudo ln -sf /home/u837790764/MasterPanel/nginx/admin.ap7affiliates.online.conf /etc/nginx/sites-enabled/admin.ap7affiliates.online.conf
sudo ln -sf /home/u837790764/MasterPanel/nginx/affiliate.ap7affiliates.online.conf /etc/nginx/sites-enabled/affiliate.ap7affiliates.online.conf
sudo nginx -t
sudo systemctl reload nginx
sudo certbot --nginx -d admin.ap7affiliates.online -d affiliate.ap7affiliates.online

printf "\nDeployment complete. Verify:\n  https://admin.ap7affiliates.online\n  https://affiliate.ap7affiliates.online\n"

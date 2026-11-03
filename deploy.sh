#!/bin/bash
set -e
# Deploy manual de argos: hace lo mismo que .github/workflows/deploy.yml.
# Cargar variables desde .env.deploy (nunca hardcodear credenciales)
source .env.deploy

# .htaccess, .user.ini y php.ini no se despliegan: los administra cPanel (ver README).
lftp -c "set ftp:ssl-allow no; \
  open -u '$FTP_USER','$FTP_PASS' ftp://$FTP_SERVER; \
  cd /argos.apps-handel.com; \
  mirror -R \
    --exclude '(^|/)\.git' \
    --exclude '^\.github/' \
    --exclude '^\.claude/' \
    --exclude '^\.env' \
    --exclude '(^|/)node_modules/' \
    --exclude '(^|/)tests/' \
    --exclude '\.md$' \
    --exclude '^deploy\.sh$' \
    --exclude '^(\.htaccess|\.user\.ini|php\.ini)$' \
    --exclude '^\.(project|buildpath|settings)' \
    --exclude '\.DS_Store$' \
    . ."

curl -f --max-time 900 -X POST "$APP_URL/post_deploy.php" -d "token=$MIGRATE_TOKEN"

# Verificación posterior (igual que el workflow)
curl -fsS --max-time 60 -o /tmp/argos_index.html "$APP_URL/index.php"
if grep -q 'localhost' /tmp/argos_index.html; then
  echo "ERROR: index.php referencia localhost (revisar php/configuracion.php)"
  exit 1
fi
rm -f /tmp/argos_index.html

echo "Deploy de argos completado."

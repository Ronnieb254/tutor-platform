# Deploy Group Session Fix to Production
# Run this from your local machine

$server = "root@your-server-ip"  # UPDATE THIS!

Write-Host "🚀 Deploying Group Session Fix..." -ForegroundColor Cyan

# Deploy via SSH
ssh $server @"
cd /root/microservice/tutor-platform && \
git pull origin master && \
docker-compose exec -T app php artisan migrate --force && \
docker-compose exec -T app php artisan config:clear && \
docker-compose exec -T app php artisan cache:clear && \
docker-compose exec -T app php artisan view:clear && \
docker-compose exec -T app php artisan optimize && \
echo '✅ Deployment complete!'
"@

Write-Host "✅ Done! Check https://mwalimu.ovaldesk.co.ke/dashboard" -ForegroundColor Green

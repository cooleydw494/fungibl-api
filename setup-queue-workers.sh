#!/bin/bash

# Install supervisor
sudo apt-get update
sudo apt-get install -y supervisor

# create log file
touch /www/fungibl-api/storage/logs/worker.log

# Create the configuration file for the queue worker
echo "[program:fungibl-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /www/fungibl-api/artisan queue:work --daemon --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/www/fungibl-api/worker.log" > /etc/supervisor/conf.d/fungibl-worker.conf

# Tell supervisor to read the new configuration file
sudo supervisorctl reread
sudo supervisorctl update

# Start the queue worker
sudo supervisorctl start fungibl-worker

# Add the babysitting script to the crontab to run every minute
(sudo crontab -l 2>/dev/null; echo "* * * * * /www/fungibl-api/babysit-queue-workers.sh") | sudo crontab -

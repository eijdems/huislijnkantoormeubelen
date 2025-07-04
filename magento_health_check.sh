#!/bin/bash

# CONFIGURATION
MAGENTO_ROOT="/var/www/html/huislijnkant"
LOG_DIR="$MAGENTO_ROOT/health_logs"
DATESTAMP=$(date +'%Y-%m-%d_%H-%M')
LOG_FILE="$LOG_DIR/health_$DATESTAMP.log"

# Ensure log directory exists
mkdir -p "$LOG_DIR"

export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin

# Start Logging
{
echo "===== Magento Health Check ($DATESTAMP) ====="

/bin/hostnamectl | grep "Static hostname"

echo -e "\n[Uptime and Load]"
uptime

echo -e "\n[Memory Usage]"
free -h

echo -e "\n[Disk Usage]"
df -h

echo -e "\n[Top 10 Memory-Consuming Processes]"
ps -eo pid,ppid,cmd,%mem,%cpu --sort=-%mem | head -n 10

echo -e "\n[Service Status]"
for service in apache2 php8.2-fpm mysql redis elasticsearch rabbitmq-server; do
    if systemctl list-units --type=service | grep -q "$service"; then
        echo -n "$service: "
        systemctl is-active --quiet "$service" && echo "active" || echo "inactive"
    fi
done

echo -e "\n[Magento Logs]"
if [ -d "$MAGENTO_ROOT/var/log" ]; then
    echo -e "\nLast 20 lines of system.log:"
    /usr/bin/tail -n 20 "$MAGENTO_ROOT/var/log/system.log" 2>/dev/null || echo "system.log not found"

    echo -e "\nLast 20 lines of exception.log:"
    /usr/bin/tail -n 20 "$MAGENTO_ROOT/var/log/exception.log" 2>/dev/null || echo "exception.log not found"
else
    echo "Magento var/log directory not found."
fi

echo -e "\n===== End of Magento Health Report ====="

} > "$LOG_FILE"


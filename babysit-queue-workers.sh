#!/bin/bash

# Check the status of the queue worker
worker_status=$(supervisorctl status fungibl-worker | awk '{print $2}')

# If the worker is not running, start it
if [ "${worker_status}" != "RUNNING" ]; then
    supervisorctl start fungibl-worker:*
fi

# Check the log file for the queue worker
log_file="/path/to/your/laravel/project/storage/logs/worker.log"

# Get the timestamp of the most recent "processing" line in the log file
processing_timestamp=$(grep "processing" "$log_file" | tail -n 1 | awk '{print $1, $2}')

# If the worker is stuck processing a job for more than 15 minutes, restart it
if [[ $(date -d "$processing_timestamp" +%s) -lt $(date -d "15 minutes ago" +%s) ]]; then
    supervisorctl restart fungibl-worker:*
fi

#!/bin/bash
step=2
for (( i = 0; i < 60; i=(i+step) )); do
  php /var/www/html/st_flow_api/corn.php 3
  sleep $step
done
exit 0

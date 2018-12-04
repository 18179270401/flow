#!/bin/bash
step=6 #间隔的秒数，不能大于60
for (( i = 0; i < 60; i=(i+step) )); do
	php /var/www/html/st_flow_api/corn.php 2
	sleep $step
done
exit 0

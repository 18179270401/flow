#!/bin/bash
step=1
for (( i = 0; i < 60; i=(i+step) )); do
  curl http://localhost:8082/corn.php
  #php /html/st_flow_api/corn.php
  sleep $step
done
exit 0

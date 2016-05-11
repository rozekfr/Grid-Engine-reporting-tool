#!/usr/bin/env bash
while [ 1 ]
do
    qstat -F > qstat
    php -d open_basedir="" rrd_updater.php
	sleep 60
done

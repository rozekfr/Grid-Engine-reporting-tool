#!/usr/bin/env bash
while [ 1 ]
do
    qstat -s psz > pending_jobs
    sleep 60
done

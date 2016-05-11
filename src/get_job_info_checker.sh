#!/usr/bin/env bash

#vyřizuje žádosti na získání informací o úloze a uklízí po sobě

while [ 1 ]
do
    rm PJ_response_*
    sh PJ_requests.sh
    rm PJ_requests.sh
    sleep 15
done

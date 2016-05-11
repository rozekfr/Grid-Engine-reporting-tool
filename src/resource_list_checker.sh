#!/usr/bin/env bash

#vyřizuje žádosti na získání blokujících úloh a uklízí po sobě

while [ 1 ]
do
    rm RL_response_*
    sh RL_requests.sh
    rm RL_requests.sh
    sleep 15
done

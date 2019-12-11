#!/bin/bash

CURL='/usr/bin/curl'
CRON_HTTP="https://{$AUTH_USERNAME}:{$AUTH_PASSWORD}@{$CONNECT_DOMAIN}/cron/{$CRON_HASH}"
CURL_ARGS="-f -s -S -k"

# curl the URL
$CURL $CURL_ARGS $CRON_HTTP

#!/bin/bash

CURL='/usr/bin/curl'
CURL_ARGS="-f -s -S -k"

# Do we have an HTTP auth to use in the URL?
if [[ -z "${AUTH_USERNAME}" ]]; then
  HTTP_AUTH=''
else
  HTTP_AUTH="${AUTH_USERNAME}:${AUTH_PASSWORD}@"
fi

CRON_HTTP="https://${HTTP_AUTH}${CONNECT_DOMAIN}/cron/${CRON_HASH}"

# curl the URL
$CURL $CURL_ARGS $CRON_HTTP

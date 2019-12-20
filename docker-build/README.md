# Docker build 

For a cleanish and encapsulated build 

Typically run like 

```
DOCKER_BUILDKIT=1 docker build -t nlc-drupal -f docker-build/Dockerfile .
```

*As of current docker version (19.03) the DOCKER_BUILDKIT is used to allow per build docker ignore files 

The overall process is as follows 

- Something runs composer (either a developer locally or else a concourse pipeline) for PHP installs
(* and then ny npm installs - TODO) to get the checked out version fully setup, so as this build can create an
immutable build

Env vars it will need can be supplied to the container - they are DB_HOST DB_USER DB_PASSWORD DB_NAME DB_DRIVER PHP_TIMEZONE 

The salesforce environment settings / auth config will also need applying as will s3fs to make it usable




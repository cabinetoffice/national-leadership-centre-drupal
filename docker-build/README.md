# Docker build 

For a cleanish and immutable build 

Typically run like this for a local build

`% DOCKER_BUILDKIT=1 docker build -t nlc-drupal -f docker-build/Dockerfile .`

*As of current docker version (19.03) the DOCKER_BUILDKIT is used to allow per build docker ignore files 

The overall process is as follows 

- Something runs composer (either a developer locally or else a concourse pipeline) for PHP installs
(* and then ny npm installs - TODO) to get the checked out version fully setup, so as this build can create an
immutable build.

in the build pipeline in concourse it is used slightly differently - the docker file here and the ignore file are 
copied into the root directory as Dockerfile and .dockerignore and used from there.

Env vars it will need can be supplied to the container at run time - see the settings files like web/sites/defaul/settings.prod-eks.php

The settings file is selected based on the NLC_ENVIRONMENT env var 

The salesforce environment settings / auth config will also need applying as will s3fs to make it usable




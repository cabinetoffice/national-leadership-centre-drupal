# Docker build 

For a cleanish and immutable build 

This build runs in two stages - stage 1 is a concourse pipeline (see pipelines in [https://github.com/cabinetoffice/national-leadership-centre-deployment](deployment repo) which sets up 
a base php image. Following that this 

Typically run like this for a local build

`% DOCKER_BUILDKIT=1 docker build -t nlc-drupal -f docker-build/Dockerfile .`

(note - the build expects a version file as build-version.txt in the root which gets added by
concourse - this will need manually adding for the local build. it's just informational so `touch build-version.txt` 
that file there)

*As of current docker version (19.03) the DOCKER_BUILDKIT is used to allow per build docker ignore files 

The overall process is as follows 

- Something runs composer (either a developer locally or else a concourse pipeline) for PHP installs
(* and then ny npm installs - TODO) to get the checked out version fully setup, so as this build can create an
immutable build.

in the build pipeline in concourse it is used slightly differently - the docker file here and the ignore file are 
copied into the root directory as Dockerfile and .dockerignore and used from there but it's pretty much the same thing otherwise.

Env vars it will need can be supplied to the container at run time - see the settings files like web/sites/defaul/settings.prod-eks.php

The settings file is selected based on the NLC_ENVIRONMENT env var - see main settings.php file for details



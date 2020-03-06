#!/bin/sh

set -eu

if ! [ -x "$(command -v pigz)" ]; then
    apk add --no-cache pigz
fi

PHP_TAG=${PHP_TAG:-"7.4-cli-alpine3.11"}
LAYERS_TXT=${LAYERS_TXT:-"/tmp/layers.txt"}
CACHE_TAR_GZ=${CACHE_TAR_GZ:-"cache.tar.gz"}

if [ "$COMPOSE_DOCKER_CLI_BUILD" = "1" ]; then
    docker image ls -qa --filter "since=php:$PHP_TAG" > "$LAYERS_TXT"
else
    docker-compose build composer | grep '\-\-\->' | grep -v 'Using cache' | sed -e 's/[ >-]//g' > "$LAYERS_TXT"
fi

# shellcheck disable=SC2046
docker save $(cat "$LAYERS_TXT") | pigz --fast > "$CACHE_TAR_GZ"

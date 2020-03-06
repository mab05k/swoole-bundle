#!/bin/sh

set -eu

if ! [ -x "$(command -v pigz)" ]; then
    apk add --no-cache pigz
fi

PHP_TAG=${PHP_TAG:-"7.4-cli-alpine3.11"}
LAYERS_TXT=${LAYERS_TXT:-"/tmp/layers.txt"}
CACHE_TAR_GZ=${CACHE_TAR_GZ:-"cache.tar.gz"}

if [ -f "$CACHE_TAR_GZ" ]; then
    pigz -dc "$CACHE_TAR_GZ" | docker load
    docker images
fi

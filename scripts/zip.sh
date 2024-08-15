#!/bin/bash

version=$(jq -r .version package.json)

zip -r wordpress-plugin-"$version".zip \
  src \
  vendor \
  wordpress-plugin.php \
  CHANGELOG.md \
  composer.json \
  composer.lock \
  package.json \
  README.md \
  readme.txt \
  yarn.lock
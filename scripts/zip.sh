#!/bin/bash

version=$(jq -r .version package.json)

mkdir -p releases

zip -r releases/wordpress-plugin-boilerplate-"$version".zip \
  src \
  vendor \
  wordpress-plugin-boilerplate.php \
  CHANGELOG.md \
  composer.json \
  composer.lock \
  package.json \
  README.md \
  readme.txt \
  yarn.lock
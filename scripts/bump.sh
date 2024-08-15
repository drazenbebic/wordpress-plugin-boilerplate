#!/bin/bash

new_version=$1

# Get the current version from the plugin file
current_version=$(cat wordpress-plugin.php | grep -o -P 'Version: ^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)(?:-((?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?(?:\+([0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?$')

# Replace the version in the plugin file
sed -i "s/Version: $current_version.*/Version: $new_version/g" wordpress-plugin.php

# Update the version in the readme.txt
sed -i "s/Stable tag: $current_version.*/Stable tag: $new_version/g" readme.txt

# Update the version in composer.json and package.json
jq ".version = \"$new_version\"" package.json > temp-package.json && mv temp-package.json package.json
jq ".version = \"$new_version\"" composer.json > temp-composer.json && mv temp-composer.json composer.json

composer update
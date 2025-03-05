#!/bin/bash

# To update this just copy-paste the newer version from the WordPress Release
# Archive: https://wordpress.org/download/releases/
WORDPRESS_ZIP="https://wordpress.org/wordpress-6.7.2.zip"

# Skip if the "wordpress" directory exists
if [ -d "wordpress" ]; then
  echo "⚠️ The 'wordpress' folder already exists. Skipping download and extraction."
  exit 0
fi

echo "🔌 Downloading and extracting WordPress..."
curl -o wordpress.zip "$WORDPRESS_ZIP" && \
unzip -oq wordpress.zip -d ./ && \
rm -rf wordpress.zip && \
echo "✅ WordPress successfully downloaded & extracted." || \
echo "❌ An error occurred while downloading and extracting WordPress"
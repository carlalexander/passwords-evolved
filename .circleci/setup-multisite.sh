#!/usr/bin/env bash

if [[ -z "$CIRCLECI" ]]; then
    echo "This script can only be run by CircleCI. Aborting." 1>&2
    exit 1
fi

if [[ -z "$WP_CORE_DIR" ]]; then
    echo "WordPress core directory isn't set. Aborting." 1>&2
    exit 1
fi

if [[ -z "$WP_HOST" ]]; then
    echo "WordPress host isn't set. Aborting." 1>&2
    exit 1
fi

if [[ -z "$WP_ORG_PLUGIN_NAME" ]]; then
    echo "WordPress.org plugin name not set. Aborting." 1>&2
    exit 1
fi

# Install WordPress
vendor/bin/wp config create --path="${WP_CORE_DIR}" --dbhost="127.0.0.1" --dbname="circle_test" --dbuser="root"
vendor/bin/wp core multisite-install --path="${WP_CORE_DIR}" --url="http://${WP_HOST}" --title="Passwords Evolved Test" --admin_user="admin" --admin_password="password" --admin_email="admin@example.com"
vendor/bin/wp rewrite structure --path="${WP_CORE_DIR}" '/%postname%/'

# Copy our plugin to WordPress directory
cp -r ./ ${WP_CORE_DIR}/wp-content/plugins/${WP_ORG_PLUGIN_NAME}

# Activate our plugin
vendor/bin/wp plugin activate --network --path="${WP_CORE_DIR}" ${WP_ORG_PLUGIN_NAME}

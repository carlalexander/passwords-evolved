#!/usr/bin/env bash

if [[ -z "$CIRCLECI" ]]; then
    echo "This script can only be run by CircleCI." 1>&2
    exit 1
fi

if [[ -z "$WP_ORG_PASSWORD" ]]; then
    echo "WordPress.org password not set." 1>&2
    exit 1
fi

if [[ -z "$CIRCLE_BRANCH" || "$CIRCLE_BRANCH" != "master" ]]; then
    echo "Build branch is required and must be 'master' branch." 1>&2
    exit 0
fi

PLUGIN="passwords-evolved"
PLUGIN_SVN_PATH="/tmp/svn"
WP_ORG_USERNAME="carlalexander"

# Checkout the SVN repo
svn co -q "http://svn.wp-plugins.org/$PLUGIN" $PLUGIN_SVN_PATH

# Delete the assets directory
rm -rf $PLUGIN_SVN_PATH/assets

# Copy our plugin assets as the new assets directory
cp -r ./assets $PLUGIN_SVN_PATH/assets

# Move to SVN directory
cd $PLUGIN_SVN_PATH

# Add new files to SVN
svn stat | grep '^?' | awk '{print $2}' | xargs -I x svn add x@

# Remove deleted files from SVN
svn stat | grep '^!' | awk '{print $2}' | xargs -I x svn rm --force x@

# Commit to SVN
svn ci --no-auth-cache --username $WP_ORG_USERNAME --password $WP_ORG_PASSWORD -m "Deploy new assets"

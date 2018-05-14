#!/usr/bin/env bash

if [[ -z "$CIRCLECI" ]]; then
    echo "This script can only be run by CircleCI. Aborting." 1>&2
    exit 1
fi

if [[ -z "$CIRCLE_BRANCH" || "$CIRCLE_BRANCH" != "master" ]]; then
    echo "Build branch is required and must be 'master' branch. Stopping deployment." 1>&2
    exit 0
fi

if [[ -z "$WP_ORG_PASSWORD" ]]; then
    echo "WordPress.org password not set. Aborting." 1>&2
    exit 1
fi

if [[ -z "$WP_ORG_PLUGIN_NAME" ]]; then
    echo "WordPress.org plugin name not set. Aborting." 1>&2
    exit 1
fi

if [[ -z "$WP_ORG_USERNAME" ]]; then
    echo "WordPress.org username not set. Aborting." 1>&2
    exit 1
fi

PLUGIN_BUILD_DIRECTORIES=(lib resources src)
PLUGIN_BUILD_FILES=(index.php LICENSE passwords-evolved.php pluggable.php readme.txt)
PLUGIN_BUILD_PATH="/tmp/build"
PLUGIN_SVN_PATH="/tmp/svn"

# Figure out the most recent git tag
LATEST_GIT_TAG=$(git describe --tags `git rev-list --tags --max-count=1`)
# Remove the "v" at the beginning of the git tag
LATEST_SVN_TAG=${LATEST_GIT_TAG:1}

# Check if the latest SVN tag exists already
TAG=$(svn ls "https://plugins.svn.wordpress.org/$WP_ORG_PLUGIN_NAME/tags/$LATEST_SVN_TAG")
error=$?
if [ $error == 0 ]; then
    # Tag exists, don't deploy
    echo "Latest tag ($LATEST_SVN_TAG) already exists on the WordPress directory. No deployment needed!"
    exit 0
fi

# Checkout the git tag
git checkout tags/$LATEST_GIT_TAG

# Create the build directory
mkdir $PLUGIN_BUILD_PATH

# Copy plugin directories to the build directory
for DIRECTORY in "${PLUGIN_BUILD_DIRECTORIES[@]}"; do
    cp -r $DIRECTORY $PLUGIN_BUILD_PATH/$DIRECTORY
done

# Copy plugin files to the build directory
for FILE in "${PLUGIN_BUILD_FILES[@]}"; do
    cp $FILE $PLUGIN_BUILD_PATH/$FILE
done

# Checkout the SVN repo
svn co -q "http://svn.wp-plugins.org/$WP_ORG_PLUGIN_NAME" $PLUGIN_SVN_PATH

# Move to SVN directory
cd $PLUGIN_SVN_PATH

# Delete the trunk directory
rm -rf ./trunk

# Copy our new version of the plugin as the new trunk directory
cp -r $PLUGIN_BUILD_PATH ./trunk

# Copy our new version of the plugin into new version tag directory
cp -r $PLUGIN_BUILD_PATH ./tags/$LATEST_SVN_TAG

# Add new files to SVN
svn stat | grep '^?' | awk '{print $2}' | xargs -I x svn add x@

# Remove deleted files from SVN
svn stat | grep '^!' | awk '{print $2}' | xargs -I x svn rm --force x@

# Commit to SVN
svn ci --no-auth-cache --username $WP_ORG_USERNAME --password $WP_ORG_PASSWORD -m "Deploy version $LATEST_SVN_TAG"

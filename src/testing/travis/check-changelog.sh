#!/bin/bash
# Conventional Changelog Enforcer Script
# Copyright Siemens AG 2017
#
# Copying and distribution of this file, with or without modification,
# are permitted in any medium without royalty provided the copyright
# notice and this notice are preserved.  This file is offered as-is,
# without any warranty.
#
# Description: Conventional Changelog Enforcer Script
# Checks if last commit, conforms to the Conventional Changelog
# https://github.com/fossology/fossology/blob/master/CONTRIBUTING.md#user-content-git-commit-conventions

set -e
curl https://raw.githubusercontent.com/creationix/nvm/v0.33.1/install.sh | bash
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"  # This loads nvm
nvm install v6
npm install --save-dev conventional-changelog-lint
cd node_modules/conventional-changelog-lint
npm install
npm run commitlint --from=master

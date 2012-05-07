#!/bin/sh

git filter-branch --env-filter '

export GIT_AUTHOR_NAME="Donis"
export GIT_AUTHOR_EMAIL="donisa+github@gmail.com"
export GIT_COMMITTER_NAME="Donis"
export GIT_COMMITTER_EMAIL="donisa+github@gmail.com"
'

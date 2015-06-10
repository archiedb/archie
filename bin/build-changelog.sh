#!/bin/bash
ABS_PATH=$(cd `dirname "${BASH_SOURCE[0]}"` && pwd)
git log --no-decorate --no-merges --format=oneline | awk '{$1=""; print$0}' > ${ABS_PATH}/../docs/CHANGELOG

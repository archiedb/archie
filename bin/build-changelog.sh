#!/bin/bash
git log --no-decorate --no-merges --format=oneline | awk '{$1=""; print$0}' > ../docs/CHANGELOG

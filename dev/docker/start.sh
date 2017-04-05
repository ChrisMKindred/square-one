#!/bin/bash

SCRIPTDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
cd "$SCRIPTDIR";

PROJECT_ID="square1"

if [[ $1 == "sync" ]]; then
  docker-compose --project-name=${PROJECT_ID} -f docker-compose.yml -f docker-compose.unison.yml up -d
else
	docker-compose --project-name=${PROJECT_ID} up -d
fi


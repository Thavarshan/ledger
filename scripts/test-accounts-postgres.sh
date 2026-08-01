#!/usr/bin/env bash

set -euo pipefail

compose_file="compose.account-tests.yml"

docker compose --file "$compose_file" up --detach --wait postgres
trap 'docker compose --file "$compose_file" down --volumes' EXIT

php vendor/bin/phpunit --configuration=phpunit.account.xml --no-coverage "$@"

#!/usr/bin/env bash
php app/console cache:clear --no-warmup
php app/console cache:clear --env=prod
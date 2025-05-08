#!/bin/bash
export $(grep -v "^#" .env.reverb | xargs)
php artisan reverb:start --host=127.0.0.1 --port=8090

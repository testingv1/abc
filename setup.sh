#!/bin/bash

cd web &&
echo '# installing packages...'
composer install &&
cd ../ &&
echo '# starting containers...'
docker-compose stop &&
docker-compose rm -f &&
docker-compose up -d &&
cd web &&
echo '# running migrations...' &&
sleep 10 &&
composer run-migrations &&
echo '# running tests...' &&
composer test &&
echo '# you may access the api at http://localhost/'

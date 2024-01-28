#!/bin/bash
docker exec -it $(docker ps | grep php-apache | cut -c 1-5) /bin/bash
#!/usr/bin/env bash

extip=$(php artisan server:startdns --onlyprintip)

sudo php artisan server:startdns "${extip}" &
serverpid=${!}

echo "server pid=$serverpid"
echo

sleep 1

dig google.de @"$extip" &
digpid=${!}

sleep 4

echo
echo "killing $serverpid $digpid ..."
sudo kill -9 $serverpid $digpid

sleep 1
sudo pkill php

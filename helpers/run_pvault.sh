#!/bin/sh
#
#
# Run Perfumer's Vault
# Script Version: v1.4
# Author: John Belekios <john@globaldyne.co.uk>
#
#
PVDIR=$HOME/Documents/pvault_pro.nosync
LEGACYDIR=$HOME/Documents/pvault_pro
DOCKER_BIN=$(which docker)

echo 'Checking for legacy data...'
if [ -d $LEGACYDIR ]; then
    echo 'Lecacy data dir found, migrating...'
    mv $LEGACYDIR $PVDIR
fi


echo "Checking if Docker is up and runnning..."
$DOCKER_BIN info --format "{{.OperatingSystem}}" | grep -q "Docker\|Linux" 

if [[ $? -eq 0 ]]; then
	#Check if required local path exists and create if not

	if [ ! -d $PVDIR ]; then
		echo "$PVDIR not exists, creating it..."
		mkdir -p $PVDIR
	fi
	
	echo "Trying to remove an already running container..."
	$DOCKER_BIN rm PV2 --force
	
	echo "Starting up...Please wait, this might take a while..."
	$DOCKER_BIN run --name PV2 -p 8080:80 -v $PVDIR/config:/config -v $PVDIR/db:/var/lib/mysql -v $PVDIR/uploads:/var/www/html/uploads globaldyne/jbvault:latest

else
    clear
    echo "Docker not detected. Please make sure you have Docker installed and is up and running."
    echo "You can download Docker from: https://docs.docker.com/"
fi


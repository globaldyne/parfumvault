#!/bin/sh
#
#
# Run Perfumer's Vault
# Script Version: v1.5
# Author: John Belekios <john@globaldyne.co.uk>
#
#
DOCKER_BIN=$(which docker)
CONTAINER=PV2
LOCAL_PORT=8080
IMAGE_TAG=latest


echo "Checking if Docker is up and runnning..."
$DOCKER_BIN info --format "{{.OperatingSystem}}" | grep -q "Docker\|Linux" 

if [[ $? -eq 0 ]]; then
	#Check if required local path exists and create if not
	
	echo "Trying to remove an already running container..."
	$DOCKER_BIN rm ${CONTAINER} --force
	
	echo "Starting up ${CONTAINER}... Please wait, this might take a while..."
	$DOCKER_BIN run --name ${CONTAINER} -p ${LOCAL_PORT}:80 -v ${CONTAINER}_VOL_CONF:/config -v ${CONTAINER}_VOL_DB:/var/lib/mysql -v ${CONTAINER}_VOL_UPLOADS:/var/www/html/uploads globaldyne/jbvault:${IMAGE_TAG}

else
    clear
    echo "Docker not detected. Please make sure you have Docker installed and is up and running."
    echo "You can download Docker from: https://docs.docker.com/"
fi

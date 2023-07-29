#!/bin/sh
#
#
# Run Perfumer's Vault
# Script Version: v1.6
# Author: John Belekios <john@globaldyne.co.uk>
#
#
DOCKER_BIN=$(which docker)
TMP_DIR_PV=$(dirname $(mktemp -u))
CONTAINER=pvault
COMPOSE_FILE=compose.yaml
REPO=https://raw.githubusercontent.com/globaldyne/parfumvault/master/docker-compose/$COMPOSE_FILE

echo "Checking if Docker is up and runnning..."
$DOCKER_BIN info --format "{{.OperatingSystem}}" | grep -q "Docker\|Linux"

if [[ $? -eq 0 ]]; then
	#Check if required local path exists and create if not
	echo "Trying to remove an already running container..."
	$DOCKER_BIN compose -p ${CONTAINER} down
    rm -f $TMP_DIR_PV/$COMPOSE_FILE
		
	echo "Fetching compose file..."
	wget -q -O $TMP_DIR_PV/$COMPOSE_FILE $REPO
	echo "Starting up ${CONTAINER}... Please wait, this might take a while..."
	$DOCKER_BIN compose -f $TMP_DIR_PV/$COMPOSE_FILE -p ${CONTAINER} up -d

else
    clear
    echo "Docker not detected. Please make sure you have Docker installed and is up and running."
    echo "You can download Docker from: https://docs.docker.com/"
fi
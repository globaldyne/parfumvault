#!/bin/bash
#
#
# Install and run Perfumer's Vault
# Script Version: v1.0 
# Author: John Belekios <john@globaldyne.co.uk>
#
#
#
#
#Config
PVDIR=~/Documents/pvault_pro
TAG=latest


#Check if Docker is up and runnning
docker info --format "{{.OperatingSystem}}" | grep -q "Docker" 

if [[ $? -eq 0 ]]; then
	#Check if required local path exists and create if not

	if [ ! -d $PVDIR ]; then
		echo "$PVDIR not exists, creating it..."
		mkdir -p $PVDIR
	fi
	#Get the image and start it
	docker run --name PV2 -p 8080:80 -v $PVDIR/config:/config -v $PVDIR/db:/var/lib/mysql -v $PVDIR/uploads:/var/www/html/uploads globaldyne/jbvault:$TAG

else
    clear
    echo "Docker not detected. Please make sure you have Docker installed and is up and running."
    echo "You can download Docker from: https://docs.docker.com/docker-for-mac/install/"
fi



# Perfumers Vault

A sophisticated tool to help perfumers organize their formulas, ingredients and inventory.

This is a FREE software provided as is without ANY warranty under MIT license.

[![Current Release](https://img.shields.io/github/v/release/globaldyne/parfumvault.svg "Current Release")](https://github.com/globaldyne/parfumvault/releases/latest) [![PayPal](https://img.shields.io/badge/donate-PayPal-green.svg)](https://paypal.me/jbparfum) 
![Discord](https://img.shields.io/discord/1238069309356638217)


# Features 
* Formulae management
* Formulae comparison
* Formulae revisions
* Ingredient management
* Suppliers list
* Customers support
* Generate finished product and its limits
* Generate paperwork for finished products 
* Option to export a formula data to sell/share it 
* Pyramid olfactory view
* Cost calculation
* IFRA limits calculation
* Label printing
* Formula export
* CSV Import for formulas and ingredients
* IFRA Library
* Import IFRA xls
* Validate ingredients in formula, against the IFRA library
* Multiple suppliers per ingredient
* Calculate costs against specific supplier
* Fetch the price automatically (Depends on suppliers platform)

For full features list please visit
	
	https://www.perfumersvault.com/knowledge-base/


# Docker Image

To run the latest docker image (scripted):

	sh -c "$(curl -fsSL https://raw.githubusercontent.com/globaldyne/parfumvault/master/helpers/run_pvault.sh)"

or manually:
	
	docker run --name pvault -e PLATFORM=CLOUD -e DB_HOST=... -p 8000:8000 -d globaldyne/perfumersvault

Please note, all DB_ variables are required.

	- `-e PLATFORM=CLOUD`
	- `-e DB_HOST=...`
	- `-e DB_NAME=...`
	- `-e DB_USER=...`
	- `-e DB_PASS=...`
	- `-e phpMyAdmin=false`
	- `-e MAX_FILE_SIZE=4194304`
	- `-e TMP_PATH=/tmp/`
	- `-e FILE_EXT='pdf, doc, docx, xls, csv, xlsx, png, jpg, jpeg, gif'`
	- `-e DB_BACKUP_PARAMETERS='--column-statistics=1'`

or via docker compose

	---
	version: '3.8'
	services:
	  pvdb:
	    image: mariadb:10.5
	    command: '--default-authentication-plugin=mysql_native_password --innodb-flush-method=fsync'
	    volumes:
	      - db_data:/var/lib/mysql
	    restart: always
	    environment:
	      MYSQL_ROOT_PASSWORD: pvault
	      MYSQL_DATABASE: pvault
	      MYSQL_USER: pvault
	      MYSQL_PASSWORD: pvault
	    expose:
	      - 3306
	  pvault:
	    image: globaldyne/perfumersvault:latest
	    ports:
	      - 8000:8000
	    restart: always
	    environment:
	      PLATFORM: CLOUD
	      DB_HOST: pvdb
	      DB_USER: pvault
	      DB_PASS: pvault
	      DB_NAME: pvault
	      MAX_FILE_SIZE: 4194304
	      TMP_PATH: /tmp/
	      FILE_EXT: 'pdf, doc, docx, xls, csv, xlsx, png, jpg, jpeg, gif'
	      DB_BACKUP_PARAMETERS: '--column-statistics=1'
	volumes:
	  db_data:



then point your browser to http://localhost:8000

For more info, please refer to:
	
	https://www.perfumersvault.com/knowledge-base/



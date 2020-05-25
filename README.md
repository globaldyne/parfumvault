# JBs Perfumers Vault

A simple tool to help perfumers organize their formulas and ingredients.

This is a FREE software provided as is without ANY warranty under MIT license.

# Features 
* Formulae management
* Ingredient management
* Suppliers list
* Auto generate TGSC links based to the ingredient name or CAS number
* Pyramid olfactory view
* Cost calculation
* IFRA limits calculation
* Label printing
* Formula export
* CSV Import
* IFRA Library
* Import IFRA xls
* Auto search IFRA Library for you ingredients and retrieve Cat4 limit


# Docker Image

To run from docker:

    docker run -p 8080:80  -v <yourpath>/local/db:/var/lib/mysql -v <yourpath>local/uploads:/var/www/html/uploads globaldyne/jbvault:latest

then point your browser to http://localhost:8080


![screen1](/screenshots/screen1.png) 
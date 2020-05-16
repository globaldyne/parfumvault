# JBs Parfum Vault

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

# Docker Image

To run from docker:

    docker run -p 8080:80  -v <yourpath>/localdb:/var/lib/mysql -v <yourpath>localupload:/var/www/html/uploads globaldyne/jbvault:latest

then point your browser to http://localhost:8080

# Screenshots 

![screen1](/screenshots/screen1.png) 
![screen2](/screenshots/screen2.png)
![screen3](/screenshots/screen3.png) 
![screen4](/screenshots/screen4.png)
![screen3](/screenshots/screen5.png) 
![screen4](/screenshots/screen6.png)
![screen7](/screenshots/screen7.png)

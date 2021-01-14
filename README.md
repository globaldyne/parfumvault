# JBs Perfumers Vault

A sophisticated tool to help perfumers organize their formulas, ingredients and inventory.

This is a FREE software provided as is without ANY warranty under MIT license.

# Features 
* Formulae management
* Ingredient management
* Suppliers list
* Customers support
* Generate finished product and its limits
* Generate paperwork for finished products 
* Option to export a formula data to sell/share it 
* Auto generate TGSC links based to the ingredient name or CAS number
* Pyramid olfactory view
* Cost calculation
* IFRA limits calculation
* Label printing
* Formula export
* CSV Import for formulas and ingredients
* IFRA Library
* Import IFRA xls
* Validate ingredients in formula, against the IFRA library



# Docker Image

To run the latest docker image (scripted):

	sh -c "$(curl -fsSL https://raw.githubusercontent.com/globaldyne/parfumvault/master/helpers/run_pvault.sh)"

or manually:
	
	docker run --name PV2 \
		-p 8080:80 \
		-v PVDIR/config:/config \
		-v PVDIR/db:/var/lib/mysql \
		-v PVDIR/uploads:/var/www/html/uploads \
		globaldyne/jbvault

then point your browser to http://localhost:8080

For more info, please refer to:
	
	https://www.jbparfum.com/knowledge-base/

![screen1](/screenshots/dashboard.png) 

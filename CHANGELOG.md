# CHANGELOG
### Version 2.4
- ADD: PubChem - exclude Mixtures and Blends
- ADD: Default purity value '100' in ingredient management
- ADD: Favicon added!
- CHG: Add new formula page now opens as a modal window
- CHG: Allergen has been renamed to 'Declare'
- IMP: Increase the size of the pop-up window when showing ingredient data

### Version 2.3
- ADD: Add a new dashboard
- ADD: Option to generate related documents to sell a formula
- ADD: Auto ingredient create when edit it
- IMP: Add/Edit ingredient page is now merged
- IMP: Improve PDF export functionality
- CHG: Customers page moved under inventory section for better management
- FIX: Fix a validation issue in finished product page
- FIX: Minor improvements in allegens page
- FIX: Various minor bug fixes and code clean-up

### Version 2.2
- ADD: Prohibition and Specification in ingredients usage classification
- ADD: Release notes
- ADD: Option to choose category class
- UPD: Remove IFRA from ingredients
- UPD: Ingredients input validation
- CHG: Change limitation format in ingredients page
- FIX: Various minor fixes and improvements

### Version 2.1.2
- ADD: Making formula page aSync
- ADD: Custom amount of formula to make
- ADD: Solvent in ingredient level
- ADD: Ingredients CSV import support
- ADD: Formula quantity decimal step
- CHG: Move suppliers from settings page under inventory
- FIX: Formula not showning properly when new
- FIX: Modal window close on bg click
- FIX: Various minor fixes and improvements

### Version 2.1.1
- ADD: Privacy notice for the PV Online services
- ADD: Usage level type
- ADD: Prevent credentials to be saved if are wrong/inactive for PV Online
- FIX: A bug preventing PubChem pic to show correctly
- FIX: Purity not showing correctly in cart
- FIX: A bug preventing allergens create
- FIX: Allergen import from PV Online


### Version 2.0.9
- ADD: Purity percentage in cart
- FIX: Show an error in impact and pyramid view if no ingredients

### Version 2.0.8
- ADD: Integration with PV Online
- ADD: Barcode generation in batch file
- ADD: Show CAS number in formula
- ADD: Dilutant field in finished product page
- ADD: Include allergen ingredients in batch pdf file
- ADD: Ingredient properties in formula view
- ADD: Short categories/profiles/types by name when adding or editing an ingredient
- UPD: Rename To Do -> To Make in formula view
- ADD: Search and paging in allergens view
- ADD: Include full list of alergens in generated pdf
- ADD: Allergens page improvements
- ADD: Upload ingredients to PV Online
- ADD: Quantity of product to purchase in cart page
- ADD: Add LOG/P in ingredients
- ADD: Advanced note impact in ingredients page
- ADD: Notes impact in formulas
- PRV: Option to exclude notes when uploading ingredients to PV Online
- CHG: Moving Olfactory Pyramid view to a tab in the formula page
- FIX: Not showing allergens correctly in label
- FIX: Message position in Generate Finished Product page
- FIX: Various bug fixes
- FIX: A bug caused incorrect error/success messages

### Version 2.0.7
- ADD: PV Maker integration (Experimental function)
- ADD: Ingredient solubility and impact
- FIX: Cart/notification menus
- FIX: A bug preventing new formulas for displaying if contains no ingredients


### Version 2.0.6
- ADD: Cart
- ADD: Make formula
- ADD: DB schema upgrades
- ADD: Ingredients data extend
- ADD: FEMA support
- ADD: PubChem data support
- Split edit ingredient data by category
- Various fixes

### Version 2.0.5
- ADD: Formula calculation tools

### Version 2.0.1
- ADD: Progress bar to indicate noes level usage
- ADD: Ingredient profile in list
- UPDATE: About page
- FIX: Various UI improvements
- Code Clean up

### Version 2.0.0
- ADD: IFRA Lirary support
- ADD: IFRA insights
- ADD: IFRA lookup first then local db
- ADD: Ingredient odor
- ADD: Formula sex (men,women,unisex)
- ADD: Authentication
- ADD: User management
- FIX: SDS Upload function
- FIX: Metadata duplicate entries
- FIX: Cost calculation to include strength %
- FIX: Concentration calculation to include strength %
- FIX: Total cost calculation
- ADD: Product Branding
- ADD: User Avatar
- ADD: Multiply or divide all formula quantity
- ADD: Pyramid values can now be set in settings page
- ADD: New Dashboard layout
- ADD: Purity levels


### Version 0.1.7
- FIX: Settings broken view
- FIX: MySql schema

### Version 0.1.6
- ADD: DataTables
- ADD: Search functionality
- ADD: Bootstrap table pagination (old method removed)
- DEL: Ingredients profile add/delete page - to protect pyramid view
- ADD: Formula type (woody, fresh, etc)

### Version 0.1.5
- FIX: Version check function
- ADD: Backup / Restore DB
- ADD: Display warning if missing formulas/ingredients
		
### Version 0.1.4
- FIX: Various bug fixes
- ADD: Check if ingredient is missing from the database
- ADD: Show missing data from an ingredeint in formula
- ADD: Check if ingredient is in use by any formula before deleting
- ADD: Check if ingridient exists in db before edit
- ADD: Ingredients usage insights
- ADD: New version check

### Version 0.1.3
- FIX: CSV import when missing concentration
- ADD: Display ingredients in Pyramid view

### Version 0.1.2
- ADD: CSV import function
- FIX: metadata delete

### Version 0.1.1 
- ADD: Checking of critical ingredient data missing
- ADD: Pyramid view

### Version 0.1.0
- FIX: upload and tmp directory structure

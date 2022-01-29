# CHANGELOG
### Version 4.0

### Version 3.9
- Fix a bug showing empty inventory column when exporting a formula
- Improve formula is made feature
- Add fixed header in formula view
- Dashboard pie now includes any extra formula categories
- Dashboard pie colors can be customised by formula category color
- Update Chart.js to its latest version
- Change legend position and color mapping for pie charts
- Rename Manufactured field in suppliers to Purchased
- Fix date picker missing left/right arrows
- Update jQuery version to 3.6.0
- Fix a bug preventing proper value update in ingredient suppliers
- Rewrite of suppliers page in ingredient level
- Rewrite of documents page in ingredient level
- Rewrite of compositions page in ingredient level
- Rewrite of ingredient management page
- Cosmetic changes in view formula 
- Add ingredients json
- Exclude/include ingredient from formula
- Add copy to clipboard function for CAS in formula view
- Make formula view responsive
- Multi dimension ingredient look up performance improvement
- Details link moved under menu in formula view
- Improve add lid page
- Improve bottle page

### Version 3.8
- tipsy.js dependency removed
- Major update in formula view to fully populate DataTables
- Major update in formula list to fully populate DataTables
- Major update in ingredients category list to fully populate DataTables
- Add support for custom categories in formulas
- PDF export improvements
- PV Online can now be disabled/enabled when configured
- Fix a bug preventing DB upgrade when online update is disabled
- Various bug fixes and improvements
- Extend run_pvault.sh to work in Linux enviroments
- Add ingredient stock managment

### Version 3.7
- Delete history log when formula is deleted
- Added final type when adding a new formula
- Added purpoce category when adding a new formula
- Fix a bug preventing showing the corrct final type in formula details
- Added EINECS (EC) for ingredient compositions
- Remove ingredient name from multi dimension lookup, validate only by CAS number
- Enable formula lock/unlock by clicking in lock pad

### Version 3.6
- Prevent background formula changes when protected mode is enabled
- Fixed Where Used being displayed when adding new ingredient
- Added a function to convert a formula to ingredient
- Fix an issue preventing users to login when in sub domain
- Allergens tab in ingredients has now be renamed to composition (UI Only)
- Keep history for formula changes

### Version 3.5
- Formula can now be grouped by category
- Added ingredient clone option
- Added GHS for ingredients
- Fix a bug preventing iOS app to sync properly
- Add formula comparison
- Group formulation menu links
- Add Export as PDF in formulas
- Add Formula revision system
- Add Accord creation from formulas notes
- Add Where ingredient used
- Add final concentration in formula
- Improvements in multi dimension limit lookup
- Increase precision in formulas

### Version 3.4
- REST API added
- DB schema ingredients category chnage from char to int
- Category migration script added in helpers
- DB and main app update process seperated
- Grams added as a measurement unit
- Extend PV Online api to upload categories as a part of ingredient data
- Added category color key
- Improve PV Online upload function
- Ingredient management page improvements
- Set minimum 8 chars length for API key
- Overall code improvements especially for ingredients and categories pages
- Added card view in ingredients

### Version 3.3
- Show CAS and INCI when listing ingredients in formula page
- All custom usage limits set to "Recommendation" by default
- Custom usage limits will return info from now on instead of warning
- Custom usage limits will not prevent IFRA certificate to be generated
- TGSC integrations removed
- Adding advanced search in ingredients
- Adding search engine modules, so you can directly search a supplier for a material and import it in the local DB
- Added link to the app store for PV Light app

### Version 3.2
- Fixed a bug preventing formula duplication
- Ingredients listing pagination
- Custom search in ingredients

### Version 3.1
- Adding documents for ingredients
- Migration script added
- Formula picture is now stored in DB

### Version 3.0
- Add multiple suppliers per ingredient
- Price scrapper added - scrape price data from your suppliers web page
- Added ingredient physical state (Solid/Liquid)
- Ingredients supplier can be chosen when generating a finished formula
- Change image storage type for ingredient categories to LONGBLOB
- Improve ingredient mamage page by adding ingredient overview
- Logo update
- Update  pubchem api
- Removed edit options when formula is protected
- Bug fixes and Various improvements
- Added physical state icon in ingredient overview
- FIX an issue preventing uploading SDS when file too big
- Add profile image instead of name in ingredients list
- Merge ingredient name and INCI
- Improve csv export in ingredients page
- Ingredient profile replaced by INCI when available otherwise CAS number is shown when adding ingredients in a formula
- Adding purpose/category per formula
- Quering using ID where possible instead of names
- Added CAS in ingredient label
- Sell formula improvements

### Version 2.9
- Label printing improvements
- Passing name is now encoded in base64 when requesting it
- Add reach number
- Remove css dependencies from viewSummary.php
- Add option to view back label of finished product as a text
- Select which ingredients will be shown in summary view
- Add product name in formulas listing
- Choose to add missing ingredients when importing from CSV
- Add ingredient id in formulas
- Include notes and default view when cloning a formula

### Version 2.8
- Formula CSV Import improvements
- Added protection from accidental deletion in formulas
- IFRA Library import improvements
- Various bug fixes
- Fix data table in formulas listing
- Fix a bug preventing CAS number to be shown in batch PDF
- Label print improvements
- Multi-dimensional ingredient lookup in alleregens and re-calculate the usage percentages 
- Add measurement units
- Add category image
- Notes summary in formula per ingrdient profile
- Category lookup by ID
- Add category image in ingredients list
- Add pubChem function
- IFRA certification improvements
- Add Molecular Weight for ingredients
- Add Mass for ingredients
- Remove legend/share if formula is empty
- Add update instructions link
- Add auto-update function when non docker
- Add option for notes per ingredient in formula
- Making username case insensitive
- Making ingredient name case insensitive

### Version 2.7
- Making sure the percentage symbol is striped out from allergens quantity
- Create entry in ingredients from allergens
- Adding no usage limit in ingredients
- Remove pvmaker code
- Ingredients management improvements
- SDS file upload improvements
- Root path update
- Add option to exclude private ingredient from uploading to PV Online
- Send local version to pv online API calls when enabled 
- Settings page improvements
- Change layout in ingredient management
- Fix a bug preventing ingredients import from a csv file
- Ingedients CSV Import improvements

### Version 2.6
- ADD: Trading name in ingredients
- ADD: Option to select between 2d and 3d view in PubChem intergration
- ADD: SG help reference
- ADD: Include trading name when search TGSC ingredients DB
- FIX: Issue preventing printing back box label
- FIX: Docker helper minor update
- FIX: A bug preventing showing fromula image in todo drop-down menu
- FIX: INCI name order
- UPD: Rename insights page to statistics

### Version 2.5
- FIX: Docker helper tag (set to :latest)
- FIX: Docker helper to remove PV2 container before start
- FIX: Issue preventing to add new ingredients

### Version 2.4
- ADD: PubChem - exclude Mixtures and Blends
- ADD: PubChem - exclude Mixtures and Blends
- ADD: Default purity value '100' in ingredient management
- ADD: Favicon added!
- CHG: Add new formula page now opens as a modal window
- CHG: Allergen has been renamed to 'Declare'
- CHG: Remove ingredient profile for grouped view in formula
- IMP: Increase the size of the pop-up window when showing ingredient data
- FIX: Minor Bug fixes and cosmetic changes
- FIX: Dashboard pie graphs size
- FIX: Docker helper script
- FIX: Self update procedure

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

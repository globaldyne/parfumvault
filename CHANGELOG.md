# CHANGELOG
### Version 9.8
- Added a low stock icon in formula view
- Fix ingredients reload after ingredient deletion
- Scheduled formulas removal message update
- Scheduled formulas notification messages moved to a toast style
- Making formulas notification messages moved to a toast style
- Info message replaced with a toast style
- Change collation to utf8 for IFRALibrary
- Removed legacy db upgrades before version 8.0
- Moved dropdown actions menu for finished product to the left
- Fix a bug preventing formula hstory to be created for new installations
- Added an automated backup to Google Drive solution - Currently not publicly available
 
### Version 9.7
- Remove epel-release
- Fix first time installation issue
- DB update for remote backup engine - WIP - Tech Preview

### Version 9.6
- Integration with label printer has been removed
- Fix typo when generating a label
- Moving docker image to minimal tag
- Prevent duplicating formulas with the same name
- Rename Clone formula to Duplicate formula
- Added validation test for numeric values when adding a supplier for ingredients
- Added env variable to disable password reset info from the login console - PASS_RESET_INFO
- phpMyAdmin has been removed from the main docker image
- Fix customer creation
- A batch document is automatically generated when a formula is made and marked as complete
- Put back IFRA doc creation and added a warning message
- Making size and price mandatory for bottles inventory
- Only bottles that contains size and price will be shown as an option in finished product page
- Fix passing incorrect formula name when generating a finished product resulting in error when creating a pdf
- Improve user state save method (may need to be revisited)
- Disable non-error reporting
- Fix user password update
- Added available stock when searching for an ingredient to replace in formula

### Version 9.5
- Show ingredients label text in modal in finished product section
- Move batch history menu under formulas
- Fix typos in IFRA generation document
- Remove option to generate IFRA docs
- Fix passing id instead of fid when deleting a formula
- Added ingredient count next to each category in formula view
- Refactor of status bar in formula view
- Refactor of json data feeding the status bar in formula view

### Version 9.4
- Fix update message when in cloud enviroment
- Added support for QR Code for formulas and ingredients that can be scanned using the PV App
- Added extended support to the API for the PV2 APP
- Formulas API extended to include more info 
- Clear file field after a succesful import in IFRA library
- Fix incorrect json image export in IFRA library
- Extend API to include formula image 
- Add supplier name when exporting ingredients to JSON
- Fix incorrect pagination
- Added explanation next to multi-dimensional lookup option
- Fix pdf export in finished formula not working

### Version 9.3
- Set defaults for non mandatory fields when import a json
- DB schema clean-up
- DB Schema price field change to double

### Version 9.2
- Add a watermark when export a formula to sell
- Add PV product name as a subtitle when export a formula in a pdf
- Set main navbar to dark colors
- Fix formulas not showing properly for some users if gender is not set

### Version 9.1
- Fix formula changes history not properly logged
- Fix no formulas error
- Fix incorrect solvent management when decreasing/increasing quantity and update solvent in a formula
- Add light theme support (WIP for dark mode)
- Fix category description misalignment

### Version 9.0
- Search in formula for ingredients for multiples using comma separated values
- Removed PV Online batch import

### Version 8.9
- Fix a bug not returning usage values in ingredient if exists in IFRA library and not contain a numeric value
- Fix deep search option not working on BS5
- Fix a bug failing to populate soem fields when adding a supplier
- Update PVOnline api v2
- Fix api log file error
- Add option to wipe out all ingredients
- Add option to wipe out all formulas

### Version 8.8
- Core update to BS5
- Major code update

### Version 8.7
- Reset form state when adding an ingredient in formula making
- If the materila quantity exceeds the amount in stock, when making a formula, PV will add the material and update material's quantity down to its maximum available, instead if returning an error
- Remove unclosed div tags from formula page

### Version 8.6
- Fix formulas json export
- Fix api log file
- Fix a bug returning error when you searching a non-existing ingredient to merge in formula
- Restructure formula settings page
- Revert formula count query change

### Version 8.5
- Upgrade to Bootstrap 5
- Fix formulas json export
- Ingredient general tab re-design
- Pubchem tab re-design
- Adding more details when a note is added during formula making
- Prevent dilutant purity to exceed 100% when composing a formula
- Restructure ingredient overview page

### Version 8.4
- Auto redirect to the new ingredient when is renamed
- Extend order sorting state save for formula revisions and history
- Disable state 2hr window expire
- General settings page div allign fix
- Pyramid view values input replaced with sliders
- State session stored on separate array
- Remove session state when user preferences reset
- Added last updated field in formulas list (non sortable limitation)
- Change details method display for IFRA, bottles and lids
- Update README to include donations badge
- Add additiotnal fields for suppliers (sku, storage)
- Fix an issue allowing blank urls when editong a supplier
- Redesign add supplier modal
- Added state save for ingredient suppliers
- Added suppliers info when exporting ingredients
- Added total materials count per supplier
- Show saved state search value in  ingredients
- Rewrite suppliers csv export method
- Export suppliers to JSON
- Export supplier's materials to JSON
- Improve csv export for IFRA Library
- Fix formula image upload message

### Version 8.3
- Blocked upgrades to the future versions starting version 6.0
- Added option to store user preferences in the database
- Re-design general settings page
- Add remove user preferences option in maintenance tab
- Remove git files from docker image
- Extend order sorting state save for:
	ingredient categories, 
	perfume types, 
	formula categories, 
	html templates, 
	batches,
	suppliers,
	customers,
	bottles,
	lids

### Version 8.2
- Check if session exists before it starts it
- Fix a bug creating invalid password when registering a new user
- Remove unsed div nesting from first time creation form
- Added a function to address first time installation in hosted enviroments
- Added remote server error handling in marketplace actions
- update_user_settings will take session name as a parameter
- Add support to store formulas and ingredients order state
- Remove duplicate id for ingredients search provider
- Remove unused pvonline functions
- Improve pvonline stats fetch
- Migrate pvonline to perfumersvault.com domain
- Logo for docker added
- Main docker repo changed to perfumersvault
- Main website is been moved to www.perfumersvault.com
- Fix marketplace actions menu position
- Fix precision calculation when comparing versions
- Fix pv meta data timestamp update
- Show server errors if upgrade fails
- Moving DB upgrade script outside index.php
- Removed unused array map from db upgrade

### Version 8.1
- Move DB restore modal to the maintenance page
- Logo position fix
- Remove success message when locking or unlocking a formula - only errors will be reported
- Added option to auto redirect to the newly created formula
- Rename "Add" button to "Add formula" when creating a new formula
- Added a warning when deleting a formula
- Fix actions menu not properly showing if formula is short
- Rewrite maintenance page
- Improve DB backup operations
- Main README file update

### Version 8.0
- Dockerfile typo fix
- Docker compose readme update
- Disable input fields in login page while request is in progress
- Code clean up
- Replacing class mr2 - with mx-2 (WIP)
- Helper script update
- Deprecating uploads directory
- Return an error if not all the required enviroment variables set for docker
- Make sure provided platform is upercase for password reset message

### Version 7.9
- Add icon on error message on login page
- Handle backend error for login page
- Disable login button until the request is complete
- Link to product web page added in login page footer
- Increase nginx body size limit to 400M
- Increase php-fpm post and upload size limit to 400M
- Remove unused packages from dockerfile
- Deprecating ImageMagick library in favor of GD
- Update password reset message
- Set mariadb version to 10.5 in docker compose (higher versions having performance issues)
- Added load time meta data in ingredients page
- Changed inodb flush method for better performance when using docker compose
- When a material is banned or prohibited, the whole fomula line will be colored red
- Further improvements in the login page and backend db creation
- A phpMyAdmin user warning message added
- Release notes update
- Cloud config file removed
- Update db connect error message
- Set default user for fpm-php (apache)
- Fix typo in db schema for brandLogo
- IFRA cert generation logo path update
- Finished product updates/improvements
- PubChem Structure image will fallback to 2d if 3d is set and fails
- Added enviroment variable to customise mysqldump parameters when taking a backup
- Deprecating view box label as image
- Finished product rewrite
- Docker compose file update
- Password reset script will auto create a user if called to non existing user
- Branding page rewrite
- HTML template add modal is now static
- Update api to store logs in the tmp directory instead
- Rewrite formula document upload backend to use json
- Rewrite ingredient document upload backend to use json
- Show an clearer error message when ingredient is missing from the database but exists in formula make view
- Ingredient management relative paths update
- Set default value for price to 0 when fail to receive from supplier
- Deprecating uploads path
- Deprecating files backup function
- Batch history files will be stored in database now
- Disable column statistics when exporting the db via docker compose
- Update mysql restore/backup to use external host
- Fix uploads tmp path
- Fix import permissions/path
- Fix ingredients export 
- Fix tmp path
- Dockerfile update
- Nginx conf added
- php-fpm conf added
- Added external vars for config
- Added Dockerfile
- Code clean-up

### Version 7.8
- Fix a bug causing ingredients synonym search to fail
- Edit ingredient category rewrite
- CSS update adding bs4 styling
- Update tagsinput to bs4 compatibility
- Added Dockerfile
- Config php file merged with opendb

### Version 7.7
- Fix a bug incorrectly showing available solvents when adjust was enabled in ingredient add
- Making sure add solvent function is reset after is added
- Added ingredient profile when fetching ingredient data (simple method)
- Remove glyphicon and replace with fontawesome
- Calculation tools modal became static
- Rewrite add formula modal
- Rephrase formula category add
- Create formula category modal is dismissed after a succesfull creation
- Rewrite some elements in formula settings page
- Fix a bug preventing product formula name to be displayed in formulas list
- Limit formula name to 100 chars
- Product name defaults to "Not set" if none
- Hide page reload info message when modal is opened for formulas list page 
- Add selectpicker in formula settings
- User profile details page became modal
- Customers page improvements
- Lids page improvements
- Bottles page improvements
- Html teplates page improvements

### Version 7.6
- Added option to delete an IFRA entry
- IFRA Library extended to display implementation deadlines for new and current creations
- Check if structural image is already in IFRA Library before attempt downloading it
- Export IFRA Libary to a json format
- Prevent a formula to be renamed to blank name
- Toggle minor fields in IFRA Library
- Rewrite of formula settings page
- Enabling user password encryption
- Fix ui miss aligment for edit user details
- Update reset password script method
- Set defaults for IFRA Library JSON export
- Set defaults for formula meta data
- Handle possible spaces in cas numbers when fetching data from pubchem
- Moving formula settings page to a bootstrap modal
- Import IFRA library from a json
- Validate IFRA/Formulas/Ingredients json file before import
- Added option to edit CAS entries in IFRA entry
- Login page auth backend rewrite
- User register page form element update
- System install page updates
- User auto logins and redirect to the dashboard after succesfull system configuration
- Fix incorrect calculation when using decimals in advanced quanity management for formulas
- Auto adjust solvent when deleting an ingredient in a formula
- Auto adjust solvent when adding an ingredient in a formula


### Version 7.5
- Making sure dilutant reset to its default if previously used
- Making scale formula modal static
- Making perfume type modal static
- Change description text type to textarea in perfume types
- Fix a bug making ingredient fail to load when sorting by category
- Move session validation on its own js
- Extend session validation if formula making view
- Typo fix in marketplace author contact form
- Added changelog link in the about page
- Sorting by color key removed from ingredients category settings
- Sorting by color key removed from formula category settings
- Making ingredient category modal static
- Making formula category modal static
- Making ingredients advanced search modal static
- Making ingredient add supplier modal static
- Making ingredient add document modal static
- Making ingredient add synonyms modals static
- Making ingredient add replacements modals static
- Moving the majority of js into an external file for full formula view
- Make sure IFRA database contains data before attempt to import images from pubChem
- Added option to maintain existing IFRA library data prior import
- Added support for IFRA 51 amedment
- pvToken db field removed

### Version 7.4
- Formula making view confirmation dialogs are now static
- Functionality to add comments/notes when adding ingredient in formula making view
- API settings page separation
- Fix a bug giving file not found error in some cases when trying to take files backup
- Added a dropdown menu to view original formula from scheduled formulas
- Added aditional info in the popup message when replacing an ingredient in formula
- Fix formula not locking all fields when protected mode is enabled
- Change the way a quantity in formula is managed, modal added with option to re-calculate values if solvent is available
- Change formula history order by most recent changes
- Added advanced formula editor (quantity only)
- Move general settings page to separate file
- General settings update calls in json
 
### Version 7.3
- Fix no ingredient json export error
- Added single ingredient export (json only)
- Ingredient name should be unique and previously validated only in application level, since version 7.3, will be forced in database level as well
- Ingredients export will include compostions as well (json only)
- Single ingredient actions menu ui improvement
- Various minor ui improvements
- Docker compose added in preparation of db bundle removal on version 8.0
- Dockerfile removed
- Added search functionality in historical changes page

### Version 7.2
- Fix a bug not showing formula name in notes when converting to ingredient
- Fix formula notes summary not display properly in PHP8
- Fix order columm in ingredient composition
- Ingredient suppliers pages improvements
- Formula settings page moved to a tab instead of a dropdown menu
- Formula historical changes page moved to a tab instead of a dropdown menu
- Formula page: Convert ingredient renamed to create ingredient instead
- Formula page: Create accord modal made static
- Formula page: Create ingredient modal made static
- Formula page: Note top bar resized and realted table removed
- Formula page: Menu table replaced by div element
- Formula page: Hide notes bar if formula is empty
- Formula page: manageQuantity function update
- Formula page: Removed all javascript calls from links
- Formula page: ingredient actions menu grouped under a drop-down menu
- Formula page: Code clean-up
- Formulas and Ingredients page: Remove actions label from table column


### Version 7.1
- Improve formula lock process
- Fix orderable columns in suppliers list
- Fix Formula Make view search failing in PHP8
- Add current revision in main formulas list
- Summary view page fixes

### Version 7.0
- Remove functionality to upload to PV Online
- Rewrite formula revision page
- Move revision in formula tab section
- Update fontawesome lib to v6.4.0
- Added an option to take a revision manually in formulas
- Revision compare method re-written
- Fix where used for ingredients for PHP8
- Added ingredient availability status per supplier
- Fix price scrape function failing sometimes in PHP8

### Version 6.6
- Set default value for total cost and concentration to 0 when formula is empty
- Add PV Online MarketPlace - BETA
- Purpose class removed from formulas view main table
- Fix sorting in IFRA library
- Update compatibility with PV Online API v2.3
- Added Dockerfile and scripts

### Version 6.5
- Remove screenshots directory
- Add show/hide password in user profile
- Implement ingredients merge in formulas
- Re-write ingredient replacement dialog
- Auto add a tag with PV Version when you create a new formula
- PV Online custom API use is now removed
- Remove unused avatar field form users
- Calculation tools page fixes

### Version 6.4
- Fix supplier details edit modal
- Add available var list in SDS Html Editor
- Convert get supplier price function to return json instead of html
- Improve privacy settings in ingredients - a message to let user know that if privacy is enabled, the ingredient data will not be available to other user in the local installation (This is a place holder for a future release)
- Added PV Online in ingredient search provider
- Added option to import single ingredient data from pvonline
- Introducing tokens (pvToken) for users to login to pvOnline - This will be fully migrated in the upcoming releases

### Version 6.3
- Add phpMyAdmin for docker images
- Add a message to notify user that SDS document generation its in preview state
- Complete rewrite suppliers page
- Add additional info fields for suppliers
- Set database to connection to utf-8 by default
- Filter IUPAC in ingredients for illegal chars
- Improvements in SDS document generation
- Ingredients CSV import/export extended to support allergen status and notes impact
- Ingredients JSON import/export
- Minor improvements in CSV import
- Fix session checker error

### Version 6.2
- Fix a bug giving an javascript error when you creating a new empty formula
- Fix a bug preventing DB backup when running in cloud or on prem
- Auto redirect back to the login page when session is expired
- Make PV Online import dialog box persistent untile the import is complete
- Set size for ingredients search box to auto to comply with screen size
- Set tmp directory inside uploads path
- Add a notification if unable to connect to PV Online
- Introducing formula tags, you can add multiple tags/labels per formula to match its description

### Version 6.1
- Stock management improvements
- Improved database and system core upgrade process
- Add allergen materials from compositions to IFRA certification and when printing a warning label
- Various other improvements and bug fixes

### Version 6.0
- Redirect to the previous page if session is expired
- Fixes errors with PHP8
- Better stock management when making a formula
- Add ingredient info in formula making  view
- Add schedule confirmation and explanation message
- Add ingredient info in formula making screen
- Add option to create a new formula category from the formulas list page
- Add low stock in formula making screen
- Improve ingredient replacement management
- Add stock value in ingredient replacement suggestion

### Version 5.9
- Introducing formulas import/export in JSON format
- Notify but don't prevent formula listing if no ingredients in database
- Added option to exclude/include synonyms and compositions when importing from PV Online
- Bash run script update to utilise docker volumes

### Version 5.8
- Make formula page improvements
- Fix incorrect stock update when making formula
- Formula is now automatically marked as made and under evaluation status when making is complete
- Add option to reset ingredient quantity to its original value when making the formula
- Detect and notify if an ingredient is overdosed when making a formula
- Rewrite sell formula page
- Added deep search when adding ingredient in formula - this will extend search in synonyms
- Fix PV Online intergration - Still in TECH PREVIEW state
- Fix a bug preventing loding ingredients page in windows hosts

### Version 5.7
- A rating option has been added so each formula can be rated
- Added the option to attach documents in formulas
- Formula making view improvements
- DB auto update improvements
- Generate ingredient SDS from the available information
- FPDF Version update 1.85
- Fix a bug preventing update default dilutant in ingredients 

### Version 5.6
- HTML Templates added
- Fix the database restore backup process via UI
- Fix a bug preventing formula pdf export in Chrome

### Version 5.5
- Fix db schema
- Fix search functionality in ingredients

### Version 5.4
- Fix login redirect issue caused some vanilla installations to fail
- Fix auto configuration when run in containerized enviroment
- Fix a bug preventing floating quantities in ingredients stock
- Fix a bug preventing formula revision compare to display properly
- Add custom perfume types
- Added last update date in ingredients suppliers
- Added inventory worth
- Cart page rewrite
- Pending formulas page rewrite
- Update ingredients list order enabled columns
- Show ingredient stock/out of stock in search field when adding in formula

### Version 5.3
- PVOnline connectivity improvements
- Fix a bug preventing set formulas to internal use customer
- Add stock quantities in the ingredients page
- Update illegal chars list

### Version 5.2
- Extend API to include allergen status
- Extend API to include solvents status
- Ingredient rename added

### Version 5.1
- New logo design
- API extend to include suppliers 
- API extend to include categories

### Version 5.0
- Introducing ingredients replacements
- Various bug fixes
- Make footer sticky in formula view
- Formula and ingredients tab js improvements
- IFRA limitations can now be bypassed and use user specific values instead
- Add formula concentration level in the api

### Version 4.9
- Update bootstrap select to v1.13.18
- FIX New ingredient popup not showing in some cases
- FIX Unable to create a new customer
- FIX ingredient management in formula when chemical names option is selected

### Version 4.7
- Added 2 character limit when searching for ingredient in formula
- FIX DB update procedure between versions 

### Version 4.6
- Fix formula clone action
- Fix formulas list sort by status and date
- A major rewrite in formula view to utilize select2 (WIP)
- Various improvements
- Removed sorting action from properties and actions columns in formula
- Make sure the dilutant field isn't enabled when purity is at 100%
- Auto enable/disable solvent in ingredient management when updating the purity
- Live search added in dilutant in formula view
- Add delay between requests to PubChem when importing ingredient images to the IFRA library
- Bug reporting or feature request is now pointing to the Github repo
- Improve version update process when updating from older versions

### Version 4.5
- Fix core upgrade 
- Calculation tools moved under top right menu
- Add user password reset info
- Adding ingredient in formula is done based to formula ID
- Formula managment is now now based in fid, remove name lookup

### Version 4.4
- Add formula status (development, failed, production, reformulation)
- Improve ingredient adding in formula
- Added a count the total number of suppliers and documents next to each ingredient
- Add formula name in formula page title
- Fix a bug preventing showing the solvent when formula is locked

### Version 4.3
- Get ingredients list in formula from a json file
- Disable solvent option for solvents
- Add formula description in formula view
- Fall back to a default image in formula view if not yet uploaded by the user
- Add select2 library for better experience when replacing ingredient in formula
- Add the relative icon next to group in formula view

### Version 4.2
- Fix revision generate function
- Fix revision comparison
- Add search support for EINECS
- Show if an ingredient is used in compositions
- Add solvents and carriers as a separate category
- Fix a bug preventing changing concentration in formula if you have updated the name as well
- Added highlight when you search in formula
- Auto update final product concentration if changed whithin the formula

### Version 4.1
- PV Online upload function now includes suppliers
- Add technical data to ingredients core API
- Rewrite IFRA page to populate datatables
- Add mark.js to highlight search results in IFRA page
- Fix broken link when trying to backup DB from IFRA page
- Add image support from pubChem for IFRA Library
- Extend search in formula description for formulas
- Fix sorting in ingredients page
- Fix sorting in formulas page
- Back label print improvements
- Formula comparison rewrite
- Categories page improvements

### Version 4.0
- Add Max Usage info in final product as well
- Add Reccomended Usage info in final product as well
- Add ingredient prohibition warning in formula
- Fix ingredient classication not updating to probition or to specification
- Make sure totals update when an ingredient is excluded from the formula
- Fix a bug causing inventory fail when dealing with liters
- Major logic update in ingredients page
- History page improvements
- Assign a formula to a customer
- Remove auth to get ingredient data from PV Online
- Fix pop-up and tooltip effect when navigating accross pages
- EINECS support added
- Layout change in ingredients view to include EINECS
- Fix molecular weight update in ingredients to be updated from pubchem
- Fix search in ingredients not showing results if contains white spaces
- Import ingredient compositions via CSV
- Fix ingredient csv bug preventing importing ingredients
- Added synonyms list for ingredients
- Import synonyms automatically from pubChem
- Update FEMA when importing data from pubChem
- Update local data from pubChem
- Rename INCI to IUPAC
- Molecular formula check against IFRA is now removed

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

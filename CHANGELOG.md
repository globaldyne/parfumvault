# CHANGELOG
### Version 13.7

### Version 13.6
- Links update

### Version 13.5
- Added formula update api calls from the PV2 app
- Added metadata in formulas get api
- AI full ingredient fill (WIP)
- AI Overall improvements
- Improved backup and restore process
- Removed deprecated columns-statistics from the backup params
- Fixed makeformula api to remove the formula from the queue of making instead of deleting it
- Updated footer for th PV Making app
- PV2 promo banner added
- Appstore links update
- Removed deprecated pv library
- Removed Marketplace

### Version 13.4
- Ensure olfactory pyramid current values appear in sliders
- Added toggle API key visibility
- Average value can now be used for formula analysis - Default
- Added additional col in formula analysis representing the final product
- Update Dockerfile package from mysql -> mariadb
- Fixed SDS doc generation failing
- Removed quantity column from finished product
- Improved sub materials lookup in finished product for IFRA calculation
- Added a sponsor link
- Added app meta data in pv api
- Updated broken IFRA xls download link

### Version 13.3
- Fixed percentage calculation errors in formula analysis.
- Fixed popup link when clicking an ingredient in formula analysis.
- Added visualization to highlight sub-materials exceeding usage limits.
- Added AI support to fetch sub-ingredient info in formula analysis
- Improved backend queries for ingredient replacements in formula view
- Added AI auto fill for ingredient notes
- Enforce that pyramid view note values must total 100% before saving settings
- Fixed Advanced search in ingredients
- Added a warning message to the ingredient management if no ingredient categories exists
- Dockefile update
- Clear error message if AI service is disabled
- Added odor field in api to maintain backport ios app compatibility

### Version 13.2
- Formula making api added "Complete Formula"
- Added Scheduled formula auto table update
- Formula making Android app link update
- Formula making app detection improvements

### Version 13.1
- Dashboard pie color is retained in local storage
- Added Pedro Perfumer in AI providers
- Fixed incorrect reload trigger for make formula page
- Fixed incorrect reload trigger for users page
- Fixed user registration form
- Fixed error message when deleting a formula
- Updated BS5 Icons

### Version 13.0
- Fixed ingredients CSV redeclarations
- Replaced alert box with toast message when copying AI response
- Added a realtime advisory count badge
- Cosmetic changes for formulas labels
- Added BS5 floating elements for formula properties
- Extend formulas search in labels
- Rename tags to labels
- Added ingredient replacement suggestion in formula making using Perfumers AI
- Added full date in AI chat repsonse
- Added CAS in ingrdeient suggestions
- Updated AI formula generation to include dilution and solvent
- Fixed js error when searching in PV library
- Replaced odor with labels for ingredients
- Fixed defaultMessage error in AI Chat
- Replaced Odor column with label in ingredients page
- Deprecated colorKey
- Added makeformula API 
- Improved Perfumers AI Chat

### Version 12.9
- Introduced Google Gemini integration.
- Introduced OpenAI integration.
- Enabled AI-powered formula creation.
- Localized date formatting across the application.
- Removed time from "created" and "updated" fields for a cleaner display.
- Eliminated "created" and "updated" timestamps from ingredient suppliers, bottles, customers, and formula documents.
- Removed "updated" timestamp from HTML templates.
- Added support for Windows and macOS executors.
- Introduced a formula advisor feature.
- Ensured random IDs are assigned during first-time user creation.
- Moved database schema creation check to PV startup instead of the login page.
- Redesigned the login screen with a template-based approach.
- Prevented archiving of protected formulas during deletion attempts.
- Added a check for material existence when generating batch PDFs.
- Included an explanation for formula archiving.
- Assigned new random IDs to archived formulas.
- Updated documentation links for better accessibility.
- Enhanced session monitor to log to a file if specified.
- Improved CSV formula import functionality.
- Introduced an AI-powered chat box.
- Added tab icons to the settings page for better navigation.

### Version 12.8
- Added links for PV apps
- Fix ingredient import compounds failing to import
- Fix AromaTrack app api
- Fix JSON export from within the formula page
- Minor cosmetic changes
- Remove user session on logout even if the session delete query fails
- Backwards compatibility db schema fix

### Version 12.7
- Improved security for fetching ingredient providers
- FPDF lib update to 1.86
- Fix backup process failing in some cases with invalid data
- Add supplier form improvements
- Add default IFRA cat for ingredients
- Dropping public formula view summary
- Adding backend support for AromaTrack app
- Error page update
- Logout better error handling
- Currencies json has been moved under db/
- Added currency support for suppliers
- Added API enable/disable option for system settings
- Fix session handler timestamp compare
- Mariadb Server updated to version 11
- Fix incorrect percentage calculation for formulas
- Fix google backups page not loading if invalid date set
- Added user data export as sql
- Fixed incorrect created field for batch history
- Added functionality to embed ingredient's sub ingredients into the formula
- Force ingredient size to return 10 (ml) by default if size not set in suppliers before version 12.7
- Prevent setting size to 0 in ingredient supplier

### Version 12.6
- Added metadata count (formulas, ingredients, etc.) for users in the admin dashboard
- Monitored user changes in the admin dashboard
- Added last login timestamp for users
- Improved formula making process
- Enhanced upload size validation function
- Added support for Google Analytics
- Improved API security
- Implemented a unique auto-generated 32-bit key for API access
- Fixed synonym search functionality
- Improved price scraper
- Handled suppliers during ingredients import
- Notified only active admin accounts about new/deleted users
- Added session and user clean-up daemon
- Added a column to indicate if users are online
- Fixed PV Library ingredient import issue for some users
- Disabled session monitor by default
- Removed inactive non-admin users after 30 days of inactivity
- Added audit functionality (manual configuration required)
- Filtered full names for illegal characters
- Applied security updates
- Fixed issue with users not loading all entries
- Fixed missing user ID when logging from session monitor agent
- Added an option to bypass strict ingredient check
- Ignored non-numeric values for sys_timeout
- Cleaned up IFRA HTML template (new install only)
- Improved logo handling for documents
- Fixed column alignment for finished product PDF export
- Updated PHP to version 8.3
- Updated nginx to version 1.26
- Added user option to exclude from emails
- Made API test URL non-clickable when the API key is not visible
- Added API key copy button
- Fixed IFRA category description not showing properly in formulas
- Updated select2 library to version 4.1.0-rc.0
- Added order tracking under inventory
- Split import formulas function from core.php
- Split export formulas function from core.php
- Rewrote formulas import/export functionality
- Updated wording when no formulas are available
- Fixed duplicate table ID for supply orders
- Prevented re-ordering if no items are available

### Version 12.5
- Code clean-up
- Various overall security improvements
- Update user profile backend
- Update db schema for a default owner id 1 - This will be removed/refacored in the next release
- Fix update form for compounds
- Replacing mysqli queries with mysql statements (WIP)
- Overall improvements across the app
- Updated core backend for better security handling and error logging
- Fixed accessory type update
- Fixed synonyms delete
- Improve PubChem structure images fetch
- Fixed redirection when adding ingredient if already exists with the same name
- Update ingredient duplication function
- Change default primary key for safety data
- Change policy when importing ingredients from json, any ingredients matching name will be ignored
- Reload ingredient data after a successful import
- Removed collation from db fields
- Only admins can take/restore backups
- Fixed wording when no batches found
- Fixed formula attachments created date display
- Unknown defaults replaced with '-' instead of 'N/A'
- Improve count cart function
- Deprecating old db update scripts
- Google backups section is only available to admin users
- Added better error logging for datatables
- Prevent IFRA document creation if formula isn't compatible with the IFRA library standards
- Remove id field from json exports
- Refactor JSON exports
- Improve file uploads
- Improve CSV imports
- Added multi-user support
- Renamed General settings to My preferences as will contain only personal user settings
- Added System Settings page to administer the system - only available to admins
- Added users import
- Added users export
- Added user impersonation
- API key and status is now configured per user
- Separated system settings from user settings
- Integrations, logs, and maintenance pages will now be available to admins only
- Search preferences reset has been moved in per user basis and globally for admins
- Session validity will only be visible to admins
- My Brand page renamed to Branding
- Branding can now be configured per user
- SDS settings moved to per user basis
- PV Library API moved to system settings
- Added settings per users
- Dropped table settings
- Added user profile self-delete
- Added user email to the session data
- Added country in user profile
- Validate email for user before allowing updating it
- Refactor index.php
- Refactor ingredients import
- Admins can now enable or disable PV Library
- Added user self-register
- Improve forgot password
- Added user announcement text
- Improved session security
- Add user settings reset
- Add DB initialization in docker
- Refactor of user search preferences in db
- Removing non-containerized installation support
- Update api.php for the new settings format
- Password complexity is now enforced
- Added SMTP email server support
- Added user self-register
- Added password reset as a self-service
- Improved documents upload
- Fixed ingredient replacements links
- Installation wizard for non-container has been removed
- Added error screen for missing configuration
- Added export options for ingredients (JSON Format only)
- Added parameterized memory and upload limit size, defaults to 500MB
- Changed enviroment variable from MAX_FILESIZE to UPLOAD_MAX_FILESIZE
- Modules README update
- Rewrite integrations to add modularity
- Dopped backup settings page - admin has to reconfigure
- Rewrite of backup integrations
- k8s templates updated to use the backup agent
- Docker compose file updated for the backup agent
- User id has been altered to a string
- Fixed cas update in IFRA library
- Implement SSO authentication
- Rewrite login page
- Remove DEMO support
- Fixed text imported formula incorrect percentage handling
- Added ingredient group by physical state
- Added users currently online
- Prevent re-activating a user if the account is locally in active
- IFRA file upload improvements
- Set prefix for google backups
- Improved formula analysis security
- Improved costs calculations function
- Improved multi demension formula calculation
- Added indexes for formulas and ingredients to improve speed
- API ingredients upload user id type update

### Version 12.4
- Add dilutant in API formulas
- API has been extended to provide IFRA library data
- Rewritten the core upgrade process
- Added update log history
- Renamed Measurement Unit to Purchase Unit for ingredients supplier for better clarity
- Restructure Index.php
- Update empty table for cart
- Update actions menu for cart
- Update ingredients datatable empty table function
- Various security updates across the app and user access management
- Better error handling for datatables
- Improve formula revisions
- Remove formulas export menu if no formulas
- Import formulas from a text
- Improve docker startup

### Version 12.3
- Fix API log file
- PV Online has been renamed to PV Library

### Version 12.2
- Docker file update
- Fixed document upload for ingredients returning incorrect results
- Update login method
- Fix API formula failing to return formulas
- Added formulas upload via the API
- Prevent user profile update if managed externally
- API now allows upload for formulas, ingredients
- User password is now using a stronger encryption algorithm
- Forgot password modal update for BS5
- Forgot password wording update
- Check if user password is already encrypted when env user variables provided, encrypt if not
- Added a stress test script to crate dummy formulas
- Added available API calls endpoint table

### Version 12.1
- Added summary of total amount required for pending materials
- Added list of materials per supplier
- Added kubernetes manifest for emphemeral db storage
- Renamed openshift folder to k8s
- Added create and update timestamps
- Added user_id to enable multi-user access (FOR SAAS ONLY)
- Update install page
- Fix Manage link in ingredients actions
- Make sure tmp file is created before try to generate a PDF for ingredient
- Wording update
- Code clean-up
- Formula notes summary re-write
- Added an error message next to misconfigured ingredients 
- Include supplier data when duplicating an ingredient
- Added enviroment variables for user creation
- Added enviroment variable to disable updates
- Improve overall sys upgrade
- Make sure it retrieves the write tag when upgrading from github

### Version 12.0
- Lids inventory dropped to accessories
- Minor overall UI updates
- Migrating backend scripts under a common backend api
- Import json functions update
- Added import for accessories
- Added import for bottles
- Added import for suppliers
- Added import for customers
- Droped old CSV export for suppliers
- Fixed pagination for suppliers
- Formula scaling improvements
- Added ingredient to formula backend update
- Fix invalid formula update date on empty formulas
- Update empty table message
- Rename sex to gender
- Error handling improvements
- Date Format update
- Auto update image for formulas when uploaded
- Auto update text title and description after a succesfull update for a formula
- File upload improvements
- Various wording updates
- Removed user alert to reload formula settings pages when making a changes
- Added openshift yaml manifests
- Improve db connect method
- Added a dedicated page to display in case of fatal error
- Added a session timeout to automatically logoff the user after 30 minutes of inactivity - configurable by user
- Change selected material color to yellow in formula making for better descrimination
- Various minor updates and code clean-up
- Added a function to convert session time to hours/mins

### Version 11.9
- Added system logs access via the UI for docker/cloud installations - this comes disabled by default
- Hide properties column in formulas
- Fix a bug preventing formula categories to be shown properly
- Set default avantar to user ico
- Cosmetic UI updates
- Update empty message for ingredients
- Prevent deletion of the last left supplier in an ingredient
- Prevent adding an ingredient with incomplete supplier data to a formula
- New dashboard update, including better and  cleaner view
- Set compounds id to integer for exported ingredients
- Validate if suppliers price is 0 when importing ingredients from a JSON

### Version 11.8
- Error handling for Formula Make page
- Readme file update
- Return 100 in purity if no data by default 
- Increase quantity storage for formulas
- Update formulas status badges
- Update ingredient advanced search filter
- Improve lids and bottles add/edit/update
- Prevent prices to be set to 0
- Prevent Finished product generation if no or invalid prices or supplier info is detected
- User registration and login error handling improvements
- System installation error handling improvements

### Version 11.7
- Formulation: Make sure dilutant is disabled if material is at 100%
- Formulation: Auto remove/add decimal point in quantity depending user's input
- Formulation: Added formula obscure when in a locked state
- Settings: Show/hide api key
- Formula settings page minor updates
- Add IFRA Categories explanation in usage page
- Make sure empty formula returns array for meta data
- Set scroll collapse for datatbales in ingredients to false

### Version 11.5
- Fix revisions comparison
- Update logout script
- Check if session already started for user session
- Improved filtering for advanced search in ingredients
- UI updates for ingredients
- Improve PV Online search
- Refactor local ingredients module
- Refactor PV-Online ingredients search

### Version 11.4
- Add shelf life for ingredients
- Add temprature measurement unit
- Add solubility options instead of a free text for ingredients
- Cleaner and better view for formula comparison
- Minor UI updates

### Version 11.3
- Minor UI updates
- Fix merge ingredient returning incorrect value when nothing found
- Fix placeholder for ingredient selection in formula
- Added force delete for ingredients in use
- A new updated IFRA document added
- IFRA document will show the maximum limit per all categories for 100% concentration
- IFRA document will be generated regardless if final product is off ifra limits
- Fixed currency symbol for finished product
- Ingredient UI update
- Formula UI and backend update
- Structure update
- UI update for IFRA Library
- Refactor JSON import for IFRALibrary
- Increase filter box size
- Extended search to the product name for Batches
- Added delete option for Batches even if the pdf is missing
- Batches backend rewrite

### Version 11.2
- Refactor of backend
- Fix author name not display properly in the contact form in Marketplace
- IFRA library categories changed to FLOAT
- Added pdf and csv export for formula analysis
- If now numeric value found when IFRA library is looked-up in formula analysis, will return 'No value'
- Fixed an issue preventing formula analysis to show the max allowed value from the IFRA library
- Increase page size for formula analysis
- Add full usage tab for formula per IFRA category
- Ignore non-numeric values in IFRA library when calculating max usage

### Version 11.1
- When an ingredient is excluded from formula calculation will also be excluded from any IFRA validations
- Add a warning in a formula when the ingredient has ifra by passed
- Fix IFRA by pass in formulas
- Fix progress bar in formula view not showing properly
- Added currency list
- Set default currency to GBP
- Directory restructure - WIP
- Fix DeepSearch function not properly showing
- Change ingredient manage to id instead of a name
- Added a link to the full ingredient data in MakeFormula
- Removed row zoom for MakeFormula
- Bootbox 6.0 update
- Added deletion for batches
- Error handling for ingredient management
- PDF doc generation is now GA for ingredients
- Make formula improvements
- Bootstrap update to 5.3.3
- jquery update to 3.7.1
- Improved error handling when generating a batch document and archiving a formula
- Blocked auto update for versions before 10.x
- Fix formula archiving when deleting a formula
- Fix auto pdf generation when a formula is marked as complete

### Version 11.0
- Making SDS GA
- Added maximum allowed usage for a formula
- Changed datatype for PubChem to json
- Added a button to view ingredient data in PubChem
- Fix PubChemData update button
- Added more error handling for ajax requests
- Added a var (pvSearch) to search ingredients in the local datatabase
- Changed the way the ingredient is handled when exists in a formula but not in the database. Instead of auto creating it when clicked, it presents options to create it, search in PV Online or import via JSON
- Added editability in IFRA Library
- Fix a bug preventing adding new ingredients
- Treat empty IFRA Library values (PROHIBITION, SPECIFICATION) as 0% allowed
- IFRA structure images import improvements

### Version 10.9
- Sys update check improvements
- Dashboard page updates
- Set datatable entries to the middle
- Sell formula PDF export updates
- Fix a bug causing json import for ingredients to fail
- Include EINECS when adding ingredient entry from compositions
- Compostion page updates and improvements
- Hide privacy tab for ingredients as is not yet utilised
- Tech data page updates
- Update synonyms backend to json format
- If Prohibition is selected in ingredient usage and limits then the usage values set to 0
- Rewrite backend for usage and limits update
- To Declare option is now moved under Udage and Limits section for ingredients
- Fix materials not updating corrrectly when added in the cart
- Added CSV export for cart ingredients
- Added a toast message for the cart actions
- Added a toast message when adding an ingredient to the cart in Make Formula
- CSS clean-up

### Version 10.8
- Fix API key update not returning error in correct format
- Rewrite API page
- Rewrite About page
- Try to get measurement unit from ingredient supplier first for ingredient search in formulation
- Table border removed from tables
- Added support for a dark theme
- Complete rewrite for Finished Product page
- Various minor improvements for Formula view
- Rewrite Sell Formula for BS5
- Added a font size when exporting a PDF in Sell Formula
- Added brand logo when exporting a PDF in Sell Formula
- Added opacity for watermak in Sell Formula page
- Replaced PDF export in Sell Formula with the native DataTables function
- Removed the space from ml2L function when appending the measurement unit
- Default PV Logo update
- Refactor of validateFormula() function
- Renamed clone formula to duplicate

### Version 10.7
- Removed ingredient purity and dilutant from Finished Product page for cleaner view
- Moved IFRA doc to a modal window
- Added a print button for the generated IFRA Doc
- Fix IFRA Document failing to be generated
- Formula Make page refactor
- Lids page minor update
- Added a pending materials page to list all pending materials and their quantity required for the pending formulas
- todo.php has been renamed to scheduledFormulas.php
- Add suppliers in pending formulas backend
- Choose which supplier you updating the stock when making a formula
- Show available stock from all the available suppliers when making a formula, instead of the preferred one only

### Version 10.6
- Ingredient Where Used page minor improvements
- Ingredient Usage & Limits page minor improvements
- Respect classification type from the IFRA Library
- Major refactor in formula limits presentation
- Removed a. element properties from banned/prohibited materials
- Updated red color for banned/prohibited materials
- Added banned/prohibited materials in the formula legend
- Move styling to its own file for MakeFormula page
- Minor cosmetic updates in the compounds page
- Discord invite update

### Version 10.5
- Modify json export for ingredients to include min and max compostion percentage
- Rewrite multi dimensional function
- Added min and max percentage for compositions
- Added formula analysis to breakdown sub-materials and percentages
- Added IFRA limit in composition if found in library
- Fix incorrect integer format for compos json
- Remove ing filter when update data for compositions
- SDS in ingredient has been renamed to a Document
- Added synonyms in document generation for ingredients
- Minor improvements when no ingredients or formulas in the database
- Edit customer form update to BS5
- Introduse SDS creation (PREVIEW)
- Formula comparison minor updates
- Auto create ingredient when importing a formula from Marketplace if not exists locally 
- Added toast type messages for ingredient managment
- Minor UI updates for ingredient management

### Version 10.4
- Hide skipped materials when making a formula
- Auto create main formula if not exists when you import a formula to make data

### Version 10.3
- Added JSON import for Making Formulas
- Added JSON export for all and specific formulas in making
- Layout update when adding suppliers for ingredients
- Added JSON import for formula categories
- Rewrite of categories import to improve ingredients and formula categories import
- Code clean up
- Improvements for SDS generation
- Auto fetch contact data from branding details for SDS
- Formula JSON upload form minor fixes
- Added JSON file import for ingredient categories
- Ingredient Safety Info update
- Formula view table element for description and image re-written to div
- Fix a font for the message returned when error occured during price get request from a supplier 
- Further improvements in SDS generation

### Version 10.2
- Minor improvements in finished product page
- Auto populate CAS and EC if entry exists as an ingredient when adding components in ingredient composition
- Extend simple search for ingredients to EC numbers
- Highlight corresponded ingredient that matches a compound name or CAS number in a formula
- Added GHS Classification for ingredient compounds
- Fix suppliers and compounds count when exporting ingredients to JSON
- Replace table with div for compositions page
- Update perfume types and httml templates page layout
- Make sure the modal box toggles when trying to schedule a formula which is already scheduled
- Fix user menu position
- Added JSON export for formula making
- Table allergens has been renamed to ingredient_compounds

### Version 10.1
- Added export to JSON for perfume types
- Added export to JSON for categories
- Added Discord server link to the footer
- Added Bootstrap-Icons
- Expand ingredient compositions (if any) when formulating
- Filter illegal chars in ingredient search
- Added orientation when exporting to a formula for selling
- Allow ordering in formula sell table
- Set custom decimal precission when generating a formula to sell
- Fix export functions in Formula Make page

### Version 10.0
- Discord server link added
- Added archive option when deleting a formula
- Added JSON export for customers
- Added inventory create/update info for customers
- Customers messages changed to toast
- Added JSON export for lids
- Added inventory create/update info for compounds
- Added JSON import/export for compounds
- Allow a formula to be marked complete when contains skipped materials
- Bottle edit page format update
- Weight added for the bottles inventory
- Bottles inventory messages changed to toast
- Added Inventory for finished Compounds
- Added document size and created date in fotmula attachements page
- Fix search when replacing a material
- PV Scale integration - WIP
- Log ingredient id in history

### Version 9.9
- Record replaced ingredients when finalising a formula in the generated PDF
- Increase toast duration for formula making to 10 seconds
- Make sure the correct ingredient's quantity is reset when reseting an ingredient in Formula make which is replaced
- Update bottle add modal
- Prevent illegal characters from a bottle name
- Fix json import for ingredients if values contains illegal chars
- Implement signaling for formula reload when its updated via the API
- Prevent negative value when updating Make Formula data via the API
- Add odor to the data sent to PV Scale
- Increase update interval check from PV Scale to 5s
- Added separate notes field for Formula Make
- When a formula is marked as completed it generates a document in formula attachments
- Added API key to the data sent to PV Scale
- Add material skip when making a formula
- Add advanced material replacement when making a formula

### Version 9.8
- Added phpMyAdmin definition in the docker compose
- Update general settings to use toast messages
- Added a donation link to the footer
- Update notes summary page to use PV Url
- Added PV Url in settings, you have to update it according to your current setup
- Delete scheduled formula when the main formula is deleted
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
- Added callback function in the api to update formula when making using the PV scale
- Added config / integration for the PV scale

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

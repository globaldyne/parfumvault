<strong>
--------------------------
IMPORTANT CHANGE COMING UP
--------------------------
Please note, if you using the docker PV version, on version 8.0, 
we will be removing mariadb from the image and PV will not be provided as a bundle anymore.
Please make sure you have migrated your database to an external mariadb/mysql instance,
before you update to version 8.0
Same process as cloud or non-docker installations should be followed.
Also - *-cloud images will be dropped as the same image should be used for local docker or
cloud installations like Openshift/EKS, etc
--------------------------
</strong>
Whats New in v7.4
--------------------------
- Minor UI improvements in formula making view
- Functionality to add comments/notes when adding ingredient in formula making view
- Added a dropdown menu to view original formula from scheduled formulas
- Added aditional info in the popup message when replacing an ingredient in formula
- Change formula history order by most recent changes
- Added advanced formula editor (quantity only)

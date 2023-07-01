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
Whats New in v7.3
--------------------------
- Added single ingredient export (json format only)
- Ingredient name should be unique and previously validated only in application level.
  Since version 7.3, will be forced in database level as well. 
  This  may cause importing (json or csv) to fail if contains duplicates. 
  Any duplicates should be removed before import.
- Ingredients export will include compostions as well (json only)
- Single ingredient actions menu ui improvement

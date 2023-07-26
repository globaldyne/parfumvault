<strong>
--------------------------
IMPORTANT CHANGE COMING UP
--------------------------
Docker image port will be changed to 8000 on the next release, please update your configuration 
accordingly.
Please note, if you using the docker PV version, on version 8.0, 
we will be removing mariadb from the image and PV will not be provided as a bundle anymore.
Please make sure you have migrated your database to an external mariadb/mysql instance,
before you update to version 8.0
You can also use docker-compose if you prefer, a docker compose is available with instructions
under the docker-compose directory.
Same process as cloud or non-docker installations should be followed.
Also - *-cloud images will be dropped as the same image should be used for local docker or
cloud installations like Openshift, EKS, etc
--------------------------
</strong>
Whats New in v7.9
--------------------------
- When a material is banned or prohibited, the whole fomula line will be colored red
- PubChem Structure image will fallback to 2d if 3d is set and fails
- Branding page rewrite
- Added enviroment variable to customise mysqldump parameters when taking a backup
- Password reset script will auto create a user if called to non existing user
- Batch history files will be stored in database now
- Generating finished product page updates
- Various bug fixes and improvements
- For full details please refer to the CHANGELOG

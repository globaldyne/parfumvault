## PV with MariaDB


Project structure:
```
.
├── compose.yaml
└── README.md
```

[_compose.yaml_](compose.yaml)
```
services:
  db:
    # We use a mariadb image which supports both amd64 & arm64 architecture
    image: mariadb:latest
    # If you want to use MySQL instead, uncomment the following line and remove the line above
    #image: mysql:latest
    ...
  pvault:
    image: globaldyne/jbvault:v8.0
    ports:
      - 8000:8000
    restart: always
    ...
```

When deploying this setup, docker compose maps the PV container port 8000 to
port 8000 of the host as specified in the compose file.
Please note, the port has been changed in version 8.0 from 80 to 8000 so please make sure you don't use an image bellow version 8.0

> **_INFO_**  
> For compatibility purpose between `AMD64` and `ARM64` architecture, we use a MariaDB as database instead of MySQL.  
> You still can use the MySQL image by uncommenting the following line in the Compose file   
> `#image: mysql:latest`

## Deploy with docker compose

```
$ docker compose up -d
```


## Expected result

Check containers are running and the port mapping:
```
$ docker ps
CONTAINER ID        IMAGE               COMMAND                  CREATED             STATUS              PORTS                 NAMES
b81fb72f3e5a   mariadb:latest                  "docker-entrypoint.s…"   6 seconds ago   Up 4 seconds   3306/tcp, 33060/tcp      docker-compose-db-1
10ec4668474c   globaldyne/jbvault:v8.0   "/bin/bash /start.sh"    6 seconds ago   Up 4 seconds   0.0.0.0:8000->8000/tcp   docker-compose-pvault-1
```

Navigate to `http://localhost:8000` in your web browser to access Perfumers Vault.


Stop and remove the containers

```
$ docker compose down
```

To remove all Perfumers Vault data, delete the named volumes by passing the `-v` parameter:
```
$ docker compose down -v
```
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
    image: mariadb:10.5
    # If you want to use MySQL instead, uncomment the following line and remove the line above
    #image: mysql:latest
    ...
  pvault:
    image: globaldyne/perfumersvault
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
0205aa69ce88   globaldyne/perfumersvault            "entrypoint.sh"          40 seconds ago   Up 36 seconds   0.0.0.0:8000->8000/tcp   docker-compose-pvault-1
bad8eb6f5273   mariadb:10.5   "docker-entrypoint.s…"   40 seconds ago   Up 36 seconds   3306/tcp                 docker-compose-pvdb-1
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
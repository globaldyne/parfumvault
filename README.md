# Perfumers Vault

A sophisticated tool to help perfumers organize their formulas, ingredients, and inventory.

This is FREE software provided "as is" without ANY warranty under the MIT license.

[![Current Release](https://img.shields.io/github/v/release/globaldyne/parfumvault.svg "Current Release")](https://github.com/globaldyne/parfumvault/releases/latest) ![Discord](https://img.shields.io/discord/1238069309356638217)

---

## Features

* AI-assisted formulation
* OpenAI and Google Gemini support
* Formula management, comparison, and revisions
* Ingredient and supplier inventory management
* SDS and IFRA document generation
* Pyramid olfactory view and cost calculation
* Label printing and formula export
* CSV, Text and JSON import for formulas and ingredients
* IFRA library integration and validation
* Multi-supplier support with automatic price fetching
* Dark mode support
* Batch history tracking
* Customizable document HTML templates

For the full feature list, please visit the [Knowledge Base](https://www.perfumersvault.com/kb/).

---

## Getting Started

### Prerequisites

Ensure you have the following installed:
- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

---

## Docker Image

### Quick Start (Scripted)

Run the latest Docker image using the following command:

```bash
sh -c "$(curl -fsSL https://raw.githubusercontent.com/globaldyne/parfumvault/master/helpers/run_pvault.sh)"
```

### Manual Setup

Run the Docker container manually:

```bash
docker run --name pvault \
  -e PLATFORM=CLOUD \
  -e DB_HOST=... \
  -e DB_NAME=... \
  -e DB_USER=... \
  -e DB_PASS=... \
  -e phpMyAdmin=false \
  -e MAX_FILE_SIZE=4194304 \
  -e TMP_PATH=/tmp/ \
  -e FILE_EXT='pdf, doc, docx, xls, csv, xlsx, png, jpg, jpeg, gif' \
  -e DB_BACKUP_PARAMETERS='--column-statistics=1' \
  -e SYS_LOGS=DISABLED \
  -p 8000:8000 \
  -d globaldyne/perfumersvault
```

> **Note:** All `DB_` variables are required.

### Docker Compose

Use the following `docker-compose.yml` configuration:

```yaml
---
services:
  pvdb:
    image: mariadb:11-ubi9
    command: '--default-authentication-plugin=mysql_native_password --innodb-flush-method=fsync'
    volumes:
      - db_data:/var/lib/mysql
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: pvault
      MYSQL_DATABASE: pvault
      MYSQL_USER: pvault
      MYSQL_PASSWORD: pvault
      MARIADB_AUTO_UPGRADE: true
    expose:
      - 3306

  pvault:
    image: globaldyne/perfumersvault:latest
    ports:
      - 8000:8000
    restart: always
    environment:
      PLATFORM: CLOUD
      DB_HOST: pvdb
      DB_USER: pvault
      DB_PASS: pvault
      DB_NAME: pvault
      MAX_FILE_SIZE: 4194304
      TMP_PATH: /tmp/
      FILE_EXT: 'pdf, doc, docx, xls, csv, xlsx, png, jpg, jpeg, gif'
      DB_BACKUP_PARAMETERS: '--column-statistics=1'

volumes:
  db_data:
```

Start the services:

```bash
docker-compose up -d
```

---

## Access the Application

Once the container is running, open your browser and navigate to:

[http://localhost:8000](http://localhost:8000)

For more information, please refer to the [Knowledge Base](https://www.perfumersvault.com/kb/).



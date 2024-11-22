## Deploys Perfumers Vault with MariaDB and phpMyAdmin


Project structure:
```
.
├── pvault-ocp-persistent.yaml
├── pvault-ocp-ephemeral.yaml
└── README.md
```


The manifest will create pods for the pv app using the latest image tag, mariadb and phpmyadmin


## Deploy in openshift via cli

```
$ oc apply -f pvault-ocp-persistent.yaml
```


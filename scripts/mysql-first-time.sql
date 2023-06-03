CREATE DATABASE pvault;
CREATE USER 'pvault'@localhost IDENTIFIED BY 'pvault';
GRANT ALL privileges ON `pvault`.* TO 'pvault'@localhost;
DROP DATABASE IF EXISTS test;


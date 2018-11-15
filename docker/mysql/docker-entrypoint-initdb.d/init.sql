grant all privileges on *.* to root@"%" identified by 'root' with grant option;
FLUSH PRIVILEGES;
SET GLOBAL sql_mode = '';
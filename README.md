Setup Instructions

Clone the repo from https://github.com/Ammelll/WynnGuessr
Host the PHP frontend locally via an apache2 server (all I had to do was move or symlink the folder to /var/www/ or something like that)
npm run devStart in the WynnGuessr folder
Install sometype of database software (mysql, mariadb ect.), dump the sql.sql file into a database
fix the database username and passwords in includes/dbh.inc.php and in websockets-server/server.js 
should be running at localhost
login to an account and play

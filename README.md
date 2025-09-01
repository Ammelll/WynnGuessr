# Wynnguessr

This is a recreation of Geoguessr made for the Minecraft server Wynncraft.




## Deployment

To setup the server side of the project run

```bash
  cd websockets-server
  node server.js
```

To setup the client side of the project (Enable PDO for mysql in php.ini) and start the PHP server in a method of your choice. Ex. 
```bash 
    php -S localhost:3000
```
If your deployment location changes, one will have to edit the "localhost" within files to the ip of their preference.

To setup the SQL database install via sql.sql


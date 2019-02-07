# Collectiv Example Project

Very small PHP website that does basic user management. External dependencies were kept to a minimum.

### Running

This project requires PHP, MySQL and a webserver like NGINX or Apache to operate.
The project includes docker containers that can be built to do this.

Running it with your own stack (should be):

* Run ```composer install --prefer-dist -o``` inside the app directory.
* Run ```yarn install``` or ```npm install``` inside the app directory.
* Run ```yarn run prod``` or ```npm run prod``` inside the app directory.
* Point your webserver to the **app/public** directory.
    * Note that your webserver has to be configured correctly.
    * You can find NGINX example files in the **docker** directory.
* Create a database called ```collectiv``` inside your MySQL instance.
* Import the default.sql file in **app/assets** directory in your MySQL instance.

Running it with docker:

* Uncomment the ```composer``` and ```node``` services in docker-compose.yml.
* Run ```docker-compose up```.
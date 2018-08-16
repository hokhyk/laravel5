# Symphony installation (version 4.x and before)
- sudo curl -LsS https://symfony.com/installer -o /usr/local/bin/symfony
-  sudo chmod a+x /usr/local/bin/symfony
- symphony new mava

# using composer to start a symphony project:
- composer create-project symfony/skeleton mava 
- cd mava

# Run your application:
    1. Change to the project directory
    2. Create your code repository with the git init command
    3. Execute the php -S 127.0.0.1:8000 -t public command
    4. Browse to the http://localhost:8000/ URL.

       Quit the server with CTRL-C.
       Run composer require server --dev for a better web server.

  * Read the documentation at https://symfony.com/doc


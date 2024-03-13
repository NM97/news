## Installation Guide

1. **Install Docker Desktop**: Ensure that you have Docker Desktop installed on your system. Installation instructions can be found on the official Docker website [here](https://www.docker.com/products/docker-desktop/).

2. **Clone the Repository**: Open a terminal or command prompt and execute the following command:

    ```bash
    git clone https://github.com/NM97/news.git
    ```

3. **Navigate to the Project Directory**: Navigate to the `news` directory that was created after cloning the repository:

    ```bash
    cd news
    ```

4. **Run Docker Containers**: In the terminal, execute the command:

    ```bash
    docker-compose up -d
    ```

5. **Install Symfony Dependencies**: Enter the PHP container to install Symfony dependencies. Execute:

    ```bash
    docker-compose exec php bash
    ```

    Then, within the PHP container, execute:

    ```bash
    composer install
    ```

6. **Configure Database Settings**: Next, configure the database settings in the `.env` file. After changing the configuration, you need to reset the PHP container with:

    ```bash
    docker restart container_name_php
    ```

    Use the following command to verify the MySQL IP:

    ```bash
    docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' container_name
    ```

    To check the name of the PHP and MySQL container use the command:

    ```bash
    docker -ps
    ```

7. **Generate Symfony Application Key**: Inside the PHP container, execute:

    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    php bin/console doctrine:fixtures:load
    php bin/console cache:clear
    ```

8. **Run the Application in Developer Mode**: Before previewing the application, make sure you have executed `npm install` and `npm run watch` commands in the project's main directory. Open two additional terminals, execute `npm install` in one, and then in the second terminal execute `npm run watch`.

9. **Preview the Application**: After completing the above steps, you can open a web browser and go to [http://localhost:8000](http://localhost:8000) to see the running application.


## API Endpoints

* /api/top-authors
* /api/author/{id}/articles
* /api/article/{id}

## App Route

* /
* /login
* /register
* /article/new
* /article/{id}
* /article/{id}/edit
* /article/{id}/delete
* /dashboard
* /dashboard/profile

## Additional information 

The application using fixtures will generate sample database entries (a dozen articles and a few users). You can log in to the system with a login test@test.pl and a password test@test.pl.

## About LaraLack
LaraLack is an open-sourced clone of Slack 

## Requirements
- PHP >= 7.2.5
- MySQL >= 5.7
- Git
- Composer
- NodeJS
- Bootstrap 4
- jQuery

## Installation
In order to install this repository on your local machine, first off make sure [Git](https://git-scm.com/downloads) is installed on your system then clone it through the following command:  
`$ git clone git@github.com:behzadmoradi/laralack.git`  
Next you need to install PHP dependencies; to do so, you need the [Composer](https://getcomposer.org/)  package manager. Go to the project directory and fetch dependencies via this command:  
`$ composer install`  
In order to install front-end dependencies, after making sure you have [NodeJS](https://nodejs.org/) installed on you machine, run the following command:  
`$ npm install`  
Make a copy of `.env.example` file located in the root of the project and call it `.env`. At the moment, the only thing that needs to be changed on this file is the name, username, and password of the database. Go to *phpmyadmin* and create a new database called *laralack* or whatever name you want then update the following section in `.env` file:  
`DB_CONNECTION=mysql`  
`DB_HOST=127.0.0.1`  
`DB_PORT=3306`  
`DB_DATABASE=laralack`  
`DB_USERNAME=root`  
`DB_PASSWORD=123456`  
Now you need to run the following Artisan command to create the required tables:  
`$ php artisan migrate`  
If successful, by going to *phpmyadmin* you can see that a couple of tables are created. Before using Laravel's encrypter, you must set a key option in your `config/app.php` configuration file; so as a final step in the installation process, do so by running `php artisan key:generate` command.

## Usage
To start using LaraLack, you need to run `php artisan serve` command in the terminal and the server starts running at `http://127.0.0.1:8000` and by going to this url, you would see that the project is up and running!  
Before anything else, you **have to** make sure that [laravel-websockets](https://github.com/beyondcode/laravel-websockets) server is running; so create another terminal window and run the following command:  
`$ php artisan websockets:serve`  
By default, post number 6601 will be used for websockets.  
Now, simply go to `http://127.0.0.1:8000/register` and create a new user (In Incognito mode of your browser, create yet another user for testing purposes.). 
After login, you need to choose a name and username for your account then click on the **plus** sign next to the "Channels" on the left sidebar to create a new channel.  
By clicking on the channel name, it appears on top of main section of the page with a down arrow. If you click on that arrow then "Invite people", you can send an invitation email to other users.  
For example, you can enter the email of the user you already created in Incognito window and if you refresh this page, the channel name that has already been created by the other user will show up. From now on, the two users can easily chat through the channel that is assigned to both of them.  
To start chatting one to one, click on the **plus** sign next to the "Direct Messages" on the left sidebar to create a direct chat with a specific user.

## Screenshots 
This image shows the popup for choosing a name and username:  
![LaraLack](https://github.com/behzadmoradi/laralack/blob/master/public/img/guides/01.png?raw=true)  
This image shows the popup for adding a new channel:  
![LaraLack](https://github.com/behzadmoradi/laralack/blob/master/public/img/guides/02.png?raw=true)  
This image shows the popup for inviting people to a channel:  
![LaraLack](https://github.com/behzadmoradi/laralack/blob/master/public/img/guides/03.png?raw=true)  
This image shows the popup for creating a chat room for a specific user:  
![LaraLack](https://github.com/behzadmoradi/laralack/blob/master/public/img/guides/04.png?raw=true)  
This image shows the chat history for a specific user:  
![LaraLack](https://github.com/behzadmoradi/laralack/blob/master/public/img/guides/05.png?raw=true)  
This image shows the number of unread messages from another user:  
![LaraLack](https://github.com/behzadmoradi/laralack/blob/master/public/img/guides/06.png?raw=true)  

## Issues
If you discover any issue within this project, please send an e-mail to me via [me.behzad.moradi@gmail.com](mailto:me.behzad.moradi@gmail.com).

## Changelog
Please see [CHANGELOG](https://github.com/behzadmoradi/laralack/blob/master/CHANGELOG.md) for more information what has changed recently.

## License
LaraLack is an open-sourced software licensed under the [MIT](https://opensource.org/licenses/MIT).  

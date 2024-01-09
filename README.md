<p align="center"><a href="https://dev.dream-speak.pl/bargify/" target="_blank"><img src="https://raw.githubusercontent.com/PiciuU/bargify-dreamfork-app/master/client/src/assets/images/bargify-logo.svg" width="200" alt="Bargify Logo"></a></p>

# Bargify

Stay ahead of the game by adding your desired products to the watchlist. Be the first to know when they become available or go on sale. Gain an advantage over other shoppers and never miss out on your dream purchases!

## Live Demo

A live version of the application is available at: [https://dev.dream-speak.pl/bargify](https://dev.dream-speak.pl/bargify/)

## Features

- Add products to your watchlist.
- Receive notifications when a product becomes available.
- Edit parameters such as the maximum price for which you want to receive notifications.
- Remove products that no longer interest you.

## Technologies

- Vue 3 - a modern JavaScript framework for building user interfaces.
- Dreamfork 1 - a nimble and swift web application framework inspired by Laravel.

## System Requirements

Make sure your system meets the following requirements:

- Node.js (version >= 18.16.1)
- Npm (version >= 9.8.0)
- PHP (version >= 8.1)
- Composer (version >= 2.5.8)
- MySQL or any other database

## Installation

1. Clone the repository: `git clone https://github.com/PiciuU/bargify-dreamfork-app.git`
2. Navigate to the project directory: `cd bargify-dreamfork-app`
3. Install backend dependencies: `cd server && composer install`
4. Create and configure the `.env` file with the appropriate database credentials
5. Run required database migrations manually (You can find all database migrations in `database/migrations` folder)
6. Install frontend dependencies: `cd ../client && npm install`
7. Create and configure the `.env.development` and `.env.production` file with the appropriate data
8. Start the Dreamfork backend server: `cd ../server && php -S localhost:8000 -t public/`
9. In a separate terminal, start the Vue frontend server: `cd ../client && npm run dev`

Please make sure to have PHP, Composer, and a compatible database installed on your system before proceeding with the installation.

For detailed configuration instructions, please refer to the README files inside the `client` and `server` folders. They provide comprehensive guidance on setting up and customizing the client and server aspects of the application.

## Authors

This repository is authored by PiciuU.

## License

This project is licensed under the [MIT License](https://opensource.org/license/mit/).

**Note:** This project serves as a demonstration of utilizing the [Dreamfork](https://github.com/PiciuU/DreamFork-PHP-Framework) framework in real-world applications. The MIT license provides you with the freedom to use, modify, and distribute the code within the terms specified in the license file.







## Chatbot

Chatbot functionalities:
- Login
- Signup
- Currency Conversion
- Money Deposits/Withdrows
- Log

## Requirements

- Laravel
- Composer
- LAMP stack

## Instalation

- Clone the repository
- Create a MySQL database
- Create a .env file and change/add the following variables
```
DB_DATABASE={db_name}
DB_USERNAME={db_user}
DB_PASSWORD={db_password}

AMDOREN_API_KEY={your_api_key}
```
- Install dependencies
```
composer install
```
- Run the following commands
```
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan cache:clear
php artisan config:clear
php artisan serve
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
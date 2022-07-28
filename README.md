## Stack
- PHP 8.1
- Laravel v9.3.1

## Installation / Setup

1. Clone repository & move into directory
```bash
git clone https://project-url Hail
```
2. Change directory
```bash
cd Hail
```
3. Create an `.env` file with bare essentials (**HAIL_** entries are important!)
```bash
cp .env.example .env 
```
5. Generate an app key 
```bash
php artisan key:generate
```
5. Install composer dependencies
```bash
composer install
```
6. Setup local Valet link (optional)
```bash
valet link hail --secure # --secure flag for HTTPS (again optional)
```

7. Visit https://hail.test and hit the "Connect to Hail" button
8. Follow the OAuth2 flow and upon redirect view the articles!!
# Work Management
Website that help Manager to manage Project and Task. To know progress work of project that manager set to the employee.

### How to Setup
1. clone this repository
```bash
git clone https://github.com/K2FA/work-management.git
cd work-management
```

2. Going to root directory and install composer package
```bash
cd root
composer install
```

3. Copy `.env.example` and setting DB Host as used, example: MySQL
```bash
cp .env.example .env
```

4. Generate key laravel app
```bash
php artisan key:generate
```

5. Setting database in `.env` file
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=database_name
DB_USERNAME=username
DB_PASSWORD=password
```

6. Migrate database and seeder
```bash
php artisan migrate:fresh --seed
```

7. Jalankan Server laravel
```bash
php artisan serve
```

8. Buka browser dan masukkan url `127.0.0.1:8000`

se for testing the app

```bash
// Admin
admin@example.com
password

// Manager
manager@example.com
password

// Employee
employee@example.com
password
```


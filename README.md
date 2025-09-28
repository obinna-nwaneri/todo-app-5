# Todo App

A lightweight todo list application built with raw PHP, MySQL, vanilla JavaScript, and CSS. The project also includes an administration area for managing tasks and authenticating privileged users.

## Features

- Create, update, and delete todo items from the public interface.
- Live updates without reloading the page via a small JSON API.
- Administrative dashboard with authentication to oversee every task.
- Graceful fallbacks so the app remains usable without JavaScript.

## Requirements

- PHP 8.1+
- MySQL 5.7+ or MariaDB 10.4+
- Composer is **not** required; the app uses no external PHP dependencies.

## Installation

1. Copy the project files to your web server.
2. Create a new MySQL database and user, then import the schema:

   ```sql
   SOURCE /path/to/schema.sql;
   ```

3. Update the database credentials in [`config.php`](config.php).
4. Generate a password hash for the admin user defined in `schema.sql` and replace the placeholder value. You can use PHP's CLI for this:

   ```bash
   php -r "echo password_hash('your-password', PASSWORD_DEFAULT), PHP_EOL;"
   ```

5. Start the PHP development server for local testing:

   ```bash
   php -S 0.0.0.0:8000 -t public
   ```

6. Visit `http://localhost:8000` to use the public todo list or `http://localhost:8000/admin/login.php` to sign in as an administrator.

## Project Structure

```
public/
├── index.php          # Public todo list UI
├── api.php            # JSON API for AJAX interactions
├── assets/
│   ├── app.js         # Front-end behaviour
│   └── styles.css     # Styling for the app and auth pages
└── admin/
    ├── dashboard.php  # Admin dashboard for managing tasks
    ├── login.php      # Administrator authentication
    └── logout.php     # Session termination
includes/
├── bootstrap.php      # Starts session and loads shared utilities
├── db.php             # Database connection helper
├── auth.php           # Admin authentication helpers
└── task_repository.php# Data-access helpers for todos
config.php             # Database credentials
schema.sql             # Database schema and seed admin user
```

## Security Notes

- Always use HTTPS in production and configure strong database credentials.
- Change the default admin account after installation and create additional accounts as required.
- Configure your web server so that only the `public/` directory is web-accessible.

## License

This project is provided as-is without any specific license. Adapt it to suit your needs.

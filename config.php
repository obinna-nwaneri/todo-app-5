<?php
/**
 * Basic database configuration for the Todo application.
 *
 * Update these constants to match your MySQL setup. The default values are
 * suitable for local development when using the built-in PHP server and a
 * MySQL instance running on the same machine.
 */
const DB_HOST = '127.0.0.1';
const DB_PORT = 3306;
const DB_NAME = 'todo_app';
const DB_USER = 'todo_user';
const DB_PASSWORD = 'todo_pass';

/**
 * Optional: adjust the timezone used by the application.
 */
date_default_timezone_set('UTC');

# PHP RESTful API About Video Games
***
# api/.htaccess file
### Purpose

The following Apache directives are used to enable URL rewriting and direct all incoming requests to a single entry point (`index.php`). This setup is commonly used in PHP applications for centralized routing and request handling.

### Directives Explained

- `RewriteEngine On`: Enables the `mod_rewrite` module for Apache, allowing URL rewriting.
- `RewriteCond %{REQUEST_FILENAME} !-f`: Checks if the requested filename does not exist as a regular file.
- `RewriteCond %{REQUEST_FILENAME} !-d`: Checks if the requested filename does not exist as a directory.
- `RewriteCond %{REQUEST_FILENAME} !-l`: Checks if the requested filename does not exist as a symbolic link.
- `RewriteRule . index.php`: If the requested URL does not match an existing file, directory, or symbolic link, redirects it internally to `index.php`.

### Usage

These directives are typically placed in an `.htaccess` file within your Apache web server's document root directory. They ensure that all requests that do not correspond to an existing file or directory are routed to `index.php`, where your PHP application can process and handle them as needed.

### Example

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-l
RewriteRule . index.php
```
***
# composer.json file
With the help of this file we can autload our files from src folder.


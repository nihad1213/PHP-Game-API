# PHP RESTful API About Video Games
***
# .htaccess file
```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-l
RewriteRule . index.php
```
**RewriteEngine On: ** is an Apache Directive that enables **mod_rewrite** modules. That allow us URL rewriting.
**RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-l:**This set of RewriteCond directives is typically used in conjunction with mod_rewrite in Apache to conditionally apply rewrite rules based on file existence checks. Let's break down what each line does:

    RewriteCond %{REQUEST_FILENAME} !-f
        This condition checks if the requested filename (%{REQUEST_FILENAME}) does not (!) exist as a regular file (-f).
        If this condition is true (meaning the file does not exist), the subsequent rewrite rule (if any) associated with it will be applied.

    RewriteCond %{REQUEST_FILENAME} !-d
        This condition checks if the requested filename (%{REQUEST_FILENAME}) does not (!) exist as a directory (-d).
        If this condition is true (meaning the directory does not exist), the rewrite rule associated with it will be applied.

    RewriteCond %{REQUEST_FILENAME} !-l
        This condition checks if the requested filename (%{REQUEST_FILENAME}) does not (!) exist as a symbolic link (-l).
        If this condition is true (meaning the symbolic link does not exist), the rewrite rule associated with it will be applied.

These conditions are often used to redirect or rewrite URLs only if the requested resource (%{REQUEST_FILENAME}) does not correspond to an existing file, directory, or symbolic link. This allows you to avoid rewriting URLs that should directly serve existing files or directories on your server.

**RewriteRule . index.php: **directive in Apache's mod_rewrite context is used to redirect or rewrite all incoming requests to a single entry point, typically used in PHP applications for routing purposes. Let's break down what this rule does:

    RewriteRule: This starts a directive that specifies a rule for rewriting URLs.
    .: The dot (.) matches any single character. In this context, it effectively matches any URL path segment that is not already handled by an existing file or directory (due to previous RewriteCond checks like !-f, !-d, !-l).
    index.php: This is the target to which the URL will be rewritten. In PHP applications, this often represents the main entry point or controller file that handles routing and processing of requests.

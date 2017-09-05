# instant-upload
php upload, self hosted file upload and sharing, ShareX upload

# dependency
PHP 7.0 or 7.1

# Nginx rule
```
location / {
    try_files $uri $uri/ /index.php?$args;
}
```

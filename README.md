# instant-upload
php upload, self hosted file upload and sharing, ShareX upload

# WARNING
상용 서버에 사용하지 마십시오.
업로드 한 파일이 1000개 이상인 경우, 시스템에 심각한 손상을 줄 수 있습니다.

# dependency
PHP 7.0 or 7.1

# Nginx rule
```
location / {
    try_files $uri $uri/ /index.php?$args;
}
```

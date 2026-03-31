
FROM php:8.2-apache

# Cài đặt các extension cần thiết cho MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copy toàn bộ code vào thư mục web của Apache
COPY . /var/www/html/

# Mở cổng 80
EXPOSE 80
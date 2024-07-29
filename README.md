bước 1: tải xampp bản 3.3.0 -> cài đặt -> mở xampp control panel -> mở phần config của apache -> PHP(php.ini) -> control + F search từ "zip" -> xóa dấu ";" ở đầu dòng 

bước 2: start apache và mysql

bước 3: composer install

bước 4: cp .env.example .env

bước 5: php artisan key:generate

bước 6: php artisan migrate -> yes

bước 7: php artisan db:seed

bước 8: php artisan serve

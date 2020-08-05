# Test

## How To

1. `git clone https://github.com/ZagovorychevEPAM/sptest.git`
2. `cd sptest`
3. `composer install`
4. `php artisan log:reader -p /youPathToTheProject/storage/data/webserver.log` - for the count of views
5. `php artisan log:reader -p /youPathToTheProject/storage/data/webserver.log -m unique` - for the unique count of views

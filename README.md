# SmartPension Tech Task

## Installation
1. `git clone https://github.com/ZagovorychevEPAM/sptest.git`
2. `cd sptest`
3. `composer install`

## Usage

#### All views
- `php artisan log:reader -p storage/data/webserver.log`
#### Unique views
- `php artisan log:reader -p storage/data/webserver.log -m unique`

## Testing
`./vendor/bin/phpunit --configuration phpunit.xml tests`

#### Requirements
PHP7.2
Node9+
yarn

#### Installation
`yarn install`

`composer install`

`yarn watch`

_Change DATABASE_URL parameter in .env file_


`./bin/console doctrine:database:create`

`./bin/console doctrine:schema:update --force`

`./bin/console server:run`


Open browser at http://127.0.0.1:8000

#### Database import

To populate database with fake data, run

`./bin/console app:populate`

It takes about 40 minutes to finish but you can use browser and see number of rows increasing.



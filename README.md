### Application informations

#### Requirements:
-- php7.3 or higher
-- composer

#### Lib:
--  [CoinGecko API](https://github.com/codenix-sv/coingecko-api)

### Run application in docker environment:

run `docker-compose build app`
run `docker-compose up -d` to start application in docker.

Now you can see application running in `http://localhost` .

### Local environment
If you prefer run application in your local environment, make sure 
if all required setting is satifaied and follow the bellow steps:

1 - to install all dependences run `composer install`
2 - run `cp .env.example .env` and set your env config for database
3 - run `php artisan key:generate`
4 - now you should create coin table, run `php artisan migrate`
5 - to execute application run `php artisan serve`. The application is runnin on `http://localhost:8000`


### Endpoints

#### To return current crypto currency price:
#### `POST /api/coin`

    body request
    {
       coin_id(optional): a coin id (possible values: bitcoin, ethereum, dacxi, ...)
       vs_currency(optional): a currency for compare coin price (possible values: usd, aud, brl, ...)
    }

If none of these params was passed, response will be btc/usd price.
#### Example:
Request:
```json
{
	"coin_id": "",
	"vs_currency": "usd"
}
```
Response:
```json
"bitcoin": {
		"usd": 29191
	}

#### `POST /api/coin/estimated-price`


    body request: 
    {
       coin_id(optional): a coin id (possible values: bitcoin, ethereum, dacxi, ...)
       date:(required): one valid date with format dd-mm-yyyy 
       vs_currency(optional): a currency for compare coin price (possible values: usd, aud, brl, ...)
    }

If ***coin_id*** and ***vs_currency*** params wasn't passed, response will be all possible parities of bitcoin for required ***date***.

#### Example:
Request:
```json
{
	"coin_id": "bitcoin",
	"date": "01-05-2022",
	"vs_currency": "usd"
}
```
Response:
```json
{
	"btc\/usd": "37820.61"
}
```

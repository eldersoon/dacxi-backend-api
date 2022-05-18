## Application informations

#### Requirements:
- php7.3 or higher
- composer

#### Lib:
-  [CoinGecko API](https://github.com/codenix-sv/coingecko-api)

## Run application in docker environment:

- run `docker-compose build app`
- run `docker-compose up -d` to start application in docker.

Now you can see application running on `http://localhost` .

### Local environment
If you prefer to run the application in your local environment, make sure that all required settings are met and follow the steps below:

* to install all dependencies run `composer install`
* run `cp .env.example .env` and set .env file settings for database
* run `php artisan key:generate`
* now you should create coin table, run `php artisan migrate`
* to execute application run `php artisan serve`. The application is runnin on `http://localhost:8000`


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

```json
body request: 
{
    coin_id(optional): a coin id (possible values: bitcoin, ethereum, dacxi, ...)
    datetime:(required): one valid date with format yyyy-mm-dd HH:ii
    vs_currency(optional): a currency for compare coin price (possible values: usd, aud, brl, ...)
}
```

If the ***coin_id*** and ***vs_currency*** parameters are not passed, the response will be the bitcoin price for the required ***date***.

#### Example:
Request:
```json
{
	"coin_id": "bitcoin",
	"datetime": "2022-05-18 12:40",
	"vs_currency": "usd"
}
```
Response:
```json
{
	"datetime": "2022-05-18 12:44",
	"usd": "29849.22"
}
```

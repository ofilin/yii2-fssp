## yii2-fssp

### Федеральная служба судебных приставов

Интерфейс программирования приложений (API) БДИП ФССП России предназначен для получения сведений общедоступной части Банка данных исполнительных производств.

Токен можно получить бесплатно здесь https://api-ip.fssprus.ru/register

Компонент позволяет выполнять запросы:

**Yii::$app->fssp->physical()** - запрос на поиск физического лица

**Yii::$app->fssp->legal()** - запрос на поиск юридического лица

**Yii::$app->fssp->ip()** - запрос на поиск по номеру исполнительного производства

**Yii::$app->fssp->group()** - групповой запрос

Получить подробную информацию можно здесь https://api-ip.fssprus.ru/swagger

### Installation

Add the package to your `composer.json`:

    {
        "require": {
            "ofilin/yii2-fssp": "^0.1"
        }
    }

and run `composer update` or alternatively run `composer require ofilin/yii2-fssp:^0.1`


add to config file:
```
'components' => [
    // ...
    'fssp' => [
        'class' => ofilin\fssp\Client::class,
        'token' => '__YOUR_TOKEN__', // Get free token from https://api-ip.fssprus.ru/
        //'retryTime' => 1, // retry every 1 sec.
        //'tryCount' => 20, // 20 counts
    ],
    // ...
],
```
### Usage
```
<?php
// Search by name
$result = Yii::$app->fssp->physical([
    'firstname' => 'Иванов',
    'secondname' => 'Иван',
    'lastname' => 'Иванович',
    'birthdate' => '10.10.1980',
    'region' => 78,
]);

// Search by company
$result = Yii::$app->fssp->legal([
    'region' => 78,
    'name' => 'ООО Рога и Копыта'
]);

// Search by number
$result = Yii::$app->fssp->ip([
    'number' => "1234/12/12345-ИП",
]);

// Group request returning many arrays!!!
$result = Yii::$app->fssp->group([
    'request' => [
        [
            'type' => 1,
            'params' => [
                'firstname' => 'Иванов',
                'secondname' => 'Иван',
                'lastname' => 'Иванович',
                'birthdate' => '10.10.1980',
                'region' => 78,
            ],
        ],
        [
            'type' => 2,
            'params' => [
                'name' => 'ООО Рога и Копыта'
                'address' => 'Ленинская',
                'region' => 78,
            ],
        ],
        [
            'type' => 3,
            'params' => [
                'number' => "1234/12/12345-ИП",
            ],
        ],
    ],
]);
```

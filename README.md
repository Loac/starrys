Platron Starrys SDK
===============
## Установка

Проект предполагает через установку с использованием composer
<pre><code>composer require platron/starrys</pre></code>

## Тесты
Для работы тестов необходим PHPUnit, для установки необходимо выполнить команду
```
composer install
```
Для того, чтобы запустить интеграционные тесты нужно скопировать файл tests/integration/MerchantSettingsSample.php удалив 
из названия Sample и вставив настройки магазина. Так же в папку tests/integration/merchant_data необходимо положить приватный
ключ и сертификат. После выполнить команду из корня проекта
```
vendor/bin/phpunit tests/integration
```

## Примеры использования

### 1. Создание чека

Смотрите в интеграционных тестах - /tests/integration/
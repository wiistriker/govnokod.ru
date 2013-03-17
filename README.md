# Говнокод.ру - по колено в коде

## О репозитории

Переписываем проект http://govnokod.ru на symfony2 (http://symfony.com)

## Установка и настройка

Скачайте репозиторий и создайте файл `app/config/parameters.yml`. Заполните его конфигурационными параметрами. За основу нужно взять файл `app/config/parameters.yml.dist`

### Ручная установка

Установите зависимости

``` bash
$ php composer.phar install
```

Обновите схему базы данных

``` bash
$ php app/console doctrine:schema:update --force
```

Заполните базу данных

``` bash
$ php app/console doctrine:fixtures:load
```

### make bootstrap

Если ваша операционная система совместима с UNIX, то предыдущие шаги можно выполнить при помощи команды

``` bash
$ make bootstrap
```

Эта команда скачает composer (в локальный файл `composer.phar`) и выполнит все шаги, описанные выше.

### Запуск сервера

Встроенный веб-сервер можно запустить через symfony-консоль:

``` bash
$ php app/console server:start
```

По умолчанию сервер будет запущен на порту 8000.

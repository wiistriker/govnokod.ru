# Говнокод.ру - по колено в коде

## О репозитории

Переписываем проект http://govnokod.ru на symfony2 (http://symfony.com)

## Установка и настройка

Скачайте репозиторий и создайте файл `app/config/parameters.yml`. Заполните его конфигурационными параметрами. За основу нужно взять файл `app/config/parameters.yml.dist`

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
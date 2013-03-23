# Говнокод.ру - по колено в коде

## О проекте

Официальный репозиторий проекта http://govnokod.ru

## Установка и настройка

Скачайте репозиторий и создайте файл `app/config/parameters.yml`. Заполните его конфигурационными параметрам. За основу нужно взять файл `app/config/parameters.yml.dist`

Обновите зависимости.

``` bash
$ php composer.phar update
```

Обновите схему базы данных

``` bash
$ php app/console doctrine:schema:update --force
```

Заполните базу данных

``` bash
$ php app/console doctrine:fixtures:load
```
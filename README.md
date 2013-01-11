# Говнокод.ру - по колено в коде

## О проекте

Официальный репозиторий проекта http://govnokod.ru

## Установка и настройка

Скачайте репозиторий и обновите зависимости

``` bash
$ php composer.phar update
```

Создайте файл `app/config/parameters.yml` и заполните его конфигурационными параметрам. За основу нужно взять файл `app/config/parameters.yml.dist`

Обновите схему базы данных

``` bash
$ php app/console doctrine:schema:update --force
```

Заполните базу данных

``` bash
$ php app/console doctrine:fixtures:load
```
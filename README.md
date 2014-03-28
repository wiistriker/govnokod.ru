# Говнокод.ру - по колено в коде

## О репозитории

Переписываем проект http://govnokod.ru на symfony2 (http://symfony.com)

## Системные требования

- php >= 5.4 (traits, short array syntax, etc)
- php_pdo
- php_intl
- http://symfony.com/doc/current/reference/requirements.html
- mysql

## Установка и настройка

Скачайте репозиторий и создайте файл `app/config/parameters.yml`. Заполните его конфигурационными параметрами. За основу нужно взять файл `app/config/parameters.yml.dist`

### Ручная установка

Установите зависимости (об установке composer читайте https://getcomposer.org/download/)

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

### Запуск сервера

Встроенный веб-сервер можно запустить через symfony-консоль:

``` bash
$ php app/console server:run --docroot=www/
```

По умолчанию сервер будет запущен на порту 8000.

### Запуск на виртуальной машине с помощью Vagrant

1. Установите [VirtualBox](https://www.virtualbox.org/) и [Vagrant](http://www.vagrantup.com/). Ознакомьтесь с [документацией по Vagrant](http://docs.vagrantup.com/v2/)
1. (Только для Windows) Добавьте пути до директорий, содержащих исполняемые файлы VirtualBox и Vagrant в переменную окружения `PATH`
1. Переименуйте `Vagrantfile.dist` в `Vagrantfile` - это сделано на случай, если вы захотите иметь свою конфигурацию с другим пробросом портов или приватным статическим IP - например, для тестирования API приложения
1. В корневой директории проекта выполните `vagrant up` - первоначальная установка может занять несколько минут
1. Выполните инструкции по установке приложения, описанные выше (создание конфигурационного файла, заполнение БД) вручную или при помощи `make`. При установке виртуальной машины будет автоматически создана БД `govnokod` и пользователь `root:root` - используйте эти данные в файле конфигурации `app/config/parameters.yml`

Учтите следующее:

- Приложение будет доступно по адресу `http://localhost:8080` - этот адрес [можно изменить](http://docs.vagrantup.com/v2/networking/private_network.html)
- Выполняя `vagrant destroy`, вы уничтожите все внесенные в БД изменения. Используйте `vagrant suspend`, если вам нужно на время выключить виртуальную машину.

## Code ##

### Style ###
Код должен быть оформлен согласно стандартам [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
и [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).
Для автоматического приведения к стандарту рекомендуется использовать [PHP Coding Standards Fixer](http://cs.sensiolabs.org/)
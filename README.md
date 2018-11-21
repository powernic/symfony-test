# Тестовое задание по Symfony

## Задание

Используя фреймворк Symfony 3+ разработать web-приложение содержащее два типа страниц ( два роута )

1. route `/`. На нем нужно вывести список новостей.
2. route `/news/{slug}/` - страница новости.

Новость состоит из заголовка, текста, и даты.
Новости можно хранить в любом хранилище (SQLite например). Можно использовать Doctrine.

В приложении должно быть два контроллера, по одному на роут.

:star: Приложение должно иметь консольную команду для добавления новых новостей в хранилище.

Полезные ссылки: 
- https://symfony.com
- https://github.com/

## Разворачивание приложения
Для установки зависимостей используем команду: `composer install`

Запуск сервера выполняется с помощью следующей команды:
`php bin/console server:run`

## Консольные команды

Приложение поддерживает 2 формата добавления новых новостей.

- С минимальная информацией: `php bin/console app:add-post`. 
- С подробной информацией: `php bin/console app:add-post -vv`.
## Обработка CSV файлов через очередь задач
### Установка

1. Клонировать репозиторий.
2. В папке с проектом выполнить: `docker-compose up -d`
3. Подключиться к PHP контейнеру:`docker-compose exec laravel.test bash`
4. В нём запустить: `composer install`
5. Потом: `./artisan migrate`
6. Заполнить БД: `./artisan db:seed`
7. Потом запустить обработчик очереди: `./artisan queue:work --timeout=0`

Проект должен стать доступен по адресу: http://localhost обработчик очереди надо держать запущенным.

В корне репозитория есть тестовые CSV файлы.

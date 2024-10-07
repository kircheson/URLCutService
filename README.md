## Инструкции по настройке проекта

1. Клонируйте репозиторий:

   **git clone https://github.com/kircheson/https://github.com/kircheson/URLCutService.git**

   **cd URLCutService**

2. Запустите Docker-контейнеры:

   **docker compose up -d**

3. Получите доступ к PHP-контейнеру:

   **docker exec -it urlcut_service_php bash**

4. Установите зависимости PHP:

   **composer install**

   **composer update**

5. Выполните миграции базы данных:

   **bin/console doctrine:migrations:migrate**

6. Загрузите фикстуры:

   **bin/console doctrine:fixtures:load**
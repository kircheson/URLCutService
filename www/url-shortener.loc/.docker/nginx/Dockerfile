FROM nginx:1.21-alpine

# Удаляем стандартный конфигурационный файл Nginx
RUN rm /etc/nginx/conf.d/default.conf

# Копируем наш конфигурационный файл в контейнер
COPY nginx.conf /etc/nginx/nginx.conf

EXPOSE 80
EXPOSE 443

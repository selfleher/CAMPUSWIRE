FROM php:8.2-cli

WORKDIR /app

COPY . .

EXPOSE 8000

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT} -t public"]
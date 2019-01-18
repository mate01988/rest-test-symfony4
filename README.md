composer install

konfiguracja bazy danych w .env (mysql://ready4s:test1234@127.0.0.1:3306/ready4s)

php bin/console server:start

dokumentacjê w nelmio uruchomiæ (na porcie z powy¿szej komendy): http://127.0.0.1:8001/api/doc

Po rejestracji nale¿y wywo³aæ logowanie na tych samych danych.

Po logowaniu, otrzymamy ApiKey, który nale¿y wkleiæ do Autoryzacji nelmio (header: X-AUTH-TOKEN).
composer install

konfiguracja bazy danych w .env (mysql://ready4s:test1234@127.0.0.1:3306/ready4s)

php bin/console server:start

dokumentacj� w nelmio uruchomi� (na porcie z powy�szej komendy): http://127.0.0.1:8001/api/doc

Po rejestracji nale�y wywo�a� logowanie na tych samych danych.

Po logowaniu, otrzymamy ApiKey, kt�ry nale�y wklei� do Autoryzacji nelmio (header: X-AUTH-TOKEN).
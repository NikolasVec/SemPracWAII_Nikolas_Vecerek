# About

This framework was created to support the teaching of the subject Development of intranet and intranet applications 
(VAII) at the [Faculty of Management Science and Informatics](https://www.fri.uniza.sk/) of
[University of Žilina](https://www.uniza.sk/). Framework demonstrates how the MVC architecture works.

# Instructions and documentation 

The framework source code is fully commented. In case you need additional information to understand,
visit the [WIKI stránky](https://github.com/thevajko/vaiicko/wiki/00-%C3%9Avodn%C3%A9-inform%C3%A1cie) (only in Slovak).

# Docker configuration

The Framework has a basic configuration for running and debugging web applications in the `<root>/docker` directory. 
All necessary services are set in `docker-compose.yml` file. After starting them, it creates the following services:

- web server (Apache) with the __PHP 8.3__ 
- MariaDB database server with a created _database_ named according `MYSQL_DATABASE` environment variable
- Adminer application for MariaDB administration

## Other notes:

- __WWW document root__ is set to the `public` in the project directory.
- The website is available at [http://localhost/](http://localhost/).
- The server includes an extension for PHP code debugging [__Xdebug 3__](https://xdebug.org/), uses the  
  port __9003__ and works in "auto-start" mode.
- PHP contains the __PDO__ extension.
- The database server is available locally on the port __3306__. The default login details can be found in `.env` file.
- Adminer is available at [http://localhost:8080/](http://localhost:8080/)

## Inštalácia a rýchle spustenie

Požiadavky
- Docker a docker-compose (odporúčané) alebo PHP 8.3 + Composer ak chcete spúšťať lokálne bez kontajnerov.
- Prístup k MariaDB/MySQL (ak používate Docker, DB je v compose službe).

1) Spustenie cez Docker
- Otvorte terminál v koreňovom adresári projektu (`c:\Users\...\SemPrac_Nikolas_Vecerek`).
- Spustite: `docker-compose -f docker\docker-compose.yml up -d`
- Po spustení navštívte: http://localhost/ (web) a http://localhost:8080/ (Adminer na správu DB).
- Prístupové údaje k DB sú nastavené v Docker compose / `.env` (ak existuje). Použite Adminer pre import SQL (sekcia Import) alebo pripojením cez mysql klient.

Import databázy
- Ak potrebujete naplniť schému alebo testovacie dáta, použite súbory v `sql/` alebo `docker` nástroje. Najjednoduchšie: prihláste sa do Adminer (http://localhost:8080/) a zvoľte Import -> nahrajte `sql/ddl.posts_01.sql` alebo iný SQL súbor.

2) Spustenie lokálne bez Docker
- Uistite sa, že máte nainštalované PHP 8.3 a Composer.
- Skopírujte alebo vytvorte súbor s nastaveniami (napr. `.env`) ak ho projekt používa, a nastavte pripojenie k DB (host, port, užívateľ, heslo, databáza).
- Nastavte web root dokumentu na priečinok `public` (napr. v Apache alebo v built-in serveri PHP):
  `php -S localhost:8000 -t public`
- Otvorte v prehliadači: http://localhost:8000/

Používanie aplikácie
- Po spustení sa otvorí hlavná stránka. Prihlásenie/registrácia sú dostupné v sekcii Auth (pozrite `App/Controllers/AuthController.php` a view v `App/Views/Auth`).
- Administrátorská sekcia je v `App/Controllers/AdminController.php` a zvyčajne na adrese `/admin`.

Logy a nahrávanie súborov
- Logy a informácie o nahratých súboroch nájdete v priečinku `storage/logs`.

Riešenie problémov
- Ak sa nenačíta stránka, skontrolujte, že server beží a že document root ukazuje na `public`.
- DB pripojenie: skontrolujte host, port a prihlasovacie údaje; ak používate Docker, DB bude dostupná na porte 3306 lokálne (podľa docker-compose).
- Chyby PHP môžete nájsť v logoch Apache/PHP alebo v `storage/logs`.

Ďalšie informácie
- Zdrojový kód frameworku je nájsť v priečinku `Framework/` a je dokumentovaný priamo v súboroch.
- Pre detailnejší popis a rozšírenia navštívte originálnu dokumentáciu projektu (WIKI) uvedenú vyššie.

!! Pri vypracovaní tejto semestrálnej práce bola využitá spolupráca s umelou inteligenciou (AI) v podobe nástroja ChatGPT od spoločnosti OpenAI. !!
!! Tento nástroj bol použitý na generovanie časti textov, ktoré boli následne upravené a prispôsobené autorom práce. Všetky generované texty boli starostlivo skontrolované a upravené, aby zodpovedali požiadavkám a štandardom danej práce. !!
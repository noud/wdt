# Symfony

## Ontwikkeling

Zorg er voor dat alle composer packages geïnstalleerd worden:

```
composer install
```

Zorg er voor dat alle node packages geïnstalleerd worden:
```
npm install
```

Migratie uitvoeren
```
bin/console doctrine:migrations:migrate
```

Fixtures draaien
```
bin/console doctrine:fixtures:load
```
### Testen
PHPUnit installeren
```
bin/phpunit install
```

PHP CS fixer
```
vendor/bin/php-cs-fixer fix --diff --dry-run -v
```

PHPstan
```
vendor/bin/phpstan analyse src tests --level max
```


### Webpack
Compileer assets eenmalig:
```
./node_modules/.bin/encore dev
```
Compileer assets automatisch wanneer bestanden veranderen:
```
./node_modules/.bin/encore dev --watch
```
Compileer assets en optimaliseer voor productie:
```
./node_modules/.bin/encore production
```

### Gebruik

In je .env.local bestand kun je de parameters wijzigen.

Als eerste dien je dan een grandToken aan te maken.
Dit kun je doen conform 
https://www.zoho.com/crm/help/developer/api/auth-request.html
Tijdens ontwikkeling is de 2de mogelijkheid, Self-Client option gebruikt.
Dus naar https://accounts.zoho.eu/developerconsole
We gebruiken daarvoor de scope
aaaserver.profile.ALL,ZohoCRM.modules.ALL,ZohoBooks.fullaccess.all,Desk.basic.READ,Desk.settings.READ,Desk.tickets.READ,Desk.tickets.UPDATE,Desk.tickets.CREATE,Desk.contacts.READ,Desk.search.READ

Nadat je het grandToken verkregen hebt moet je de
uiteindelijke tokens genereren. Dit doe je door
http://host/generate-access-token/{grandToken}


#Requirements
* composer
* node & npm
* yarn
* apache
* mysql server

#Install
`$ composer install`
`$ yarn install`
Ajouter les lignes clear cache update bdd (pas nécessaire pour le moment)

Ajouter cette ligne:
`127.0.0.1       chatsymfony` au fichier `C:\Windows\System32\drivers\etc\hosts`

Ajouter cette ligne:

`<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/public"
    ServerName chatsymfony
</VirtualHost>` au fichier : `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

run:
`$ yarn encore dev --watch`

Restart apache : go to http://chatsymfony/
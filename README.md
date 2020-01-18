# premiumizesonarr

Connects Sonarr & Jacket with Premiumize.me


# Requirements

php > 7.3
rclone > 1.50.2
composer > 1.9.1
nginx
php-fpm
docker
docker-compose


# Install

## premiumizephp
First you have to paste your apikey into the apikey.txt file

```
cd premiumizephp/ && mkdir cache && composer install
```
You can validate that everything runs correcly by executing init.php -> no output everything works

## Rclone

Next you have to mount premiumize via rclone, for this to work you either need a gui or a other computer that has rclone installed.
```
rclone config
```
Just follow the steps on the screen and make sure to name your connection "premiumize"


## Nginx

Copy & rename the nginx.config file over to your webserver
(Don't forget to restart the server systemctl restart nginx)



# How to use

All you have to do now is to execute the start.bash after the execution finished all the process should've been spawned in the background.
Try to access the web interface now

You now got Jackett & Sonarr and a premiumizeapi webservice.
```
/jackett/
/premiumapi/index.php
```
Make sure that both urls open and then your good to go
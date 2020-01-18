FROM php:7.4-cli
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Install dependencies
COPY . /usr/src/premiumizerphp
WORKDIR /usr/src/premiumizerphp
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader && rm -rf /root/.composer

# Finish composer
RUN composer dump-autoload --no-scripts --no-dev --optimize

CMD [ "php", "./watcher.php" ] 

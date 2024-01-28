FROM php:7.4-apache
LABEL maintainer fmihel76@gmail.com

RUN apt-get update \ 
    && apt-get upgrade -y \
    && apt-get install autoconf automake mc -y \
    && docker-php-ext-install mysqli \
    && mkdir /opt/xdebug/ \
    && curl https://xdebug.org/files/xdebug-3.1.6.tgz -o /opt/xdebug.tgz \ 
    && tar -zxvf /opt/xdebug.tgz -C /opt/xdebug/ \ 
    && rm -rf /opt/xdebug.tgz \
    && cd /opt/xdebug/xdebug-3.1.6/ \
    && phpize \ 
    && ./configure \
    && make\
    && cp modules/xdebug.so /usr/local/lib/php/extensions/no-debug-non-zts-20190902\
    && a2enmod headers \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer
    # install imagick
    #&& apt-get -y install libmagickwand-dev \
    #&& pecl install imagick \
    #&& docker-php-ext-enable imagick \
FROM phusion/baseimage:latest 
MAINTAINER "Benjamin M. Botwin <thecodethinker@gmail.com>"

CMD ["/sbin/my_init"]

RUN apt-add-repository ppa:nginx/stable
RUN apt-get update
RUN apt-get install -y php5-fpm php5-cgi php5-cli nginx git php5-mcrypt php5-curl php5-pgsql

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


RUN mkdir -p /var/www/app
RUN mkdir -p /var/log/nginx
RUN touch /var/log/nginx/access.log
RUN touch /var/log/nginx/error.log

WORKDIR /var/www/app
RUN curl -sS https://getcomposer.org/installer | php

#Add service directories
RUN mkdir -p /etc/service/nginx
RUN mkdir -p /etc/service/php5-fpm

#Enable Mcrypt
RUN php5enmod mcrypt
RUN php5enmod pgsql
RUN service php5-fpm restart

#Add the app and nginx conf
ADD ./docker-web/nginx.conf /etc/nginx/nginx.conf
ADD ./laravel-app/ /var/www/app
RUN chown -R :www-data /var/www/app
RUN chmod -R 775 /var/www/app/app/storage
RUN php composer.phar install -vvv

#Add Services
ADD ./docker-web/services/nginx/ /etc/service/nginx/run
RUN chmod +x /etc/service/nginx/run

ADD ./docker-web/services/php5-fpm/ /etc/service/php5-fpm/run
RUN chmod +x /etc/service/php5-fpm/run

ADD ./docker-web/services/laravel /etc/my_init.d/laravelMigrate
RUN chmod +x /etc/my_init.d/laravelMigrate

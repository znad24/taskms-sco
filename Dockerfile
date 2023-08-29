FROM dockersvr.hokben.net:55555/hokben-php5.6
MAINTAINER Eko Ari Purnomo <eko.purnomo@hokben.co.id>

COPY . /var/www/html/
COPY 000-default.conf /etc/apache2/000-default.conf

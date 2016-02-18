FROM php:5.6-cli

RUN apt-get update && apt-get install -y zip unzip gzip libc6 --no-install-recommends && rm -r /var/lib/apt/lists/*

RUN docker-php-ext-install mbstring && \
    curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD . /opt/graze/dataFile

WORKDIR /opt/graze/dataFile

CMD /bin/bash

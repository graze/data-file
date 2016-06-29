FROM graze/stats:7.0

RUN apk add --no-cache --repository "http://dl-cdn.alpinelinux.org/alpine/edge/testing" \
    php7-mbstring \
    php7-xdebug \
    perl \
    file \
    musl-utils \
    zip \
    gzip

ADD . /opt/graze/data-file

WORKDIR /opt/graze/data-file

CMD /bin/bash

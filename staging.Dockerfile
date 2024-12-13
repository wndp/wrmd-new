FROM laravelphp/vapor:php83-arm

# Add the `imagemagick` PHP extension
RUN apk --update add imagemagick ghostscript
# RUN pecl install imagick
# RUN docker-php-ext-enable imagick

# TODO: Use latest released version, after https://github.com/Imagick/imagick/issues/640 is fixed
RUN apk add git --update --no-cache && \
    git clone https://github.com/Imagick/imagick.git --depth 1 /tmp/imagick && \
    cd /tmp/imagick && \
    git fetch origin master && \
    git switch master && \
    cd /tmp/imagick && \
    phpize && \
    ./configure && \
    make && \
    make install && \
    apk del git && \
    docker-php-ext-enable imagick

COPY ./php.ini /usr/local/etc/php/conf.d/overrides.ini

COPY . /var/task

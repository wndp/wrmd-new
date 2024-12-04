FROM laravelphp/vapor:php83-arm

# Add the `imagemagick` PHP extension
RUN apk --update add imagemagick ghostscript ffmpeg
RUN pecl install imagick
RUN docker-php-ext-enable imagick

COPY ./php.ini /usr/local/etc/php/conf.d/overrides.ini

COPY . /var/task

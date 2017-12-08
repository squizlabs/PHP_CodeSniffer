FROM alpine:latest

WORKDIR /phpcs

COPY . /phpcs

RUN apk --no-cache add \
        ca-certificates \
        php7 \
        php7-ctype \
        php7-tokenizer \
        php7-simplexml \
        php7-xmlwriter \
    && ./bin/phpcs ./src ./tests \
    && ln -s /phpcs/bin/phpcs /usr/local/bin/phpcs \
    && ln -s /phpcs/bin/phpcbf /usr/local/bin/phpcbf

WORKDIR /app
VOLUME ["/app"]

CMD ["Usage: docker run --rm -v $PWD:/app <image_name> phpcs ./src ./tests --report=junit"]

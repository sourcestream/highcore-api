FROM nginx:latest

RUN apt-get update && apt-get install -y \
    wget \
 && rm -rf /var/lib/apt/lists/*

ENV DOCKERIZE_VERSION v0.3.0
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz

COPY default.conf /etc/nginx/default.tmpl

CMD dockerize -template /etc/nginx/default.tmpl:/etc/nginx/conf.d/default.conf -wait tcp://api:9000 -timeout 20s nginx -g "daemon off;"
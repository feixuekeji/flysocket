FROM php:7.3-cli
LABEL maintainer="waxiongfeifei@gmail.com" version="1.0"
RUN sed -i s@/deb.debian.org/@/mirrors.aliyun.com/@g /etc/apt/sources.list \
&& apt-get clean \
&& apt-get update \
&& apt-get install -y openssl libssl-dev \
&& apt-get install libevent-dev -y \
&& docker-php-ext-install pcntl sockets pdo_mysql \
&&  sh -c '/bin/echo -e "no\nyes\n/usr\nno\nyes\nno\nyes\nno" | pecl install event' \
&& docker-php-ext-enable event \
&& pecl install redis \
&& docker-php-ext-enable redis \
&& curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
EXPOSE 8282 8283 7273 1238
CMD /bin/bash

docker build -t workerman .
docker tag workerman:1.0 xiongfeifei/workerman:1.0
docker run -dit --name worker1 -p 8285:8282 -v /home/wwwroot:/www w2:v2
docker run -dit --name workerman -p 8282:8282 -v /home/wwwroot:/www workerman
docker exec -it workerman /bin/bash
echo  -e "net.ipv4.tcp_max_tw_buckets = 20000\nnet.core.somaxconn = 65535\nnet.ipv4.tcp_max_syn_backlog = 262144\nnet.core.netdev_max_backlog = 30000\nnet.ipv4.tcp_tw_recycle = 0\nfs.file-max = 6815744\nnet.netfilter.nf_conntrack_max = 2621440" > /etc/sysctl.conf
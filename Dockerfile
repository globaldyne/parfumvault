# =============================================================================
#
# Perfumers Vault Pro Dockerfile
# 
# =============================================================================
FROM quay.io/centos/centos:stream9

LABEL co.uk.globaldyne.component="perfumers-vault-container"  description="Perfumers Vault container image"  summary="Perfumers Vault container image"  version="PRO"  io.k8s.description="Init Container for Perfumers Vault PRO"  io.k8s.display-name="Perfumers Vault Pro Container"  io.openshift.tags="pvault,jb,perfumer,vault,jbpvault,PRO"  name="globaldyne/pvault"  maintainer="John Belekios"

ARG uid=100001
ArG gid=100001

RUN dnf install -y epel-release
RUN dnf -y update 

RUN dnf --setopt=tsflags=nodocs -y install \
	php \
	php-cli \
	php-xml \
	php-mysqlnd \
	php-gd \
	php-pear-Mail \
	php-mbstring \
	php-fpm \
	phpMyAdmin \
	git \
	python3-pip \
	procps \
	openssl \
	bc \
	mysql \
	nginx \
	&& dnf clean all

RUN dnf install make php-devel php-pear ImageMagick ImageMagick-devel pcre-devel -y
RUN pecl channel-update pecl.php.net
RUN printf "\n" | pecl install imagick
RUN dnf remove ImageMagick-devel php-devel make -y
RUN echo "extension=imagick.so" > /etc/php.d/40-ImageMagick.ini


RUN python3 -m pip install --upgrade pip \
        && python3 -m pip install --no-warn-script-location --upgrade brother_ql

RUN dnf update && dnf clean all && rm -rf /var/cache/yum/*

RUN sed -i \
	-e 's~^;date.timezone =$~date.timezone = UTC~g' \
	-e 's~^upload_max_filesize.*$~upload_max_filesize = 80M~g' \
	-e 's~^post_max_size.*$~post_max_size = 120M~g' \
	-e 's~^session.auto_start.*$~session.auto_start = 1~g' \
	/etc/php.ini

ENV LANG en_GB.UTF-8

ADD . /html


RUN sed -i "s/ 'localhost'/ getenv(\"DB_HOST\")/g" /etc/phpMyAdmin/config.inc.php
RUN echo "\$cfg['TempDir'] = '/tmp/'" >> /etc/phpMyAdmin/config.inc.php
RUN chown -R root.root /etc/phpMyAdmin/

RUN ln -s /usr/share/phpMyAdmin/ /html/phpMyAdmin
ADD scripts/php-fpm/www.conf /etc/php-fpm.d/www.conf
ADD scripts/php-fpm/php-fpm.conf /etc/php-fpm.conf
ADD scripts/entrypoint.sh /usr/bin/entrypoint.sh
ADD scripts/nginx/nginx.conf /etc/nginx/nginx.conf
ADD scripts/reset_pass.sh /usr/bin/reset_pass.sh

RUN chmod +x /usr/bin/entrypoint.sh
RUN chmod +x /usr/bin/reset_pass.sh

WORKDIR /html
STOPSIGNAL SIGQUIT
USER ${uid}
RUN mkdir /tmp/php
EXPOSE 8000
ENTRYPOINT ["entrypoint.sh"]

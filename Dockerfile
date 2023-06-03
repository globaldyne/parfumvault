# =============================================================================
#
# Perfumers Vault Docker Image Builder
# Version 1.6 
# =============================================================================

FROM quay.io/centos/centos:stream9
MAINTAINER JB <john@globaldyne.co.uk>

ARG git_repo=master

RUN dnf install -y epel-release
RUN dnf -y update 

RUN dnf --setopt=tsflags=nodocs -y install \
	httpd \
	php \
	php-cli \
	php-xml \
	php-mysqlnd \
	php-gd \
	php-zip \
	mariadb-server \
	php-mbstring \
	git \
	python3-pip \
	procps \
	openssl \
	bc \
	&& dnf clean all

RUN dnf install phpMyAdmin -y
RUN dnf install make php-devel php-pear ImageMagick ImageMagick-devel pcre-devel -y
RUN pecl channel-update pecl.php.net
RUN printf "\n" | pecl install imagick
RUN dnf remove ImageMagick-devel php-devel make -y
RUN dnf clean packages -y
RUN echo "extension=imagick.so" > /etc/php.d/40-ImageMagick.ini 

RUN python3 -m pip install --upgrade pip \
	&& python3 -m pip install --no-warn-script-location --upgrade brother_ql

RUN ln -sf /usr/share/zoneinfo/UTC /etc/localtime \
	&& echo "NETWORKING=yes" > /etc/sysconfig/network

RUN sed -i \
        -e 's~^#ServerName www.example.com:80$~ServerName pvault~g' \
        -e 's~^ServerSignature On$~ServerSignature Off~g' \
        -e 's~^ServerTokens OS$~ServerTokens Prod~g' \
        -e 's~^DirectoryIndex \(.*\)$~DirectoryIndex \1 index.php~g' \
        -e 's~^IndexOptions \(.*\)$~#IndexOptions \1~g' \
        -e 's~^IndexIgnore \(.*\)$~#IndexIgnore \1~g' \
	-e 's/\/var\/www\/html/\/html/g' \
        /etc/httpd/conf/httpd.conf

RUN sed -i \
	-e 's~^;date.timezone =$~date.timezone = UTC~g' \
	-e 's~^upload_max_filesize.*$~upload_max_filesize = 500M~g' \
	-e 's~^post_max_size.*$~post_max_size = 320M~g' \
	-e 's~^session.auto_start.*$~session.auto_start = 1~g' \
	/etc/php.ini

RUN sed -i \
	-e 's/Require local/Require all granted/g' \
	/etc/httpd/conf.d/phpMyAdmin.conf

ENV LANG en_GB.UTF-8


ADD . /html

ADD /scripts/start.sh /start.sh
ADD /scripts/pvdb.sh /usr/bin/pvdb.sh
ADD /scripts/reset_pass.sh /usr/bin/reset_pass.sh
ADD /scripts/mysql-first-time.sql /tmp/mysql-first-time.sql
ADD /scripts/pv_httpd.conf /etc/httpd/conf.d/pv_httpd.conf


WORKDIR "/html"
EXPOSE 80
VOLUME ["/var/lib/mysql", "/html/uploads", "/config"]
CMD ["/bin/bash", "/start.sh"]


# =============================================================================
#
# Perfumers Vault Pro Dockerfile
# 
# =============================================================================
FROM quay.io/centos/centos:stream9-minimal

LABEL co.uk.globaldyne.component="perfumers-vault-container"  description="Perfumers Vault container image"  summary="Perfumers Vault container image"  version="PRO"  io.k8s.description="Init Container for Perfumers Vault PRO"  io.k8s.display-name="Perfumers Vault Pro Container"  io.openshift.tags="pvault,jb,perfumer,vault,jbpvault,PRO"  name="globaldyne/pvault"  maintainer="John Belekios"

ARG uid=100001
ARG gid=100001

RUN microdnf -y update 

#A temp workaround to address microdnf module version conflicts
RUN microdnf -y module enable nginx:1.24
RUN microdnf -y module enable php:8.2

RUN microdnf --setopt=tsflags=nodocs -y install \
	php \
	php-mysqlnd \
	php-gd \
	php-mbstring \
	php-fpm \
	openssl \
	mysql \
	ncurses \
	nginx \
	git



RUN sed -i \
	-e 's~^;date.timezone =$~date.timezone = UTC~g' \
	-e 's~^upload_max_filesize.*$~upload_max_filesize = 400M~g' \
	-e 's~^post_max_size.*$~post_max_size = 400M~g' \
	-e 's~^session.auto_start.*$~session.auto_start = 1~g' \
	/etc/php.ini

ENV LANG en_GB.UTF-8

ADD . /html


ADD scripts/php-fpm/www.conf /etc/php-fpm.d/www.conf
ADD scripts/php-fpm/php-fpm.conf /etc/php-fpm.conf
ADD scripts/entrypoint.sh /usr/bin/entrypoint.sh
ADD scripts/nginx/nginx.conf /etc/nginx/nginx.conf
ADD scripts/reset_pass.sh /usr/bin/reset_pass.sh

RUN chmod +x /usr/bin/entrypoint.sh
RUN chmod +x /usr/bin/reset_pass.sh

RUN git log -1 --format="%h" > /html/COMMIT
RUN rm -rf /html/.git /html/.github
RUN microdnf -y remove git
RUN microdnf clean all && rm -rf /var/cache/yum/*

WORKDIR /html
STOPSIGNAL SIGQUIT
USER ${uid}
EXPOSE 8000
ENTRYPOINT ["entrypoint.sh"]

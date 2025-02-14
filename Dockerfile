# =============================================================================
#
# Perfumers Vault Pro Dockerfile
# 
# =============================================================================
FROM quay.io/centos/centos:stream9-minimal

LABEL co.uk.globaldyne.component="perfumers-vault-container" \
	description="Perfumers Vault container image" \
	summary="Perfumers Vault container image" \
	version="PRO" \
	io.k8s.description="Init Container for Perfumers Vault PRO" \
	io.k8s.display-name="Perfumers Vault Pro Container" \
	io.openshift.tags="pvault,jb,perfumer,vault,jbpvault,PRO" \
	name="globaldyne/pvault" \
	maintainer="John Belekios"

ARG uid=100001
ARG gid=100001

RUN microdnf -y install epel-release && \
	microdnf -y update && \
	microdnf -y module enable nginx:1.24 php:8.3 && \
	microdnf --setopt=tsflags=nodocs -y install \
	  php \
	  php-mysqlnd \
	  php-gd \
	  php-mbstring \
	  php-fpm \
	  php-pear-Mail \
	  openssl \
	  mysql \
	  ncurses \
	  nginx \
	  procps-ng \
	  diffutils \
	  golang && \
	sed -i \
	  -e 's~^;date.timezone =$~date.timezone = UTC~g' \
	  -e 's~^upload_max_filesize.*$~upload_max_filesize = 500M~g' \
	  -e 's~^post_max_size.*$~post_max_size = 500M~g' \
	  -e 's~^session.auto_start.*$~session.auto_start = 1~g' \
	  /etc/php.ini && \
	microdnf clean all && \
	rm -rf /var/cache/yum/*

ENV LANG=en_GB.UTF-8

ADD . /html
RUN if [ -f .git/COMMIT_EDITMSG ]; then \
	cat .git/COMMIT_EDITMSG | sed -n 's/^\[\(.*\)\].*/\[\1\]/p' > /html/COMMIT; \
	fi

ADD scripts/php-fpm/www.conf /etc/php-fpm.d/www.conf
ADD scripts/php-fpm/php-fpm.conf /etc/php-fpm.conf
ADD scripts/entrypoint.sh /usr/bin/entrypoint.sh
ADD scripts/nginx/nginx.conf /etc/nginx/nginx.conf
ADD scripts/reset_pass.sh /usr/bin/reset_pass.sh
ADD scripts/update_db_schema.sh /usr/bin/update_db_schema.sh

RUN chmod +x /usr/bin/entrypoint.sh /usr/bin/reset_pass.sh /usr/bin/update_db_schema.sh && \
	rm -rf /html/.git /html/.github /html/helpers /html/docker-compose /html/k8s

WORKDIR /html/scripts/session_monitor
RUN [ ! -f go.mod ] && go mod init session_monitor || true && \
	go mod tidy && go build -o session_monitor . && \
	cp session_monitor /usr/bin/session_monitor && \
	chmod +x /usr/bin/session_monitor && \
	rm -rf /html/scripts

WORKDIR /html
STOPSIGNAL SIGQUIT
USER ${uid}
EXPOSE 8000
ENTRYPOINT ["entrypoint.sh"]

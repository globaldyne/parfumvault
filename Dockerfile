# =============================================================================
#
# Perfumers Vault Pro Dockerfile
# 
# =============================================================================

# Base image
FROM quay.io/centos/centos:stream10-minimal

# Metadata labels
LABEL com.perfodynelabs.component="perfumers-vault-container" \
	description="Perfumers Vault container image" \
	summary="Perfumers Vault container image" \
	version="PRO" \
	io.k8s.description="Init Container for Perfumers Vault PRO" \
	io.k8s.display-name="Perfumers Vault Pro Container" \
	io.openshift.tags="pvault,perfumersvault" \
	name="perfodynelabs/pvault" \
	maintainer="John Belekios"

# Set default environment variables
ENV LANG=en_GB.UTF-8

# Define user and group IDs
ARG uid=100001
ARG gid=100001
ARG INSTALL_SESS_MONITOR=false


# Update the system
RUN microdnf clean all && microdnf update -y
RUN microdnf -y update
# Install PHP and other dependencies
RUN microdnf --setopt=tsflags=nodocs -y install \
	  php \
	  php-mysqlnd \
	  php-gd \
	  php-mbstring \
	  php-fpm \
	  php-pear \
	  openssl \
	  mysql \
	  ncurses \
	  nginx \
	  procps-ng \
	  diffutils

# Configure PHP settings with environment variable defaults
RUN sed -i \
	  -e 's~^;date.timezone =$~date.timezone = ${PHP_TIMEZONE:-UTC}~g' \
	  -e 's~^upload_max_filesize.*$~upload_max_filesize = ${PHP_UPLOAD_MAX_FILESIZE:-500M}~g' \
	  -e 's~^post_max_size.*$~post_max_size = ${PHP_POST_MAX_SIZE:-500M}~g' \
	  -e 's~^session.auto_start.*$~session.auto_start = 1~g' \
	  -e 's~^memory_limit.*$~memory_limit = ${PHP_MEMORY_LIMIT:-512M}~g' \
	  /etc/php.ini

# Install additional PHP extensions
RUN pear install mail Mail_mime Auth_SASL Net_SMTP

# Conditionally install Go for session monitor
RUN if [ "$INSTALL_SESS_MONITOR" = "true" ]; then \
	microdnf -y install golang; \
	fi

# Clean up package manager cache
RUN microdnf clean all && \
	rm -rf /var/cache/yum/*

# Add application files
ADD . /html

# Extract commit message if available
RUN if [ -f .git/COMMIT_EDITMSG ]; then \
	cat .git/COMMIT_EDITMSG | sed -n 's/^\[\(.*\)\].*/\[\1\]/p' > /html/COMMIT; \
	fi

# Add configuration and script files
ADD scripts/php-fpm/www.conf /etc/php-fpm.d/www.conf
ADD scripts/php-fpm/php-fpm.conf /etc/php-fpm.conf
ADD scripts/entrypoint.sh /usr/bin/entrypoint.sh
ADD scripts/nginx/nginx.conf /etc/nginx/nginx.conf
ADD scripts/reset_pass.sh /usr/bin/reset_pass.sh
ADD scripts/create_db_schema.sh /usr/bin/create_db_schema.sh
ADD scripts/update_db_schema.sh /usr/bin/update_db_schema.sh

# Build Go-based session monitor if enabled
RUN if [ "$INSTALL_SESS_MONITOR" = "true" ]; then \
		cd /html/scripts/session_monitor && \
		[ ! -f go.mod ] && go mod init session_monitor || true && \
		go mod tidy && go build -o session_monitor . && \
		cp session_monitor /usr/bin/session_monitor && \
		chmod +x /usr/bin/session_monitor; \
	fi

# Clean up unnecessary files
RUN rm -rf /html/scripts /html/helpers /html/docker-compose /html/k8s /html/.git /html/.github 

# Set executable permissions for scripts
RUN chmod +x /usr/bin/entrypoint.sh /usr/bin/reset_pass.sh /usr/bin/update_db_schema.sh /usr/bin/create_db_schema.sh

# Set working directory
WORKDIR /html

# Define stop signal
STOPSIGNAL SIGQUIT

# Set default user
USER ${uid}

# Expose application port
EXPOSE 8000

# Define entrypoint
ENTRYPOINT ["entrypoint.sh"]

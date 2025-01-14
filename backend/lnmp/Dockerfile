FROM ubuntu:22.04

# Let the container know that there is no tty
ENV DEBIAN_FRONTEND noninteractive
ENV php_conf /etc/php/8.2/fpm/php.ini
ENV fpm_conf /etc/php/8.2/fpm/pool.d/www.conf
ENV COMPOSER_VERSION 2.3.10
ENV LD_LIBRARY_PATH="/opt/sqlanywhere16/lib64"
ENV SQLANY16="/opt/sqlanywhere16"
ENV TZ=Asia/Taipei

WORKDIR /www
# Install Basic Requirements
RUN buildDeps='curl gcc make autoconf libc-dev zlib1g-dev pkg-config git software-properties-common' \
    && set -x \
    && apt-get update \
    && apt-get install --no-install-recommends $buildDeps --no-install-suggests -q -y gnupg2 dirmngr apt-transport-https lsb-release ca-certificates \
    && add-apt-repository ppa:ondrej/php \
    && apt-get update \
    && apt-get install --no-install-recommends --no-install-suggests -q -y \
    nginx \
    libzip-dev \
    libxml2-dev \
    php-pear \
    php8.2-fpm \
    php8.2-cli \
    php8.2-bcmath \
    php8.2-dev \
    php8.2-common \
    php8.2-mbstring \
    php8.2-curl \
    php8.2-mysql \
    php8.2-zip \
    php8.2-pgsql \
    php8.2-intl \
    php8.2-xml \
    php8.2-gd \
    && mkdir -p /run/php \
    && echo "#!/bin/sh\nexit 0" > /usr/sbin/policy-rc.d \
    && sed -i -e "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g" ${php_conf} \
    && sed -i -e "s/;date.timezone =/date.timezone = Asia\/Taipei/g" ${php_conf} \
    && sed -i -e "s/memory_limit\s*=\s*.*/memory_limit = 256M/g" ${php_conf} \
    && sed -i -e "s/upload_max_filesize\s*=\s*2M/upload_max_filesize = 100M/g" ${php_conf} \
    && sed -i -e "s/post_max_size\s*=\s*8M/post_max_size = 100M/g" ${php_conf} \
    && sed -i -e "s/variables_order = \"GPCS\"/variables_order = \"EGPCS\"/g" ${php_conf} \
    && sed -i -e "s/;daemonize\s*=\s*yes/daemonize = no/g" /etc/php/8.2/fpm/php-fpm.conf \
    && sed -i -e "s/error_log = \/var\/log\/php8\.2-fpm\.log/error_log = \/dev\/stderr/" /etc/php/8.2/fpm/php-fpm.conf \
    && sed -i -e "s/;access\.log = log\/$pool\.access\.log/;access.log = \/dev\/stdout/" ${fpm_conf} \
    && sed -i -e "s/;catch_workers_output\s*=\s*yes/catch_workers_output = yes/g" ${fpm_conf} \
    && sed -i -e "s/pm.max_children = 5/pm.max_children = 4/g" ${fpm_conf} \
    && sed -i -e "s/pm.start_servers = 2/pm.start_servers = 3/g" ${fpm_conf} \
    && sed -i -e "s/pm.min_spare_servers = 1/pm.min_spare_servers = 2/g" ${fpm_conf} \
    && sed -i -e "s/pm.max_spare_servers = 3/pm.max_spare_servers = 4/g" ${fpm_conf} \
    && sed -i -e "s/pm.max_requests = 500/pm.max_requests = 200/g" ${fpm_conf} \
    && sed -i -e "s/\ \/run\/php\/php8.2-fpm.sock/0.0.0.0:9000/g" ${fpm_conf} \
    && sed -i -e "s/www-data/nginx/g" ${fpm_conf} \
    && sed -i -e "s/^;clear_env = no$/clear_env = no/" ${fpm_conf} \
    # import php sqlanywhere lib
    && echo "extension = /var/lib/sqlanywhere/lib64/php-8.2.0_sqlanywhere_r.so" > /etc/php/8.2/mods-available/SQLAnywhere.ini \
    && ln -sf /etc/php/8.2/mods-available/SQLAnywhere.ini /etc/php/8.2/fpm/conf.d/40-SQLAnywhere.ini \
    && ln -sf /etc/php/8.2/mods-available/SQLAnywhere.ini /etc/php/8.2/cli/conf.d/40-SQLAnywhere.ini \
    # Install Composer
    && curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
    && curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
    && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
    && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --version=${COMPOSER_VERSION} \
    && rm -rf /tmp/composer-setup.php \
    # Set openssl
    && sed -i -e "s/DEFAULT:@SECLEVEL=2/DEFAULT@SECLEVEL=0/g" /etc/ssl/openssl.cnf\
    # Install suprevisord
    && apt install --no-install-recommends --no-install-suggests -q -y \
    python3 \
    python3-pip \
    supervisor \
    && pip install git+https://github.com/coderanger/supervisor-stdout \
    # Clean up
    && rm -rf /tmp/pear \
    && apt-get purge -y --auto-remove $buildDeps \
    && apt-get clean \
    && apt-get autoremove \
    && rm -rf /var/lib/apt/lists/*

COPY ./config/nginx/default.conf /etc/nginx/sites-available/default

# Supervisor config
COPY ./config/supervisord.conf /etc/supervisord.conf

# Install SQLAnywhere
ADD ./tools/SQLAnywhere-php-8.2_Linux.tar.gz /var/lib/sqlanywhere
ADD ./tools/sqla16_client_linux_x86x64.tar.gz /tmp/tools
RUN sh /tmp/tools/client1600/setup -ss -I_accept_the_license_agreement

# change uid & gid
RUN addgroup --gid 1001 nginx && adduser --system --uid 1001 -gid 1001 nginx

EXPOSE 9000
EXPOSE 6001
EXPOSE 80

CMD /usr/bin/supervisord -n -c /etc/supervisord.conf

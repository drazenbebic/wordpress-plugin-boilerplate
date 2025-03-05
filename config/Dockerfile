FROM php:8.3-fpm

# Dependencies
RUN apt-get -y update && \
    apt-get -y install \
    mariadb-client \
    sshpass \
    unzip \
    msmtp \
    nano \
    vim \
    procps \
    git \
    sudo \
    zsh \
    curl

# Install PHP extensions
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions @composer xdebug imagick mysqli

# Install WordPress CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x wp-cli.phar && \
    mv wp-cli.phar /usr/local/bin/wp

# Create a new user named 'admin'
ARG USERNAME=admin
ARG USER_UID=1000
ARG USER_GID=1000
RUN groupadd --gid $USER_GID $USERNAME && \
    useradd --uid $USER_UID --gid $USER_GID --create-home $USERNAME && \
    usermod --shell /bin/zsh $USERNAME && \
    echo "$USERNAME ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers && \
    usermod -a -G www-data $USERNAME

# Set permissions and ownership
RUN chown -R $USERNAME:www-data /var/www/html && \
    chmod -R g+rwX /var/www/html

# Cleanup
RUN apt-get -y autoremove && rm -rf /var/lib/apt/lists/*

# Switch to the new user
USER admin

# Install Oh My Zsh for the new user
RUN sh -c "$(curl -fsSL https://raw.githubusercontent.com/ohmyzsh/ohmyzsh/master/tools/install.sh)" "" --unattended

# Set the working directory
WORKDIR /var/www/html

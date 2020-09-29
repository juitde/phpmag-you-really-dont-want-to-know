# Demo Application *You Really Don't Want to Know*

This application demonstrates how to use asymmetric (public/private key)
encryption to en- and decrypt data on the client (in the web browser).
This allows to have fully encrypted data only in all communication between
client and server and on the server itself. The secret private key can
remain safely outside systems that are publically accessible.

## Installation Guide

### TLDR

    docker-compose up -d

### Requirements

In order to run the application in a docker container you need to have
a docker installation on your computer. Git is required to to clone this
repository.

- [Git](https://git-scm.com/)
- [Docker](https://www.docker.com/)
- [Composer](https://getcomposer.org/)

### Running the application

1. Clone the repository to a local folder on your computer
2. Open a Terminal / Command Line Window and change the directory to the
   root of the cloned repository
3.  Start the docker containers with `docker-compose up`
4. Install the dependencies of the application by running `composer install`
5. To access the application point your web browser to http://localhost:8888/

### Tipps and Troubleshooting

#### Stopping the docker container

Run `docker-compose down` from the command line in the root directory of the
cloned repository.
    
#### Installing dependencies with composer

To avoid problems resulting from a different PHP version installed on your
computer locally and the PHP version used in the docker container you can
execute *Composer* from within the docker container.

In order to do so, just place a copy of the composer.phar file in the root
folder of the cloned repository. Then run `docker-compose exec php bash`.
This will open a *BASH* session inside the docker container. From there
you can run `php ./composer.phar install` to install all dependencies.

# Disclaimer

This application is meant for demonstration and educational purposes only
and comes without any warranty. Please consult the [LICENSE](LICENSE) for
further details.

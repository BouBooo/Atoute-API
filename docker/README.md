# Local installation for development

## Prerequisites

Install Docker

Install docker-compose

### Mac

Install Docker for Mac : https://store.docker.com/editions/community/docker-ce-desktop-mac

## Source code

Get the source code of the other projets that you need:

```bash
# Back (Symfony 5.1)
git clone https://gitlab.com/atoute/back.git
```

## Stack

Add in your hosts file:

```
127.0.0.1 back-local.atoute.com
```

Run the docker stack:

```bash
cd back/
docker-compose build
docker-compose up -d
```

## Applications

Check this URL:

http://back-local.atoute.com:8080

If you want to install one of the apps:

- [Back](https://gitlab.com/atoute/back)

If you need to open a bash on one of the containers:

```bash
# Apache server
docker-compose exec php bash
```
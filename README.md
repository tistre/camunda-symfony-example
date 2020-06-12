# camunda-symfony-example

An example of using the [camunda_php_client](https://github.com/tistre/camunda_php_client/) library from within a minimal 
(no Web UI, just command line) Symfony 5 application.

## Installation

Install dependencies (Symfony, libraries) using the Composer Docker image:

```
$ cd app
$ docker run --rm --interactive --tty \
    --volume $PWD:/app \
    --volume ${COMPOSER_HOME:-$HOME/.composer}:/tmp \
    composer install
```

Run the Symfony Console once to test whether things are working:

```
$ cd app
$ docker run -it --rm \
  --volume "$PWD":/opt/app --workdir /opt/app \
  php:7.4-cli ./bin/console
```

## Starting 

Start Camunda and the worker process via Docker:

```
$ docker-compose -f docker-compose.yml up -d
```

During the first start, Camunda will take 1-2 minutes to initialize. 

Then you should be able to access the Camunda UI on http://localhost:8080/camunda/ (log in as demo/demo). 

## Using

Create 20 example process instances:

```
$ docker exec -it camunda-symfony-example_fizzbuzzworker_1 \
  /opt/app/bin/console app:fizzbuzz-start-process 20
```

Inspect them in the [Camunda Cockpit](http://localhost:8080/camunda/app/cockpit/default/), 
and "review" (= finish) them in the [Camunda Tasklist](http://localhost:8080/camunda/app/tasklist/default/) when you’re done.

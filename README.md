# camunda-symfony-example

An example of using the [camunda_php_client](https://github.com/tistre/camunda_php_client/) library from within a minimal 
(no Web UI, just command line) Symfony 6 application.

## Installation

Install dependencies (Symfony, libraries):

```
$ docker compose run --rm --workdir /opt/app php composer install 
```

Run the Symfony Console once to test whether things are working:

```
$ docker compose run --rm php /opt/app/bin/console debug:messenger

Messenger
=========

messenger.bus.default
---------------------

 The following messages can be dispatched:

 ------------------------------------------------------------------ 
  App\Message\FizzBuzz\BuzzMessage                                  
      handled by App\MessageHandler\FizzBuzz\BuzzHandler            
                                                                    
  App\Message\FizzBuzz\CombineResultsMessage                        
      handled by App\MessageHandler\FizzBuzz\CombineResultsHandler  
                                                                    
  App\Message\FizzBuzz\FizzMessage                                  
      handled by App\MessageHandler\FizzBuzz\FizzHandler            
                                                                    
  App\Message\FizzBuzz\GetDivisorsMessage                           
      handled by App\MessageHandler\FizzBuzz\GetDivisorsHandler     
                                                                    
 ------------------------------------------------------------------ 
```

## Starting 

Start Camunda and the worker process via Docker:

```
$ docker compose up -d
```

During the first start, Camunda will take 1-2 minutes to initialize. 

Then you should be able to access the Camunda UI on http://localhost:8080/camunda/ (log in as demo/demo). 

Deploy bpmn/FizzBuzz.bpmn to http://localhost:8080/engine-rest via the Camunda Modeler.

## Using

Create 20 example [FizzBuzz](https://en.wikipedia.org/wiki/Fizz_buzz) process instances:

```
$ docker compose run --rm php /opt/app/bin/console app:start-fizzbuzz-process --numInstances=20 21
```

Inspect them in the [Camunda Cockpit](http://localhost:8080/camunda/app/cockpit/default/), 
and "review" (= finish) them in the [Camunda Tasklist](http://localhost:8080/camunda/app/tasklist/default/) when you’re done.

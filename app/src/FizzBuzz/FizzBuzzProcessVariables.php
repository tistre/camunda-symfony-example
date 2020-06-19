<?php

namespace App\FizzBuzz;


class FizzBuzzProcessVariables
{
    const CAMUNDA_PROCESS_DEFINITION_KEY = 'FizzBuzz';
    const CAMUNDA_VAR_NUMBER = 'number';
    const CAMUNDA_VAR_DIVISIBLE_BY_3 = 'divisibleBy3';
    const CAMUNDA_VAR_DIVISIBLE_BY_5 = 'divisibleBy5';
    const CAMUNDA_VAR_IS_FIZZ = 'isFizz';
    const CAMUNDA_VAR_IS_BUZZ = 'isBuzz';
    const CAMUNDA_VAR_OUTPUT = 'output';
}
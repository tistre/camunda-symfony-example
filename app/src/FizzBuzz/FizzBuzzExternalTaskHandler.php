<?php

namespace App\FizzBuzz;

use StrehleDe\CamundaClient\CamundaExternalTask;
use StrehleDe\CamundaClient\CamundaExternalTaskHandler;
use StrehleDe\CamundaClient\CamundaTopic;
use StrehleDe\CamundaClient\CamundaTopicBag;
use StrehleDe\CamundaClient\CamundaUtils;
use StrehleDe\CamundaClient\Variable\CamundaBooleanVariable;
use StrehleDe\CamundaClient\Variable\CamundaStringVariable;
use StrehleDe\CamundaClient\Variable\CamundaVariableBag;


class FizzBuzzExternalTaskHandler extends CamundaExternalTaskHandler
{
    /**
     * @inheritDoc
     */
    public function getHandledTopics(): CamundaTopicBag
    {
        $topicNames = [
            'FizzBuzz_GetDivisors',
            'FizzBuzz_Fizz',
            'FizzBuzz_Buzz',
            'FizzBuzz_CombineResults'
        ];

        $topics = new CamundaTopicBag();

        foreach ($topicNames as $topicName) {
            $topics[] = (new CamundaTopic())->setTopicName($topicName)->setLockDuration(1000);
        }

        return $topics;
    }


    /**
     * @param CamundaExternalTask $externalTask
     * @param CamundaVariableBag $updateVariables
     */
    protected function handle_FizzBuzz_GetDivisors(
        CamundaExternalTask $externalTask,
        CamundaVariableBag $updateVariables
    ): void {
        CamundaUtils::assertRequiredVariables(
            $externalTask->getVariables(),
            [FizzBuzzProcessVariables::CAMUNDA_VAR_NUMBER]
        );

        $number = $externalTask->getVariables()->getValue(FizzBuzzProcessVariables::CAMUNDA_VAR_NUMBER);

        $updateVariables[FizzBuzzProcessVariables::CAMUNDA_VAR_DIVISIBLE_BY_3] = new CamundaBooleanVariable($number % 3 === 0);
        $updateVariables[FizzBuzzProcessVariables::CAMUNDA_VAR_DIVISIBLE_BY_5] = new CamundaBooleanVariable($number % 5 === 0);
    }


    /**
     * @param CamundaExternalTask $externalTask
     * @param CamundaVariableBag $updateVariables
     */
    protected function handle_FizzBuzz_Fizz(
        CamundaExternalTask $externalTask,
        CamundaVariableBag $updateVariables
    ): void {
        CamundaUtils::assertRequiredVariables(
            $externalTask->getVariables(),
            [FizzBuzzProcessVariables::CAMUNDA_VAR_DIVISIBLE_BY_3]
        );

        $divisible = $externalTask->getVariables()->getValue(FizzBuzzProcessVariables::CAMUNDA_VAR_DIVISIBLE_BY_3);

        $updateVariables[FizzBuzzProcessVariables::CAMUNDA_VAR_IS_FIZZ] = new CamundaBooleanVariable($divisible);
    }


    /**
     * @param CamundaExternalTask $externalTask
     * @param CamundaVariableBag $updateVariables
     */
    protected function handle_FizzBuzz_Buzz(
        CamundaExternalTask $externalTask,
        CamundaVariableBag $updateVariables
    ): void {
        CamundaUtils::assertRequiredVariables(
            $externalTask->getVariables(),
            [FizzBuzzProcessVariables::CAMUNDA_VAR_DIVISIBLE_BY_5]
        );

        $divisible = $externalTask->getVariables()->getValue(FizzBuzzProcessVariables::CAMUNDA_VAR_DIVISIBLE_BY_5);

        $updateVariables[FizzBuzzProcessVariables::CAMUNDA_VAR_IS_BUZZ] = new CamundaBooleanVariable($divisible);
    }


    /**
     * @param CamundaExternalTask $externalTask
     * @param CamundaVariableBag $updateVariables
     */
    protected function handle_FizzBuzz_CombineResults(
        CamundaExternalTask $externalTask,
        CamundaVariableBag $updateVariables
    ): void {
        CamundaUtils::assertRequiredVariables(
            $externalTask->getVariables(),
            [FizzBuzzProcessVariables::CAMUNDA_VAR_NUMBER]
        );

        $isFizz = $externalTask->getVariables()->getValue(FizzBuzzProcessVariables::CAMUNDA_VAR_IS_FIZZ) ?? false;
        $isBuzz = $externalTask->getVariables()->getValue(FizzBuzzProcessVariables::CAMUNDA_VAR_IS_BUZZ) ?? false;

        if (!($isFizz || $isBuzz)) {
            $output = $externalTask->getVariables()->getValue(FizzBuzzProcessVariables::CAMUNDA_VAR_NUMBER);
        } else {
            $output = sprintf(
                '%s%s',
                $isFizz ? 'Fizz' : '',
                $isBuzz ? 'Buzz' : ''
            );
        }

        $updateVariables[FizzBuzzProcessVariables::CAMUNDA_VAR_OUTPUT] = new CamundaStringVariable($output);
    }
}
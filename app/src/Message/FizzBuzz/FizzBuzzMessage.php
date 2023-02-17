<?php

namespace App\Message\FizzBuzz;

use App\CamundaTransport\Message\CamundaProcessMessage;
use StrehleDe\CamundaClient\Variable\CamundaBooleanVariable;
use StrehleDe\CamundaClient\Variable\CamundaIntegerVariable;
use StrehleDe\CamundaClient\Variable\CamundaStringVariable;


/**
 * Class AssetRemoveBg
 * @package App\Message
 */
class FizzBuzzMessage extends CamundaProcessMessage
{
    public const PROCESS_DEFINITION_KEY = 'FizzBuzz';

    public const VAR_IS_BUZZ = 'isBuzz';
    public const VAR_IS_DIVISIBLE_BY_3 = 'isDivisibleBy3';
    public const VAR_IS_DIVISIBLE_BY_5 = 'isDivisibleBy5';
    public const VAR_IS_FIZZ = 'isFizz';
    public const VAR_NUMBER = 'number';
    public const VAR_OUTPUT = 'output';

    protected ?bool $isBuzz = null;
    protected ?bool $isDivisibleBy3 = null;
    protected ?bool $isDivisibleBy5 = null;
    protected ?bool $isFizz = null;
    protected ?int $number = null;
    protected ?string $output = null;

    // TODO use PHP 8 attributes instead
    protected array $propertyToVariable = [
        self::VAR_IS_BUZZ => CamundaBooleanVariable::class,
        self::VAR_IS_DIVISIBLE_BY_3 => CamundaBooleanVariable::class,
        self::VAR_IS_DIVISIBLE_BY_5 => CamundaBooleanVariable::class,
        self::VAR_IS_FIZZ => CamundaBooleanVariable::class,
        self::VAR_NUMBER => CamundaIntegerVariable::class,
        self::VAR_OUTPUT => CamundaStringVariable::class
    ];

    /**
     * @return bool|null
     */
    public function getIsBuzz(): ?bool
    {
        return $this->isBuzz;
    }

    /**
     * @param bool|null $isBuzz
     * @return self
     */
    public function setIsBuzz(?bool $isBuzz): self
    {
        $this->isBuzz = $isBuzz;
        $this->isDirty[self::VAR_IS_BUZZ] = true;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsDivisibleBy3(): ?bool
    {
        return $this->isDivisibleBy3;
    }

    /**
     * @param bool|null $isDivisibleBy3
     * @return self
     */
    public function setIsDivisibleBy3(?bool $isDivisibleBy3): self
    {
        $this->isDivisibleBy3 = $isDivisibleBy3;
        $this->isDirty[self::VAR_IS_DIVISIBLE_BY_3] = true;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsDivisibleBy5(): ?bool
    {
        return $this->isDivisibleBy5;
    }

    /**
     * @param bool|null $isDivisibleBy5
     * @return self
     */
    public function setIsDivisibleBy5(?bool $isDivisibleBy5): self
    {
        $this->isDivisibleBy5 = $isDivisibleBy5;
        $this->isDirty[self::VAR_IS_DIVISIBLE_BY_5] = true;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsFizz(): ?bool
    {
        return $this->isFizz;
    }

    /**
     * @param bool|null $isFizz
     * @return self
     */
    public function setIsFizz(?bool $isFizz): self
    {
        $this->isFizz = $isFizz;
        $this->isDirty[self::VAR_IS_FIZZ] = true;
        return $this;
    }


    /**
     * @return int|null
     */
    public function getNumber(): ?int
    {
        return $this->number;
    }


    /**
     * @param int $number
     * @return self
     */
    public function setNumber(int $number): self
    {
        $this->number = $number;
        $this->isDirty[self::VAR_NUMBER] = true;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOutput(): ?string
    {
        return $this->output;
    }

    /**
     * @param string|null $output
     * @return self
     */
    public function setOutput(?string $output): self
    {
        $this->output = $output;
        $this->isDirty[self::VAR_OUTPUT] = true;
        return $this;
    }
}
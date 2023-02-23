<?php

namespace App\CamundaTransport\Message;

use StrehleDe\CamundaClient\CamundaExternalTask;
use StrehleDe\CamundaClient\Exception\CamundaInvalidInputException;
use StrehleDe\CamundaClient\Variable\CamundaVariableBag;


class CamundaProcessMessage
{
    // Override this in the base message for your process
    public const PROCESS_DEFINITION_KEY = '';

    // Override this in the message that handles a topic
    public const TOPIC_NAME = '';

    // Optionally override this in the message that handles a topic
    public const LOCK_DURATION = 60000; // 60 seconds

    protected ?string $businessKey = null;

    protected string $errorMessage = '';
    protected string $errorDetails = '';

    protected array $propertyToVariable = [];
    protected array $isDirty = [];


    /**
     * @return string
     */
    public function getProcessDefinitionKey(): string
    {
        return static::PROCESS_DEFINITION_KEY;
    }


    /**
     * @return string|null
     */
    public function getBusinessKey(): ?string
    {
        return $this->businessKey ?? null;
    }


    /**
     * @param string $businessKey
     * @return self
     */
    public function setBusinessKey(string $businessKey): self
    {
        $this->businessKey = $businessKey;
        return $this;
    }


    /**
     * @return string
     */
    public function getTopicName(): string
    {
        return static::TOPIC_NAME;
    }


    /**
     * @return int
     */
    public function getLockDuration(): int
    {
        return static::LOCK_DURATION;
    }


    /**
     * @param array $propertyNames
     */
    public function assertRequiredProperties(array $propertyNames): void
    {
        foreach ($propertyNames as $propertyName) {
            if (!isset($this->{$propertyName})) {
                throw new CamundaInvalidInputException(sprintf('%s: Required variable "%s" is not set',
                    get_class($this), $propertyName));
            }

            if (strlen($this->{$propertyName}) === 0) {
                throw new CamundaInvalidInputException(sprintf('%s: Required variable "%s" is set but empty',
                    get_class($this), $propertyName));
            }
        }
    }


    /**
     * Serialize message state to Camunda variables
     *
     * @param bool $onlyDirty
     * @return CamundaVariableBag
     */
    public function getCamundaVariables(bool $onlyDirty = false): CamundaVariableBag
    {
        $variables = new CamundaVariableBag();

        foreach ($this->propertyToVariable as $propertyName => $className) {
            // When completing an external task, send only updated variables
            if ($onlyDirty && !isset($this->isDirty[$propertyName])) {
                continue;
            }

            // Ignore undefined/null properties
            if (!isset($this->{$propertyName}) || ($this->{$propertyName} === null)) {
                continue;
            }

            $variables[$propertyName] = new $className($this->{$propertyName});
        }

        return $variables;
    }


    /**
     * Unserialize message state from Camunda variables
     *
     * @param CamundaVariableBag $variables
     * @return void
     */
    protected function setCamundaVariables(CamundaVariableBag $variables): void
    {
        foreach (array_keys($this->propertyToVariable) as $propertyName) {
            if (isset($variables[$propertyName])) {
                $this->{$propertyName} = $variables[$propertyName]->getValue();
            }
        }
    }


    /**
     * @param CamundaExternalTask $externalTask
     * @return self
     */
    public function fromExternalTask(CamundaExternalTask $externalTask): self
    {
        if ($externalTask->getBusinessKey() !== null) {
            $this->setBusinessKey($externalTask->getBusinessKey());
        }

        $this->setCamundaVariables($externalTask->getVariables());

        return $this;
    }


    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }


    /**
     * @param string $message
     */
    public function setErrorMessage(string $message): void
    {
        $this->errorMessage = $message;
    }


    /**
     * @return string
     */
    public function getErrorDetails(): string
    {
        return $this->errorDetails;
    }


    /**
     * @param string $details
     */
    public function setErrorDetails(string $details): void
    {
        $this->errorDetails = $details;
    }
}
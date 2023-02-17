<?php

namespace App\CamundaTransport\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;


/**
 * Class CamundaWorkerIdStamp
 * @package App\CamundaTransport
 */
class CamundaWorkerIdStamp implements StampInterface
{
    private string $id;


    /**
     * CamundaWorkerIdStamp constructor.
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }


    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
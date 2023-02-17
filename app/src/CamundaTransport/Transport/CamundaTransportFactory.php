<?php

namespace App\CamundaTransport\Transport;

use App\CamundaTransport\CamundaTopicList;
use Psr\Log\LoggerInterface;
use StrehleDe\CamundaClient\CamundaClient;
use StrehleDe\CamundaClient\CamundaConfig;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;


/**
 * Class CamundaTransportFactory
 * @package App\CamundaTransport
 */
class CamundaTransportFactory implements TransportFactoryInterface
{
    /**
     * CamundaTransportFactory constructor.
     * @param LoggerInterface $logger
     * @param CamundaTopicList $topicList
     */
    public function __construct(
        protected LoggerInterface $logger,
        protected CamundaTopicList $topicList
    )
    {
    }


    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        // TODO: sensible DSN to URL conversion
        $url = str_replace('camunda://', 'http://', $dsn);

        $config = new CamundaConfig($url);

        return new CamundaTransport(new CamundaClient($config, $this->logger), $this->topicList);
    }


    public function supports(string $dsn, array $options): bool
    {
        return (str_starts_with($dsn, 'camunda://'));
    }
}
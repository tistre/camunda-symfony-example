<?php

namespace App\DependencyInjection\Compiler;

use App\CamundaTransport\CamundaTopicList;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;


class CamundaTopicPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(CamundaTopicList::class)) {
            return;
        }

        $definition = $container->findDefinition(CamundaTopicList::class);
        $taggedServices = $container->findTaggedServiceIds('app.camunda_topic');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addTopic', [new Reference($id)]);
        }
    }
}
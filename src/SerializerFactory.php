<?php

namespace CQRSFactory;

use CQRS\Serializer\HybridSerializer;
use CQRS\Serializer\JsonSerializer;
use CQRS\Serializer\SerializerInterface;
use Psr\Container\ContainerInterface;

class SerializerFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $configKey
     * @return SerializerInterface
     */
    protected function createWithConfig(ContainerInterface $container, string $configKey): SerializerInterface
    {
        $config = $this->retrieveConfig($container, $configKey, 'serializer');

        if ($config['class'] === HybridSerializer::class) {
            $dictionary = $config['event_type_map'] ?: [];

            $jsonSerializer = new JsonSerializer();

            return new $config['class'](
                $jsonSerializer,
                $dictionary
            );
        }

        return new $config['class'](
            is_string($config['instance'])
                ? $container->get($config['instance'])
                : $config['instance']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig(): array
    {
        return [
            'class' => JsonSerializer::class,
            'instance' => null,
        ];
    }
}

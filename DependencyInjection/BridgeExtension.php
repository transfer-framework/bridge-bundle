<?php

/*
 * This file is part of Transfer.
 *
 * For the full copyright and license information, please view the LICENSE file located
 * in the root directory.
 */

namespace Bridge\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class BridgeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $processor = new Processor();
        $configuration = new Configuration();
        $processedConfiguration = $processor->processConfiguration(
            $configuration,
            $configs
        );

        $this->generateServices($processedConfiguration['services'], $container);

        $this->addClassesToCompile(array(
            'Bridge\\Registry',
        ));
    }

    /**
     * Generates service definitions for services.
     *
     * @param array            $services  Services
     * @param ContainerBuilder $container Container builder
     */
    private function generateServices(array $services, ContainerBuilder $container)
    {
        foreach ($services as $serviceName => $service) {
            $serviceId = 'bridge.'.$serviceName;

            $serviceDefinition = $this->createComponentDefinition($serviceName, $service['class'], $service['options']);
            $serviceDefinition->addMethodCall('setEventDispatcher', array(new Reference('bridge.event_dispatcher')));
            $serviceDefinition->addMethodCall('addEventSubscriber', array(new Reference('bridge.event_subscriber.action_activity')));
            $serviceDefinition->addMethodCall('setRegistry', array(new Reference('bridge.registry')));

            $this->generateGroups($service['groups'], $serviceId, $serviceDefinition, $container);

            $container->setDefinition($serviceId, $serviceDefinition);

            $container
                ->getDefinition('bridge.registry')
                ->addMethodCall('addService', array(new Reference($serviceId)));
        }
    }

    /**
     * Generates services definitions for groups associated to a service.
     *
     * @param array            $groups    Groups
     * @param string           $parentId  Parent component ID
     * @param Definition       $parent    Parent service definition
     * @param ContainerBuilder $container Container builder
     */
    private function generateGroups(array $groups, $parentId, Definition $parent, ContainerBuilder $container)
    {
        foreach ($groups as $groupName => $group) {
            $groupId = $parentId.'.'.$groupName;

            $groupDefinition = $this->createComponentDefinition($groupName, $group['class'], $group['options']);
            $groupDefinition->addMethodCall('setService', array(new Reference($parentId)));

            $this->generateActions($group['actions'], $groupId, $groupDefinition, $container);

            $container->setDefinition($groupId, $groupDefinition);

            $parent->addMethodCall('addGroup', array(new Reference($groupId)));
        }
    }

    /**
     * Generates service definitions for actions associated to service groups.
     *
     * @param array            $actions   Actions
     * @param string           $parentId  Parent component ID
     * @param Definition       $parent    Parent definition
     * @param ContainerBuilder $container Container builder
     */
    private function generateActions(array $actions, $parentId, Definition $parent, ContainerBuilder $container)
    {
        foreach ($actions as $actionName => $action) {
            $actionId = $parentId.'.'.$actionName;

            $actionDefinition = $this->createComponentDefinition($actionName, $action['class'], $action['options']);
            $actionDefinition->addMethodCall('setGroup', array(new Reference($parentId)));

            $container->setDefinition($actionId, $actionDefinition);

            $parent->addMethodCall('addAction', array(new Reference($actionId)));
        }
    }

    /**
     * Creates a Bridge component service definition.
     *
     * @param string $name    Component name
     * @param string $class   Component class
     * @param array  $options Component options
     *
     * @return Definition
     */
    private function createComponentDefinition($name, $class, $options = array())
    {
        return new Definition($class, array($name, $options));
    }
}

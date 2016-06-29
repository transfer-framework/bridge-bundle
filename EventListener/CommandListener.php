<?php

/*
 * This file is part of Transfer.
 *
 * For the full copyright and license information, please view the LICENSE file located
 * in the root directory.
 */

namespace Bridge\Bundle\EventListener;

use Bridge\Registry;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

class CommandListener
{
    /**
     * @var Registry Bridge registry
     */
    protected $registry;

    /**
     * @param Registry $registry Bridge registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Returns an array of supported commands.
     *
     * @return string[] Support commands
     */
    public function getSupportedCommands()
    {
        return array(
            'bridge:list',
            'bridge:execute',
            'bridge:cache:pools',
            'bridge:cache:clear',
            'bridge:cache:remove',
        );
    }

    /**
     * Analyzes and modifies command.
     *
     * @param ConsoleCommandEvent $event Command event
     */
    public function command(ConsoleCommandEvent $event)
    {
        /** @var BridgeCommand $command */
        $command = $event->getCommand();

        if (!in_array($command->getName(), $this->getSupportedCommands())) {
            return;
        }

        $command->setRegistry($this->registry);
    }
}

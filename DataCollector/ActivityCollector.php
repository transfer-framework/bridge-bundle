<?php

/*
 * This file is part of Transfer.
 *
 * For the full copyright and license information, please view the LICENSE file located
 * in the root directory.
 */

namespace Bridge\Bundle\DataCollector;

use Bridge\EventSubscriber\ActionActivitySubscriber;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class ActivityCollector extends DataCollector
{
    /**
     * @var ActionActivitySubscriber
     */
    private $actionActivitySubscriber;

    /**
     * @param ActionActivitySubscriber $actionActivitySubscriber
     */
    public function __construct(ActionActivitySubscriber $actionActivitySubscriber)
    {
        $this->actionActivitySubscriber = $actionActivitySubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['call_time'] = $this->actionActivitySubscriber->getTotalExecutionTime();
        $this->data['call_count'] = $this->actionActivitySubscriber->getTotalCallCount();

        $this->data['calls'] = $this->actionActivitySubscriber->getActivity();
    }

    /**
     * Returns total call execution time.
     *
     * @return float
     */
    public function getCallTime()
    {
        return $this->data['call_time'];
    }

    /**
     * Returns total call count.
     *
     * @return int
     */
    public function getCallCount()
    {
        return $this->data['call_count'];
    }

    /**
     * Returns call metadata.
     *
     * @return array
     */
    public function getCalls()
    {
        return $this->data['calls'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'bridge.activity_collector';
    }
}

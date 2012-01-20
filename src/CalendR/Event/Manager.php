<?php

namespace CalendR\Event;

use CalendR\Event\EventInterface;
use CalendR\Period\PeriodInterface;

class Manager implements \IteratorAggregate
{
    /**
     * @var array|EventInterface
     */
    private $events = array();

    /**
     * @param array|EventInterface $event
     * @return Manager
     */
    public function add($events)
    {
        $events = (array)$events;
        foreach ($events as $event) {
            if (!$event instanceof EventInterface) {
                throw new \InvalidArgumentException('Events must implement \\CalendR\\Event\\EventInterface.');
            }
            $this->events[$event->getUid()] = $event;
        }

        return $this;
    }

    /**
     * @param string $uid event unique identifier
     * @return bool
     */
    public function has($uid)
    {
        return isset($this->events[$uid]);
    }

    /**
     * @param string $uid event unique identifier
     * @return EventInterface
     * @throws Exception\NotFound
     */
    public function get($uid)
    {
        if (!$this->has($uid)) {
            throw new Exception\NotFound;
        }

        return $this->events[$uid];
    }

    /**
     * @param string $uid event unique identifier
     * @return Manager
     * @throws Exception\NotFound
     */
    public function remove($uid)
    {
        if (!$this->has($uid)) {
            throw new Exception\NotFound;
        }

        unset($this->events[$uid]);

        return $this;
    }

    /**
     * @return array|EventInterface
     */
    public function all()
    {
        return $this->events;
    }

    /**
     * find events that matches the given period (during or over)
     *
     * @param \CalendR\Period\PeriodInterface $period
     * @return array|EventInterface
     */
    public function find(PeriodInterface $period)
    {
        $events = array();

        foreach ($this->all() as $event) {
            if ($event->containsPeriod($period) || $event->isDuring($period)) {
                $events[] = $event;
            }
        }

        return $events;
    }

    /**
     * \IteratorAggregate implementation
     * @return \ArrayIterator
     */
    public function getIterator()
    {
       return new \ArrayIterator($this->events);
    }
}
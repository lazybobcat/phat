<?php

namespace Phat\ORM;


use Phat\Event\EventListenerInterface;

class Behavior implements EventListenerInterface
{

    private $table;

    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * Returns the list of listened Events and the method that is used as callback
     * Example:
     *
     *  public function implementedEvents()
     *  {
     *      return [
     *          'Post.saved' => 'addTagsToPost',
     *          'Basket.Item.Added' => ['callable' => 'updateStocks', 'priority' => 20]
     *      ];
     *  }
     *
     * @return array
     */
    public function implementedEvents()
    {
        $eventMap = [
            'orm.table.initialize'  => 'initialize',
            'orm.table.beforeFind'  => 'beforeFind',
            'orm.table.afterFind'   => 'afterFind',
            'orm.table.beforeSave'  => 'beforeSave',
            'orm.table.afterSave'   => 'afterSave',
            'orm.table.beforeDelete'=> 'beforeDelete',
            'orm.table.afterDelete' => 'afterDelete'
        ];

        $events = [];
        $priority = isset($config['priority']) ? $config['priority'] : null;

        foreach($eventMap as $event => $method) {
            if(!method_exists($this, $method)) {
                continue;
            }

            if($priority) {
                $events[$event] = [
                    'callable' => $method,
                    'priority' => $priority
                ];
            } else {
                $events[$event] = $method;
            }
        }
    }
}
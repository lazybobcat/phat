<?php

namespace Phat\Event;


interface EventListenerInterface
{
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
    public function implementedEvents();
}
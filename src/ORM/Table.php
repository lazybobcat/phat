<?php

namespace Phat\ORM;


use Phat\Event\Event;
use Phat\Event\EventDispatcherInterface;
use Phat\Event\EventDispatcherTrait;
use Phat\Event\EventListenerInterface;
use Phat\Event\EventManager;
use Phat\Utils\Inflector;

class Table implements RepositoryInterface, EventListenerInterface, EventDispatcherInterface
{
    use EventDispatcherTrait;

    // TODO : validation


    protected $table;

    protected $alias;

    protected $connection;

    protected $primaryKey = 'id';

    protected $displayField = 'name';

    protected $associations;

    protected $behaviors = [];

    protected $entityClass;


    public function __construct(array $options = [])
    {
        if(!empty($options['table'])) {
            $this->table = $options['table'];
        }
        if(!empty($options['alias'])) {
            $this->alias = $options['alias'];
        }
        if(!empty($options['connection'])) {
            $this->connection = $options['connection'];
        }
        if(!empty($options['primaryKey'])) {
            $this->primaryKey = $options['primaryKey'];
        }
        if(!empty($options['displayField'])) {
            $this->displayField = $options['displayField'];
        }
        if(!empty($options['entityClass'])) {
            $this->entityClass = $options['entityClass'];
        }

        // TODO : attach behaviors
        // TODO : associations

        $this->initialize($options);
        $this->dispatchEvent('orm.table.initialize');
    }

    public function initialize(array $options = [])
    {
    }

    public function table()
    {
        if ($this->table === null) {
            $table = namespaceSplit(get_class($this));
            $table = substr(end($table), 0, -5);
            if (empty($table)) {
                $table = $this->alias();
            }
            $this->table = Inflector::underscore($table);
        }
        return $this->table;
    }

    public function alias()
    {
        if ($this->alias === null) {
            $alias = namespaceSplit(get_class($this));
            $alias = substr(end($alias), 0, -5);
            $this->alias = $alias;
        }
        return $this->alias;
    }

    public function entityClass()
    {
        if(null === $this->entityClass) {
            $default = 'Phat\ORM\Entity';
            $class = namespaceSplit(get_class($this));
            $class = substr(end($class), 0, -5);
            $class = 'App\Model\Entity\\'.$class;
            if(class_exists($class)) {
                $this->entityClass = $class;
            } else {
                $this->entityClass = $default;
            }
        }

        return $this->entityClass;
    }

    public function aliasField($field)
    {
        return $this->alias() . '.' . $field;
    }

    /**
     * Returns the Phat\Event\EventManager instance. You can use this instance to register callbacks.
     *
     * @return EventManager
     */
    public function eventManager()
    {
        return EventManager::instance();
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
        // TODO: Implement implementedEvents() method.
    }

    public function query()
    {
        // TODO: Implement query() method.
    }

    public function newEntity($data = null, array $options = [])
    {
        // TODO: Implement newEntity() method.
    }

    public function find($type = 'all', array $options = [])
    {
        // TODO: Implement find() method.
    }

    public function findById($id, array $options = [])
    {
        // TODO: Implement findById() method.
    }

    public function save(EntityInterface $entity, array $options = [])
    {
        // TODO: Implement save() method.
    }

    public function updateAll($fields, array $conditions)
    {
        // TODO: Implement updateAll() method.
    }

    public function delete(EntityInterface $entity, array $options = [])
    {
        // TODO: Implement delete() method.
    }

    public function deleteAll(array $conditions)
    {
        // TODO: Implement deleteAll() method.
    }
}
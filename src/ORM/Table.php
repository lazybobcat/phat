<?php

namespace Phat\ORM;


use Phat\Core\Configure;
use Phat\Event\EventDispatcherInterface;
use Phat\Event\EventDispatcherTrait;
use Phat\Event\EventListenerInterface;
use Phat\Event\EventManager;
use Phat\ORM\Database\Connection;
use Phat\ORM\Database\TableHandler;
use Phat\Utils\Inflector;
use Pixie\QueryBuilder;

class Table implements RepositoryInterface, EventListenerInterface, EventDispatcherInterface
{
    use EventDispatcherTrait;

    // TODO : validation

    public $database = 'default';

    public $table;

    protected $alias;

    protected $repositoryAlias;

    protected $connection;

    protected $handler;

    protected $primaryKey = 'id';

    protected $displayField = 'name';

    protected $associations;

    protected $behaviors = [];

    public $entityClass;


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
        if(!empty($options['database'])) {
            $this->database = $options['database'];
        }

        $this->connection = Connection::get($this->database);
        $this->handler = new TableHandler();

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

    public function repositoryAlias()
    {
        if ($this->repositoryAlias === null) {
            $alias = namespaceSplit(get_class($this));
            $alias = substr(end($alias), 0, -5);
            $alias = Inflector::singularize(strtolower($alias));
            $this->repositoryAlias = $alias;
        }
        return $this->repositoryAlias;
    }

    public function entityClass()
    {
        if(null === $this->entityClass) {
            $default = 'Phat\ORM\Entity';
            $class = namespaceSplit(get_class($this));
            $class = substr(end($class), 0, -5);
            $class = Inflector::singularize($class);
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
        if(false === strstr($field, '.')) {
            return strtolower($this->alias()) . '.' . $field;
        }
        return $field;
    }

    public function repositoryAliasField($field)
    {
        return $this->repositoryAlias() . '__' . $field;
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
        $eventMap = [
            'orm.table.initialize'  => 'initialize',
            'orm.table.beforeFind'  => 'beforeFind',
            'orm.table.afterFind'   => 'afterFind',
            'orm.table.beforeSave'  => 'beforeSave',
            'orm.table.afterSave'   => 'afterSave',
            'orm.table.beforeDelete'=> 'beforeDelete',
            'orm.table.afterDelete' => 'afterDelete',
            'orm.table.beforeValidation' => 'beforeValidation',
            'orm.table.afterValidation' => 'afterValidation'
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

    /**
     * @return QueryBuilder\QueryBuilderHandler
     */
    public function query()
    {
        $qb = Connection::getQueryBuilder($this->database);

        return $qb->table($this->table())->setFetchMode(\PDO::FETCH_ASSOC);
    }

    public function newEntity(array $data = [], array $options = [])
    {
        $class = $this->entityClass();

        return new $class($data);
    }

    public function find($type = 'all', array $options = [])
    {
        $fields = $this->handler->columnList($this, $this->database);

        $query = $this->query()
            ->leftJoin('users', 'posts.user_id', '=', 'users.id');

        foreach($fields as $field) {
            $query->select([$this->aliasField($field) => $this->repositoryAliasField($field)]);
        }

        $query->where('posts.active', '=', 1)
            ->orderBy('posts.created')
//            ->limit(1)->offset(0)
        ;
        debug($query->getQuery()->getSql());
        debug($query->getQuery()->getBindings());
        debug($query->getQuery()->getRawSql());

        $fetched = $query->get();
        $results = [];
        foreach($fetched as $result) {
            debug($result);
            $results[] = $this->hydrate($result);
        }
        return $results;
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

    private function hydrate(array $data = [])
    {
        $attributes = [];
        foreach($data as $sfield => $v) {
            list($alias, $field) = explode('__', $sfield);
            if($alias === $this->repositoryAlias()) {
                $attributes[$field] = $v;
            }
        }

        $class = $this->entityClass();
        return new $class($attributes);
    }
}
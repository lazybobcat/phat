<?php

namespace Phat\ORM;


use Phat\ORM\Database\Connection;

class Query
{
    /**
     * @var \Pixie\Connection
     */
    protected $connection;
    protected $database;
    protected $table;
    protected $queryBuilder;

    public function __construct(Table $table, $database = 'default')
    {
        $this->table = $table;
        $this->connection = Connection::get($database);
        $this->database = $database;
        $this->queryBuilder = Connection::getQueryBuilder($database)->table($table->table())->setFetchMode(\PDO::FETCH_ASSOC);
    }

    public function select($fields = [])
    {
        if($fields instanceof Table) {
            $fields = $fields->getFields();
        } elseif(empty($fields)) {
            $fields = $this->table->getFields();
        }

        foreach($fields as $field) {
            $this->queryBuilder->select([$this->table->aliasField($field) => $this->table->repositoryAliasField($field)]);
        }

        return $this;
    }

    // @TODO : join

    /**
     * $query->andWhere('name', '=', 'John');
     * $query->andWhere('name', 'John'); // implicit equal "="
     * $query->andWhere('number', '>', 42);
     *
     * You can pass a function as parameter to do a grouped where. IE:
     * $query->andWhere(function($q) {
     *      $q->where('active', false);
     *      $q->orWhere('publication', '>', '2016-06-06 00:00:00');
     * });
     * where $q is a \Pixie\QueryBuilder\QueryBuilderHandler , will generate :
     * "WHERE (`active`=false OR `publication`>'2016-06-06 00:00:00')"
     *
     * @param Callable|string $field
     * @param string|null $operator
     * @param string|null $value
     *
     * @return Query
     */
    public function andWhere($field, $operator = null, $value = null)
    {
        $field = $this->handleField($field);

        if(null === $value) {
            $this->queryBuilder->where($field, $operator);
        } else {
            $this->queryBuilder->where($field, $operator, $value);
        }

        return $this;
    }

    public function orWhere($field, $operator = null, $value = null)
    {
        $field = $this->handleField($field);

        if(null === $value) {
            $this->queryBuilder->orWhere($field, $operator);
        } else {
            $this->queryBuilder->orWhere($field, $operator, $value);
        }

        return $this;
    }

    public function where($field, $operator = null, $value = null)
    {
        return $this->andWhere($field, $operator, $value);
    }

    public function andWhereIn($field, array $values)
    {
        $field = $this->handleField($field);
        $this->queryBuilder->whereIn($field, $values);

        return $this;
    }

    public function orWhereIn($field, array $values)
    {
        $field = $this->handleField($field);
        $this->queryBuilder->orWhereIn($field, $values);

        return $this;
    }

    public function andWhereNotIn($field, array $values)
    {
        $field = $this->handleField($field);
        $this->queryBuilder->whereNotIn($field, $values);

        return $this;
    }

    public function orWhereNotIn($field, array $values)
    {
        $field = $this->handleField($field);
        $this->queryBuilder->orWhereNotIn($field, $values);

        return $this;
    }

    public function andWhereBetween($field, $from, $to)
    {
        $field = $this->handleField($field);
        $this->queryBuilder->whereBetween($field, $from, $to);

        return $this;
    }

    public function orWhereBetween($field, $from, $to)
    {
        $field = $this->handleField($field);
        $this->queryBuilder->orWhereBetween($field, $from, $to);

        return $this;
    }

    /**
     * @param string|array $field
     * @return Query
     */
    public function groupBy($field)
    {
        $field = $this->handleField($field);
        $this->queryBuilder->groupBy($field);

        return $this;
    }

    public function orderBy($field, $direction = 'ASC')
    {
        $field = $this->handleField($field);
        $this->queryBuilder->orderBy($field, $direction);

        return $this;
    }

    public function limit($limit)
    {
        $this->queryBuilder->limit($limit);

        return $this;
    }

    public function offset($offset)
    {
        $this->queryBuilder->offset($offset);

        return $this;
    }

    public function having($field, $operator, $value, $joiner = 'AND')
    {
        $field = $this->handleField($field);
        $this->queryBuilder->having($field, $operator, $value, $joiner);

        return $this;
    }

    public function andHaving($field, $operator, $value)
    {
        return $this->having($field, $operator, $value, 'AND');
    }

    public function orHaving($field, $operator, $value)
    {
        return $this->having($field, $operator, $value, 'OR');
    }

    /*********************************************************************************/

    public function getResult()
    {
        $fetched = $this->queryBuilder->get();
        $results = [];
        foreach($fetched as $result) {
            // @TODO : for associations, hydrate with the right table
            $results[] = $this->table->hydrate($result);
        }

        return $results;
    }

    public function getOneOrNullResult()
    {
        $result = $this->queryBuilder->first();

        return (null === $result ? null : $this->table->hydrate($result));
    }

    public function getCount()
    {
        $this->queryBuilder->setFetchMode(\PDO::FETCH_CLASS);
        $count = $this->queryBuilder->count();
        $this->queryBuilder->setFetchMode(\PDO::FETCH_ASSOC);

        return $count;
    }

    public function insert(array $data)
    {
        $this->queryBuilder->insert($data);
    }

    public function update(array $data)
    {
        $this->queryBuilder->update($data);
    }

    public function delete()
    {
        $this->queryBuilder->delete();
    }

    /*********************************************************************************/

    public function getQuery()
    {
        return $this->queryBuilder->getQuery()->getSql();
    }

    public function getBindings()
    {
        return $this->queryBuilder->getQuery()->getBindings();
    }

    public function getRawQuery()
    {
        return $this->queryBuilder->getQuery()->getRawSql();
    }

    /**
     * @param $field
     * @return string
     */
    private function handleField($field)
    {
        if (is_string($field)) {
            $field = $this->table->aliasField($field);
        }

        return $field;
    }
}
<?php

namespace pavle\yii2\rest;

use yii\base\Component;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveQueryTrait;
use yii\db\ActiveRelationTrait;
use yii\db\QueryTrait;

/**
 * This is just an example.
 */
class ActiveQuery extends Component implements ActiveQueryInterface
{
    use QueryTrait;
    use ActiveQueryTrait;
    use ActiveRelationTrait;

    /**
     * @var \pavle\yii2\rest\Connection
     */
    protected $connect;

    /**
     * Constructor.
     * @param string $modelClass the model class associated with this query
     * @param array $config configurations to be applied to the newly created query object
     */
    public function __construct($modelClass, $config = [])
    {
        parent::__construct($config);
        $this->modelClass = $modelClass;
        $this->connect = new Connection($modelClass);
    }

    /**
     * Executes the query and returns all results as an array.
     * @param Connection $db the database connection used to execute the query.
     * If this parameter is not given, the `db` application component will be used.
     * @return array|ActiveRecord[] the query results. If the query results in nothing, an empty array will be returned.
     */
    public function all($db = null)
    {
        $rows = $this->connect->lists($this);
        return $this->populate($rows);
    }

    /**
     * Executes the query and returns a single row of result.
     * @param Connection $db the database connection used to execute the query.
     * If this parameter is not given, the `db` application component will be used.
     * @return ActiveRecord|array|null a single row of query result. Depending on the setting of [[asArray]],
     * the query result may be either an array or an ActiveRecord object. Null will be returned
     * if the query results in nothing.
     */
    public function one($db = null)
    {
        $this->limit = 1;
        $rows = $this->connect->lists($this);
        return reset($this->populate($rows));
    }

    /**
     * Returns the number of records.
     * @param string $q the COUNT expression. Defaults to '*'.
     * @param Connection $db the database connection used to execute the query.
     * If this parameter is not given, the `db` application component will be used.
     * @return integer number of records.
     */
    public function count($q = '*', $db = null)
    {
        return $this->connect->count($this);
    }

    /**
     * Returns a value indicating whether the query result contains any row of data.
     * @param Connection $db the database connection used to execute the query.
     * If this parameter is not given, the `db` application component will be used.
     * @return boolean whether the query result contains any row of data.
     */
    public function exists($db = null)
    {
        return $this->connect->exists($this);
    }

    /**
     * @inheritdoc
     */
    public function populate($rows)
    {
        if (empty($rows)) {
            return [];
        }

        $models = $this->createModels($rows);
        if (!empty($this->with)) {
            $this->findWith($this->with, $models);
        }
        if (!$this->asArray) {
            foreach ($models as $model) {
                $model->afterFind();
            }
        }

        return $models;
    }
}

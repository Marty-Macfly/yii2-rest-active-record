<?php
/**
 * User: 李鹏飞 <523260513@qq.com>
 * Date: 2016/3/2
 * Time: 19:01
 */

namespace pavle\yii2\rest;


use Curl\Curl;
use yii\base\Component;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;

class Connection extends Component
{
    /**
     * @var array
     */
    public $map;

    /**
     * @var string
     */
    public $modelClass;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var callable
     */
    public $success;

    /**
     * @var callable
     */
    public $error;

    public function init()
    {
        parent::init();
        $this->curl = new Curl();
        !$this->success && $this->success = function (Curl $curl) {
            $curl->response = ArrayHelper::toArray($curl->response);
        };
        !$this->error && $this->error = function (Curl $curl) {
            \Yii::trace(serialize($curl->response), 'rest.connect');
            throw new ErrorException($curl->errorMessage);
        };
        $this->curl->success($this->success);
        $this->curl->error($this->error);
    }


    /**
     * @param ActiveQuery $query
     * @return mixed
     */
    public function lists(ActiveQuery $query)
    {
        return $this->curl->get($this->getUrl($this->modelClass, 'lists'), ['where' => $query->where, 'limit' => $query->limit, 'offset' => $query->offset]);
    }

    /**
     * Returns the number of records.
     * If this parameter is not given, the `db` application component will be used.
     * @param ActiveQuery $query
     * @return int number of records.
     */
    public function count(ActiveQuery $query)
    {
        return $this->curl->get($this->getUrl($this->modelClass, 'count'), ['where' => $query->where, 'limit' => $query->limit, 'offset' => $query->offset]);
    }

    /**
     * Returns a value indicating whether the query result contains any row of data.
     * If this parameter is not given, the `db` application component will be used.
     * @param ActiveQuery $query
     * @return bool whether the query result contains any row of data.
     */
    public function exists(ActiveQuery $query)
    {
        $result = $this->curl->get($this->getUrl($this->modelClass, 'count'), ['where' => $query->where]);
        return $result ? true : false;
    }

    /**
     * @param $attributes
     * @param $condition
     * @return integer
     */
    public function updateAll($attributes, $condition)
    {
        return $this->curl->put($this->getUrl($this->modelClass, 'update') . '?' . http_build_query($condition), $attributes);
    }

    public function deleteAll($condition, $params)
    {
        return $this->curl->delete($this->getUrl($this->modelClass, 'delete'), $condition);
    }

    /**
     * @param $attributes
     * @return mixed
     */
    public function create($attributes)
    {
        return $this->curl->post($this->getUrl($this->modelClass, 'create'), $attributes);
    }

    /**
     * @param $modelClass
     * @param $operate
     * @return mixed
     */
    protected function getUrl($modelClass, $operate)
    {
        return ArrayHelper::getValue($this->map, $modelClass . '.' . $operate);
    }

    /**
     * @param $counters
     * @param $condition
     * @return integer
     */
    public function updateAllCounters($counters, $condition)
    {
        return $this->curl->put($this->getUrl($this->modelClass, 'update') . '?' . http_build_query($condition), $counters);
    }
}
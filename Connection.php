<?php
/**
 * User: 李鹏飞 <523260513@qq.com>
 * Date: 2016/3/2
 * Time: 19:01
 */

namespace pavle\yii2\rest;


use Curl\Curl;
use yii\base\Component;
use yii\base\Event;
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

    const EVENT_CURL_SUCCESS = 'curlSuccess';
    const EVENT_CURL_ERROR = 'curlError';

    public function init()
    {
        parent::init();
        $this->curl = new Curl();
        $this->curl->success(function(Curl $curl){
            $event = new CurlEvent(['curl' => $curl]);
            Connection::trigger('curlSuccess', $event);
        });
        $this->curl->error(function(Curl $curl){
            $event = new CurlEvent(['curl' => $curl]);
            Connection::trigger('curlError', $event);
        });
    }


    /**
     * @param ActiveQuery $query
     * @return mixed
     */
    public function lists(ActiveQuery $query)
    {
        return $this->curl->get($this->getUrl($query->modelClass, 'lists'), ['where' => $query->where, 'limit' => $query->limit, 'offset' => $query->offset, 'orderBy' => $query->orderBy]);
    }

    /**
     * Returns the number of records.
     * If this parameter is not given, the `db` application component will be used.
     * @param ActiveQuery $query
     * @return int number of records.
     */
    public function count(ActiveQuery $query)
    {
        return $this->curl->get($this->getUrl($query->modelClass, 'count'), ['where' => $query->where, 'limit' => $query->limit, 'offset' => $query->offset, 'orderBy' => $query->orderBy]);
    }

    /**
     * Returns a value indicating whether the query result contains any row of data.
     * If this parameter is not given, the `db` application component will be used.
     * @param ActiveQuery $query
     * @return bool whether the query result contains any row of data.
     */
    public function exists(ActiveQuery $query)
    {
        $result = $this->curl->get($this->getUrl($query->modelClass, 'count'), ['where' => $query->where]);
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
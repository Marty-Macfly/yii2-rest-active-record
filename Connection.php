<?php
/**
 * User: 李鹏飞 <523260513@qq.com>
 * Date: 2016/3/2
 * Time: 19:01
 */

namespace pavle\yii2\rest;


use Curl\Curl;
use yii\base\Component;
use yii\helpers\ArrayHelper;

class Connection extends Component
{
    public $map = [
        'pavle\yii2\rest\test\Fans' => [
            'lists' => 'http://baseapi.chexiu-local.cn/fans/lists',
            'create' => 'http://baseapi.chexiu-local.cn/fans/create',
            'update' => 'http://baseapi.chexiu-local.cn/fans/update',
            'count' => 'http://baseapi.chexiu-local.cn/fans/count',
        ],
        'pavle\yii2\rest\test\Store' => [
            'lists' => 'http://baseapi.chexiu-local.cn/store/lists',
            'create' => 'http://baseapi.chexiu-local.cn/store/create',
            'update' => 'http://baseapi.chexiu-local.cn/store/update',
            'count' => 'http://baseapi.chexiu-local.cn/store/count',
        ],
        'pavle\yii2\rest\test\Pay' => [
            'lists' => 'http://baseapi.chexiu-local.cn/pay/lists',
            'create' => 'http://baseapi.chexiu-local.cn/pay/create',
            'update' => 'http://baseapi.chexiu-local.cn/pay/update',
            'count' => 'http://baseapi.chexiu-local.cn/pay/count',
        ],
    ];

    public $modelClass;

    /**
     * @var Curl
     */
    protected $curl;

    public function __construct($modelClass, $config = [])
    {
        parent::__construct($config);
        $this->modelClass = $modelClass;
        $this->curl = new Curl();
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
        return true;
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

    /**
     * @param $condition
     * @param $params
     * @return bool
     */
    public function deleteAll($condition, $params)
    {
        return true;
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
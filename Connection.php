<?php

namespace pavle\yii2\rest;

use Yii;
use yii\base\InvalidArgumentException;
use yii\httpclient\Client;
use yii\helpers\ArrayHelper;

class Connection extends Client
{
    /**
     * @var array
     */
    public $map = [];

    /**
     * @var string
     */
    public $modelClass;

    /**
     * @event ResponseEvent an event raised right after getting succeful response.
     */
    const EVENT_RESPONSE_SUCCESS = 'responseSuccess';
    /**
     * @event ResponseEvent an event raised right after getting error response.
     */
    const EVENT_RESPONSE_ERROR = 'responseError';

    /**
     * @inheritdoc
     */
    public function afterSend($request, $response)
    {
        parent::afterSend($request, $response);

        $event = new ResponseEvent();
        $event->response = $response;

        if ($response->isOk) {
            $this->trigger(self::EVENT_RESPONSE_SUCCESS, $event);
        } else {
            $this->trigger(self::EVENT_RESPONSE_ERROR, $event);
        }
    }

    /**
     * @param ActiveQuery $query
     * @return mixed
     */
    public function _lists(ActiveQuery $query)
    {
        return $this->modelClass::getDb()->get([$this->getUrl($query->modelClass, 'lists'), 'where' => $query->where, 'limit' => $query->limit, 'offset' => $query->offset, 'orderBy' => $query->orderBy])->send()->data;
    }

    /**
     * Returns the number of records.
     * If this parameter is not given, the `db` application component will be used.
     * @param ActiveQuery $query
     * @return int number of records.
     */
    public function _count(ActiveQuery $query)
    {
        return $this->modelClass::getDb()->get([$this->getUrl($query->modelClass, 'count'), 'where' => $query->where, 'limit' => $query->limit, 'offset' => $query->offset, 'orderBy' => $query->orderBy])->send()->data;
    }

    /**
     * Returns a value indicating whether the query result contains any row of data.
     * If this parameter is not given, the `db` application component will be used.
     * @param ActiveQuery $query
     * @return bool whether the query result contains any row of data.
     */
    public function _exists(ActiveQuery $query)
    {
        $result = $this->modelClass::getDb()->get([$this->getUrl($query->modelClass, 'count'), 'where' => $query->where])->send();
        return $result->isOk;
    }

    /**
     * @param $id
     * @param $attributes
     * @return int
     */
    public function _update($id, $attributes)
    {
        return $this->modelClass::getDb()->put([$this->getUrl($this->modelClass, 'update'), 'id' => $id], $attributes)->send()->data;
    }

    /**
     * @param $id
     * @return string
     */
    public function _delete($id)
    {
        return $this->modelClass::getDb()->delete([$this->getUrl($this->modelClass, 'delete'), 'id' => $id])->send()->data;
    }

    /**
     * @param $attributes
     * @return mixed
     */
    public function _create($attributes)
    {
        return $this->modelClass::getDb()->post($this->getUrl($this->modelClass, 'create'), $attributes)->send()->data;
    }

    /**
     * @param $modelClass
     * @param $operate
     * @return mixed
     */
    protected function getUrl($modelClass, $operate)
    {
        return ArrayHelper::getValue(ArrayHelper::merge($modelClass::$map, ArrayHelper::getValue($this->map, $modelClass, [])), $operate);
    }

    /**
     * @param $counters
     * @param $condition
     * @return integer
     */
    public function updateAllCounters($counters, $condition)
    {
        return $this->modelClass::getDb()->put([$this->getUrl($this->modelClass, 'update'), $condition], $counters);
    }
}

<?php

namespace pavle\yii2\rest;

use Yii;
use yii\httpclient\Client;

class Connection extends Client
{
    /**
     * @var array
     */
    public $map = [];

    /**
     * @var string
     */

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
     * @param $counters
     * @param $condition
     * @return integer
     */
     /*
    public function updateAllCounters($counters, $condition)
    {
        return $this->modelClass::getDb()->put([$this->getUrl($this->modelClass, 'update'), $condition], $counters);
    }
    */
}

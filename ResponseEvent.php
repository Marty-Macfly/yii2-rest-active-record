<?php

namespace pavle\yii2\rest;

use yii\base\Event;

/**
 * ResponseEvent represents the event parameter used for a response events.
 *
 * @author Charles Delfly <mmacfly@gmail.com>
 */
class ResponseEvent extends Event
{
    /**
     * @var Response|null related HTTP response.
     * This field will be filled with response received.
     */
    public $response;
}

<?php
/**
 * User: 李鹏飞 <523260513@qq.com>
 * Date: 2016/3/3
 * Time: 13:17
 */

namespace pavle\yii2\rest;


use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\rest\Action;

class CountAction extends Action
{
    public function run()
    {
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        /* @var $query ActiveQuery */
        $query = \Yii::configure($modelClass::find(), \Yii::$app->request->queryParams);

        return $query->count();
    }
}
<?php
/**
 * User: 李鹏飞 <523260513@qq.com>
 * Date: 2016/3/3
 * Time: 13:16
 */

namespace pavle\yii2\rest;


use yii\rest\Action;

class UpdateAllAction extends Action
{
    public function run()
    {
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        /* @var $query ActiveQuery */
        return $modelClass::updateAll(\Yii::$app->request->post(), \Yii::$app->request->queryParams);
    }
}
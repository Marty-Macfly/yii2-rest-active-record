<?php
/**
 * User: 李鹏飞 <523260513@qq.com>
 * Date: 2016/3/3
 * Time: 13:15
 */

namespace pavle\yii2\rest;

use yii\rest\Action;

class SearchAction extends Action
{
    use RequestQueryTrait;

    public function run()
    {
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        /* @var $query ActiveQuery */
        $query = \Yii::configure($modelClass::find(), \Yii::$app->request->queryParams);
        $query->orderBy = $this->formatOrder($query->orderBy);

        return $query->all();
    }
}

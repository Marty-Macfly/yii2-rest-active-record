<?php
/**
 * User: 李鹏飞 <523260513@qq.com>
 * Date: 2016/3/3
 * Time: 16:25
 */

namespace pavle\yii2\rest\test;


use yii\data\ActiveDataProvider;

class FansSearch extends Fans
{
    public function search()
    {
        $query = Fans::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'fans_id' => $this->fans_id,
            'store_id' => $this->store_id,
        ]);

        return $dataProvider;
    }
}
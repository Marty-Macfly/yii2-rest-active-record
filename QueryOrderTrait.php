<?php
/**
 * User: 李鹏飞 <523260513@qq.com>
 * Date: 2016/3/7
 * Time: 18:38
 */

namespace pavle\yii2\rest;


trait QueryOrderTrait
{
    /**
     * 格式化orders数组，来使用查询
     * @param array $orders
     * @return array
     */
    public function formatOrder(array $orders)
    {
        $data = [];
        foreach ($orders as $key => $value) {
            $data[$key] = (int)$value;
        }

        return $data;
    }
}
<?php
/**
 * User: 李鹏飞 <523260513@qq.com>
 * Date: 2016/3/2
 * Time: 19:18
 */

namespace pavle\yii2\rest\test;


use pavle\yii2\rest\ActiveRecord;

class Store extends ActiveRecord
{
    public function attributes()
    {
        return [
            "store_id",
            "store_name",
            "company_id",
            "country",
            "province",
            "city",
            "area",
            "adr_info",
            "logo_path",
            "wechat_name",
        ];
    }

    public function rules()
    {
        return [
            [$this->attributes(), 'safe']
        ];
    }

    public function getPay(){
        return $this->hasOne(Pay::className(), ['store_id' => 'store_id']);
    }
}
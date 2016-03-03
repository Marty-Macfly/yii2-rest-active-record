<?php
/**
 * User: 李鹏飞 <523260513@qq.com>
 * Date: 2016/3/3
 * Time: 11:31
 */

namespace pavle\yii2\rest\test;


use pavle\yii2\rest\ActiveRecord;

class Pay extends ActiveRecord
{
    public function attributes()
    {
        return [
            "pay_config_id",
            "store_id",
            "app_id",
            "app_secret",
            "pay_sign_key",
            "partner_id",
            "partner_key",
            "mch_id",
            "created_at",
            "updated_at",
        ];
    }

    public function rules()
    {
        return [
            [$this->attributes(), 'safe']
        ];
    }
}
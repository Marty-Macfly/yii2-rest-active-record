<?php
/**
 * User: 李鹏飞 <523260513@qq.com>
 * Date: 2016/3/2
 * Time: 19:17
 */

namespace pavle\yii2\rest\test;


use pavle\yii2\rest\ActiveRecord;

class Fans extends ActiveRecord
{
    public function attributes()
    {
        return [
            "fans_id",
            "store_id",
            "open_id",
            "member_name",
            "wx_name",
            "gender",
            "language",
            "city",
            "province",
            "country",
            "is_focus",
            "focus_at",
            "refer_channel",
            "unfocus_at",
            "created_at",
            "updated_at",
            "avatar",
            "deleted_at",
        ];
    }

    public function rules()
    {
        return [
            [$this->attributes(), 'safe']
        ];
    }

    public static function primaryKey()
    {
        return ['fans_id'];
    }

    /**
     * @return \pavle\yii2\rest\ActiveQuery
     */
    public function getStore(){
        return $this->hasOne(Store::className(), ['store_id' => 'store_id']);
    }
}
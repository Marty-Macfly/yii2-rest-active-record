Rest Active Record
==================
一个基于restful api模型资源的ActiveRecord方案

安装
------------

安装此扩展的首选方式是通过 [composer](http://getcomposer.org/download/).

任一运行

```
php composer.phar require --prefer-dist pavle/yii2-rest-active "*"
```

或者添加

```
"pavle/yii2-rest-active": "*"
```

到您的 `composer.json` 文件.


用法
-----

1、继承pavle\yii2\rest\ActiveRecord，添加attributes

```php
class Fans extends pavle\yii2\rest\ActiveRecord
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
```

2、配置rest接口

```php
public function actions(){
    return ArrayHelper::merge(parent::actions(), [
        'lists' => [
            'class' => 'pavle\yii2\rest\SearchAction'
        ],
        'update-all' => [
            'class' => 'pavle\yii2\rest\UpdateAllAction'
        ],
        'update-all-count' => [
            'class' => 'pavle\yii2\rest\CountAction'
        ]
    ]);
}
```

3、reset api配置完，在原项目配置config/web.php

```php
    ...
    'components' => [
        'rest' => [
            'class' => 'pavle\yii2\rest\Connection',
            'map' => [
                'pavle\yii2\rest\test\Fans' => [
                    'lists' => 'http://baseapi.chexiu-local.cn/fans/lists',
                    'create' => 'http://baseapi.chexiu-local.cn/fans/create',
                    'update' => 'http://baseapi.chexiu-local.cn/fans/update',
                    'count' => 'http://baseapi.chexiu-local.cn/fans/count',
                ],
                'pavle\yii2\rest\test\Store' => [
                    'lists' => 'http://baseapi.chexiu-local.cn/store/lists',
                    'create' => 'http://baseapi.chexiu-local.cn/store/create',
                    'update' => 'http://baseapi.chexiu-local.cn/store/update',
                    'count' => 'http://baseapi.chexiu-local.cn/store/count',
                ],
                'pavle\yii2\rest\test\Pay' => [
                    'lists' => 'http://baseapi.chexiu-local.cn/pay/lists',
                    'create' => 'http://baseapi.chexiu-local.cn/pay/create',
                    'update' => 'http://baseapi.chexiu-local.cn/pay/update',
                    'count' => 'http://baseapi.chexiu-local.cn/pay/count',
                ],
            ],
        ]
    ],
    ...
```

4、然后就可以像db的ActiveRecord一样调用了

```php
$model = Fans::find()->with('store.pay')->one();
$model->wx_name = 'test';
$model->save();
```

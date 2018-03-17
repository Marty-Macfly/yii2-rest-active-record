Rest Active Record
==================

[Active Record](http://www.yiiframework.com/doc-2.0/guide-db-active-record.html) interface for accessing and manipulating data stored on remote ressource throug a RESTful API.

INSTALLATION
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist pavle/yii2-rest-active-record "*"
```

or add

```
"pavle/yii2-rest-active": "*"
```

to the require section of your `composer.json` file.

COMPATIBILITY CHANGE SINCE VERSION 1.0.5
------------

* Switch from [php-curl-class/php-curl-class](https://github.com/php-curl-class/php-curl-class) to [yiisoft/yii2-httpclient](https://github.com/yiisoft/yii2-httpclient/)
  * Class CurlEvent replace by ResponseEvent
    * EVENT_CURL_SUCCESS by EVENT_RESPONSE_SUCCESS
    * EVENT_CURL_ERROR by EVENT_RESPONSE_ERROR

Yii framework (server) providing the Rest API side
-----

Install the module `pavle/yii2-rest-active` and be sure your controller define the following actions:

```php
public function actions(){
    return ArrayHelper::merge(parent::actions(), [
        'lists' => [
            'class' => 'pavle\yii2\rest\SearchAction' // Action to add
        ],
        'create' => [
            'class' => 'yii2\rest\CreateAction'
        ],
        'update' => [
            'class' => 'yii2\rest\UpdateAction'
        ],
        'delete' => [
            'class' => 'yii2\rest\DeleteAction'
        ],
        'count' => [
            'class' => 'pavle\yii2\rest\CountAction' // Action to add
        ]
    ]);
}
```

Yii framework (client) consuming the REST API and using the Rest ActiveRecord Model
-----

Install the module `pavle/yii2-rest-active`

1. Create your ActiveRecord Model, `pavle\yii2\rest\ActiveRecord::attributes`

```php
/**
 *
 * @property $fans_id
 * @property $store_id
 * @property $open_id
 * @property $member_name
 * @property $wx_name
 * @property $gender
 * @property $language
 */
class Fans extends ActiveRecord
{
    /**
     * ActiveRecord attributes list
     */
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

    /**
     * Define the ActiveRecord primary key
     */
    public static function primaryKey()
    {
        return ['fans_id'];
    }

    /**
     * @return \pavle\yii2\rest\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Store::className(), ['store_id' => 'store_id']);
    }
}
```

2、Define API endppont for the compoenent in `config/web.php`.

```php
    ...
    'components' => [
        'rest' => [
            'class' => 'pavle\yii2\rest\Connection',
            'map' => [
                'pavle\yii2\rest\test\Fans' => [
                    'lists' => 'http://baseapi.xxxxxx.cn/fans/lists',
                    'create' => 'http://baseapi.xxxxxx.cn/fans/create',
                    'update' => 'http://baseapi.xxxxxx.cn/fans/update',
                    'count' => 'http://baseapi.xxxxxx.cn/fans/count',
                    'delete' => 'http://baseapi.xxxxxx.cn/fans/delete',
                ],
                'pavle\yii2\rest\test\Store' => [
                    'lists' => 'http://baseapi.xxxxxx.cn/store/lists',
                    'create' => 'http://baseapi.xxxxxx.cn/store/create',
                    'update' => 'http://baseapi.xxxxxx.cn/store/update',
                    'count' => 'http://baseapi.xxxxxx.cn/store/count',
                    'delete' => 'http://baseapi.xxxxxx.cn/fans/delete',
                ],
                'pavle\yii2\rest\test\Pay' => [
                    'lists' => 'http://baseapi.xxxxxx.cn/pay/lists',
                    'create' => 'http://baseapi.xxxxxx.cn/pay/create',
                    'update' => 'http://baseapi.xxxxxx.cn/pay/update',
                    'count' => 'http://baseapi.xxxxxx.cn/pay/count',
                    'delete' => 'http://baseapi.xxxxxx.cn/fans/delete',
                ],
            ],
        ]
    ],
    ...
```

3. Use your ActiveRecord to do what you want `db\ActiveRecord'.

```php
$model = Fans::find()->with('store.pay')->one();
$model->wx_name = 'test';
$model->save();
```

You can use with ActiveForm、ListView、GridView, ..

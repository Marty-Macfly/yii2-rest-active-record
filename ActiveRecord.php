<?php
/**
 * User: 李鹏飞 <523260513@qq.com>
 * Date: 2016/3/2
 * Time: 18:52
 */

namespace pavle\yii2\rest;

use Yii;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\db\BaseActiveRecord;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

/**
 * Class ActiveRecord
 * @package pavle\yii2\rest
 *
 * @method ActiveQuery hasOne($class, $link)
 * @method ActiveQuery hasMany($class, $link)
 */
class ActiveRecord extends BaseActiveRecord
{
    /**
     * @var array
     */
    public static $map = [];

    /**
     * Returns the primary key **name(s)** for this AR class.
     *
     * Note that an array should be returned even when the record only has a single primary key.
     *
     * For the primary key **value** see [[getPrimaryKey()]] instead.
     *
     * @return string[] the primary key name(s) for this AR class.
     */
    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * @param ActiveQuery $query
     * @return mixed
     */
    public static function _lists(ActiveQuery $query)
    {
        return static::getDb()->get([static::getUrl($query->modelClass, 'lists'), 'where' => $query->where, 'limit' => $query->limit, 'offset' => $query->offset, 'orderBy' => $query->orderBy])->send()->data;
    }

    /**
     * Returns the number of records.
     * If this parameter is not given, the `db` application component will be used.
     * @param ActiveQuery $query
     * @return int number of records.
     */
    public static function _count(ActiveQuery $query)
    {
        return static::getDb()->get([static::getUrl($query->modelClass, 'count'), 'where' => $query->where, 'limit' => $query->limit, 'offset' => $query->offset, 'orderBy' => $query->orderBy])->send()->data;
    }

    /**
     * Returns a value indicating whether the query result contains any row of data.
     * If this parameter is not given, the `db` application component will be used.
     * @param ActiveQuery $query
     * @return bool whether the query result contains any row of data.
     */
    public static function _exists(ActiveQuery $query)
    {
        $result = static::getDb()->get([static::getUrl($query->modelClass, 'count'), 'where' => $query->where])->send();
        return $result->isOk;
    }

    /**
     * @param $id
     * @param $attributes
     * @return int
     */
    public static function _update($id, $attributes)
    {
        return static::getDb()->put([static::getUrl(get_called_class(), 'update'), 'id' => $id], $attributes)->send()->data;
    }

    /**
     * @param $id
     * @return string
     */
    public static function _delete($id)
    {
        return static::getDb()->delete([static::getUrl(get_called_class(), 'delete'), 'id' => $id])->send()->data;
    }

    /**
     * @param $attributes
     * @return mixed
     */
    public static function _create($attributes)
    {
        return static::getDb()->post(static::getUrl(get_called_class(), 'create'), $attributes)->send()->data;
    }

    /**
     * @param $modelClass
     * @param $operate
     * @return mixed
     */
    protected static function getUrl($modelClass, $operate)
    {
        return ArrayHelper::getValue(ArrayHelper::merge($modelClass::$map, ArrayHelper::getValue(static::getDb()->map, $modelClass, [])), $operate);
    }

    /**
     * Creates an [[ActiveQueryInterface]] instance for query purpose.
     *
     * The returned [[ActiveQueryInterface]] instance can be further customized by calling
     * methods defined in [[ActiveQueryInterface]] before `one()` or `all()` is called to return
     * populated ActiveRecord instances. For example,
     *
     * ```php
     * // find the customer whose ID is 1
     * $customer = Customer::find()->where(['id' => 1])->one();
     *
     * // find all active customers and order them by their age:
     * $customers = Customer::find()
     *     ->where(['status' => 1])
     *     ->orderBy('age')
     *     ->all();
     * ```
     *
     * This method is also called by [[BaseActiveRecord::hasOne()]] and [[BaseActiveRecord::hasMany()]] to
     * create a relational query.
     *
     * You may override this method to return a customized query. For example,
     *
     * ```php
     * class Customer extends ActiveRecord
     * {
     *     public static function find()
     *     {
     *         // use CustomerQuery instead of the default ActiveQuery
     *         return new CustomerQuery(get_called_class());
     *     }
     * }
     * ```
     *
     * The following code shows how to apply a default condition for all queries:
     *
     * ```php
     * class Customer extends ActiveRecord
     * {
     *     public static function find()
     *     {
     *         return parent::find()->where(['deleted' => false]);
     *     }
     * }
     *
     * // Use andWhere()/orWhere() to apply the default condition
     * // SELECT FROM customer WHERE `deleted`=:deleted AND age>30
     * $customers = Customer::find()->andWhere('age>30')->all();
     *
     * // Use where() to ignore the default condition
     * // SELECT FROM customer WHERE age>30
     * $customers = Customer::find()->where('age>30')->all();
     *
     * @return ActiveQuery the newly created [[ActiveQueryInterface]] instance.
     */
    public static function find()
    {
        return Yii::createObject(ActiveQuery::className(), [get_called_class()]);
    }

    /**
     * Inserts the record into the database using the attribute values of this record.
     *
     * Usage example:
     *
     * ```php
     * $customer = new Customer;
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->insert();
     * ```
     *
     * @param boolean $runValidation whether to perform validation (calling [[validate()]])
     * before saving the record. Defaults to `true`. If the validation fails, the record
     * will not be saved to the database and this method will return `false`.
     * @param array $attributes list of attributes that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     * @return boolean whether the attributes are valid and the record is inserted successfully.
     */
    public function insert($runValidation = true, $attributes = null)
    {
        if ($runValidation && !$this->validate($attributes)) {
            Yii::info('Model not inserted due to validation error.', __METHOD__);
            return false;
        }

        if (!$this->beforeSave(true)) {
            return false;
        }

        $values = $this->getDirtyAttributes($attributes);
        if (($data = static::_create($values)) === false) {
            return false;
        }

        foreach ($data as $name => $value) {
            $this->setAttribute($name, $value);
            $values[$name] = $value;
        }

        $changedAttributes = array_fill_keys(array_keys($values), null);
        $this->setOldAttributes($values);
        $this->afterSave(true, $changedAttributes);

        return true;
    }

    /**
     * Returns the connection used by this AR class.
     * @return Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('rest');
    }

    public static function getmodelClass()
    {
        if (static::$modelClass == null) {
            static::$modelClass = get_called_class();
        }
        return static::$modelClass;
    }

    /**
     * @param array $attributes
     * @param string $condition
     * @return int|void
     * @throws NotSupportedException
     */
    public static function updateAll($attributes, $condition = '')
    {
        $primary = static::primaryKey();
        $primary = reset($primary);
        if (is_array($condition) && isset($condition[$primary])) {
            $primary = $condition[$primary];
            return static::_update($primary, $attributes);
        } else {
            throw new NotSupportedException(__METHOD__ . ' is only supported for model instance.');
        }
    }

    /**
     * @param array $counters
     * @param string $condition
     * @return mixed
     * @throws NotSupportedException
     */
    public static function updateAllCounters($counters, $condition = '')
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }

    /**
     * @param string $condition
     * @param array $params
     * @return int|void
     * @throws NotSupportedException
     */
    public static function deleteAll($condition = '', $params = [])
    {
        $primary = static::primaryKey();
        $primary = reset($primary);
        if (is_array($condition) && isset($condition[$primary])) {
            $primary = $condition[$primary];
            return static::_delete($primary);
        } else {
            throw new NotSupportedException(__METHOD__ . ' is only supported for model instance.');
        }
    }

    /**
     * @param string $name
     * @param \yii\db\ActiveRecordInterface $model
     * @param array $extraColumns
     * @throws NotSupportedException
     */
    public function link($name, $model, $extraColumns = [])
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }

    /**
     * @param string $name
     * @param \yii\db\ActiveRecordInterface $model
     * @param bool $delete
     * @throws NotSupportedException
     */
    public function unlink($name, $model, $delete = false)
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }

    /**
     * @param string $name
     * @param bool $delete
     * @throws NotSupportedException
     */
    public function unlinkAll($name, $delete = false)
    {
        throw new NotSupportedException(__METHOD__ . ' is not supported.');
    }
}

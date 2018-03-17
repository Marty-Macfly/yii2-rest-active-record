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
     * @var Connection
     */
    protected $connect;

    public function init()
    {
        parent::init();
        $this->connect = self::getDb();
    }


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
        if (($data = $this->connect->create($values)) === false) {
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
        /* @var $rest Connection */
        $rest = Yii::$app->get('rest');
        $rest->modelClass = get_called_class();

        return $rest;
    }

    /**
     * @param array $attributes
     * @param string $condition
     * @return int|void
     * @throws NotSupportedException
     */
    public static function updateAll($attributes, $condition = '')
    {
        $primary = reset(static::primaryKey());
        if (is_array($condition) && isset($condition[$primary])) {
            $primary = $condition[$primary];
            return static::getDb()->update($primary, $attributes);
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
        $primary = reset(static::primaryKey());
        if (is_array($condition) && isset($condition[$primary])) {
            $primary = $condition[$primary];
            return static::getDb()->delete($primary);
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

<?php

namespace app\models;

use Faker\Provider\Payment;
use Yii;
use yii\behaviors\TimestampBehavior;
use app\models\User as GlobalUser;
use app\modules\bot\validators\RadiusValidator;
use app\modules\bot\validators\LocationLatValidator;
use app\modules\bot\validators\LocationLonValidator;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

/**
 * This is the model class for table "currency_exchange_order".
 *
 * @property int $id
 * @property int $user_id
 * @property int $selling_currency_id
 * @property int $buying_currency_id
 * @property float|null $selling_rate
 * @property float|null $buying_rate
 * @property float|null $selling_currency_min_amount
 * @property float|null $selling_currency_max_amount
 * @property int $status
 * @property int $delivery_radius
 * @property string|null $location_lat
 * @property string|null $location_lon
 * @property int $created_at
 * @property int|null $processed_at
 * @property int $selling_cash_on
 * @property int $buying_cash_on
 * @property int $cross_rate_on
 *
 * @property CurrencyExchangeOrderBuyingPaymentMethod[] $currencyExchangeOrderBuyingPaymentMethods
 * @property CurrencyExchangeOrderMatch[] $currencyExchangeOrderMatches
 * @property CurrencyExchangeOrderMatch[] $currencyExchangeOrderMatches0
 * @property CurrencyExchangeOrderSellingPaymentMethod[] $currencyExchangeOrderSellingPaymentMethods
 */
class CurrencyExchangeOrder extends \yii\db\ActiveRecord
{
    public const STATUS_OFF = 0;
    public const STATUS_ON = 1;

    public const LIVE_DAYS = 30;

    public const CROSS_RATE_OFF = 0;
    public const CROSS_RATE_ON = 1;

    public const CASH_OFF = 0;
    public const CASH_ON = 1;

    public $updateBuyingPaymentMethods = [];
    public $updateSellingPaymentMethods = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currency_exchange_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'user_id',
                    'selling_currency_id',
                    'buying_currency_id',
                ],
                'required',
            ],
            [
                [
                    'user_id',
                    'selling_currency_id',
                    'buying_currency_id',
                    'status',
                    'delivery_radius',
                    'created_at',
                    'processed_at',
                    'selling_cash_on',
                    'buying_cash_on',
                    'cross_rate_on',
                ],
                'integer',
            ],
            [
                'delivery_radius',
                RadiusValidator::class,
            ],
            [
                'location_lat',
                LocationLatValidator::class,
            ],
            [
                'location_lon',
                LocationLonValidator::class,
            ],
            ['location', 'required', 'when' => function ($model) {
                $loc = $model->location;
                if (($model->selling_cash_on || $model->buying_cash_on) && !$model->location) {
                    $model->addError('location', 'Location is Required');
                }
            }, 'whenClient' => new JsExpression("function(attribute, value) {
                return $('#cashBuyCheckbox').prop('checked') || $('#cashSellCheckbox').prop('checked');
            }")
            ],
            [
                'location', 'string',
            ],
            [
                [
                    'selling_rate',
                    'buying_rate',
                ],
                'double',
                'min' => 0,
                'max' => 9999999999999.99,
            ],
            [['updateSellingPaymentMethods', 'updateBuyingPaymentMethods'], 'each', 'rule' => ['integer']],
            [
                [
                    'selling_currency_min_amount',
                    'selling_currency_max_amount',
                ],
                'double',
                'min' => 0,
                'max' => 9999999999.99999999,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'selling_currency_id' => 'Selling Currency ID',
            'buying_currency_id' => 'Buying Currency ID',
            'selling_rate' => Yii::t('bot', 'Exchange rate'),
            'buying_rate' => Yii::t('bot', 'Reverse exchange rate'),
            'selling_currency_min_amount' => Yii::t('bot', 'Min. amount'),
            'selling_currency_max_amount' => Yii::t('bot', 'Max. amount'),
            'status' => Yii::t('bot', 'Status'),
            'delivery_radius' => Yii::t('bot', 'Delivery radius'),
            'location_lat' => 'Location Lat',
            'location_lon' => 'Location Lon',
            'created_at' => 'Created At',
            'processed_at' => 'Processed At',
            'selling_cash_on' => Yii::t('bot', 'Cash'),
            'buying_cash_on' => Yii::t('bot', 'Cash'),
            'cross_rate_on' => 'Cross Rate On',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * @param string $location
     * @return $this
     */
    public function setLocation(string $location): self
    {
        $latLon = explode(',', $location);
        if (count($latLon) === 2) {
            $this->location_lat = $latLon[0] ?? '';
            $this->location_lon = $latLon[1] ?? '';
        }

        return $this;
    }

    public function getLocation(): string
    {
        return ($this->location_lat && $this->location_lon) ? implode(',',[$this->location_lat, $this->location_lon]): '';
    }


    /**
     * Gets query for [[CurrencyExchangeOrderSellingPaymentMethods]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSellingPaymentMethods()
    {
        return $this->hasMany(PaymentMethod::className(), ['id' => 'payment_method_id'])
            ->viaTable('{{%currency_exchange_order_selling_payment_method}}', ['order_id' => 'id']);
    }


    public function updateSellingPaymentMethods() {
        $ids = $this->updateSellingPaymentMethods;
        if ($ids) {
            $currentMethodsIds = ArrayHelper::getColumn($this->getSellingPaymentMethods()->asArray()->all(), 'id');

            $toDelete = array_diff($currentMethodsIds, $ids);
            $toLink = array_diff($ids, $currentMethodsIds);

            CurrencyExchangeOrderSellingPaymentMethod::deleteAll(['AND', ['order_id' => $this->id], ['in', 'payment_method_id', $toDelete]]);

            foreach ($toLink as $id) {
                $this->link('sellingPaymentMethods', PaymentMethod::findOne($id));
            }
        }
    }

    /**
     * Gets query for [[CurrencyExchangeOrderBuyingPaymentMethods]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBuyingPaymentMethods()
    {
        return $this->hasMany(PaymentMethod::className(), ['id' => 'payment_method_id'])
            ->viaTable('{{%currency_exchange_order_buying_payment_method}}', ['order_id' => 'id']);
    }


    public function updateBuyingPaymentMethods() {
        $ids = $this->updateBuyingPaymentMethods;
        $s = !empty($ids);
        if ($ids) {
            $currentMethodsIds = ArrayHelper::getColumn($this->getBuyingPaymentMethods()->asArray()->all(), 'id');

            $toDelete = array_diff($currentMethodsIds, $ids);
            $toLink = array_diff($ids, $currentMethodsIds);

            CurrencyExchangeOrderBuyingPaymentMethod::deleteAll(['AND', ['order_id' => $this->id], ['in', 'payment_method_id', $toDelete]]);

            foreach ($toLink as $id) {
                $this->link('buyingPaymentMethods', PaymentMethod::findOne($id));
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getMatches()
    {
        return $this->hasMany(self::className(), ['id' => 'match_order_id'])
            ->viaTable('{{%currency_exchange_order_match}}', ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getCounterMatches()
    {
        return $this->hasMany(self::className(), ['id' => 'order_id'])
            ->viaTable('{{%currency_exchange_order_match}}', ['match_order_id' => 'id']);
    }

    public function updateMatches()
    {
        $this->unlinkAll('matches', true);
        $this->unlinkAll('counterMatches', true);

        return true;
    }

    public function getGlobalUser()
    {
        return $this->hasOne(GlobalUser::className(), ['id' => 'user_id']);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->sellingCurrency->code . '/' . $this->buyingCurrency->code;
    }

    /**
     * @return string
     */
    public function getReverseTitle()
    {
        return $this->buyingCurrency->code . '/' . $this->sellingCurrency->code;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSellingCurrency()
    {
        return $this->hasOne(Currency::class, ['id' => 'selling_currency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuyingCurrency()
    {
        return $this->hasOne(Currency::class, ['id' => 'buying_currency_id']);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->status == self::STATUS_ON;
    }

    /**
     * {@inheritdoc}
     */
    public function clearMatches()
    {
        if ($this->processed_at !== null) {
            $this->unlinkAll('matches', true);
            $this->unlinkAll('counterMatches', true);

            $this->setAttributes([
                'processed_at' => null,
            ]);

            $this->save();
        }
    }

    public function linkSellingCashPaymentMethod()
    {
        if ($cashMethod = PaymentMethod::findOne(['type' => PaymentMethod::TYPE_CASH])) {
            $this->link('sellingPaymentMethods', $cashMethod);
        }
    }
    public function linkBuyingCashPaymentMethod()
    {
        if ($cashMethod = PaymentMethod::findOne(['type' => PaymentMethod::TYPE_CASH])) {
            $this->link('buyingPaymentMethods', $cashMethod);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->updateBuyingPaymentMethods();
        $this->updateSellingPaymentMethods();

        if ($this->selling_cash_on ) {
            $this->linkSellingCashPaymentMethod();
        }
        if ($this->buying_cash_on) {
            $this->linkBuyingCashPaymentMethod();
        }

        $clearMatches = false;

        if (isset($changedAttributes['status'])) {
            if ($this->status == self::STATUS_OFF) {
                $clearMatches = true;
            }
        }

        if (isset($changedAttributes['cross_rate_on'])) {
            if ($this->cross_rate_on == self::CROSS_RATE_ON) {
                $clearMatches = true;
                Yii::warning('cross_rate_on');
            }
            Yii::warning('cross_rate_on2');
        }

        if (isset($changedAttributes['selling_rate'])) {
            $this->buying_rate = 1 / $this->selling_rate;
            $this->cross_rate_on = self::CROSS_RATE_OFF;
            $this->save();

            $clearMatches = true;
            Yii::warning('selling_rate');
        }

        if (isset($changedAttributes['buying_rate'])) {
            $this->selling_rate = 1 / $this->buying_rate;
            $this->cross_rate_on = self::CROSS_RATE_OFF;
            $this->save();

            $clearMatches = true;
            Yii::warning('buying_rate');
        }

        if (isset($changedAttributes['selling_currency_min_amount'])
            || isset($changedAttributes['selling_currency_max_amount'])) {
            $clearMatches = true;
            Yii::warning('selling_currency_min_amount selling_currency_max_amount');
        }

        if ($clearMatches) {
            $this->clearMatches();
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return array
     */
    public function notPossibleToChangeStatus()
    {
        $notFilledFields = [];

        if (($this->selling_cash_on == self::CASH_ON) || ($this->buying_cash_on == self::CASH_ON)) {
            if (!($this->location_lon && $this->location_lat)) {
                $notFilledFields[] = Yii::t('bot', $this->getAttributeLabel('location'));
            }
        }

        return $notFilledFields;
    }

    /**
     * @return string
     */
    public function getSellingCurrencyMinAmount()
    {
        if ($this->selling_currency_min_amount) {
            return number_format($this->selling_currency_min_amount, 2);
        } else {
            return '∞';
        }
    }

    /**
     * @return string
     */
    public function getSellingCurrencyMaxAmount()
    {
        if ($this->selling_currency_max_amount) {
            return number_format($this->selling_currency_max_amount, 2);
        } else {
            return '∞';
        }
    }

    public function hasAmount()
    {
        if ($this->selling_currency_min_amount || $this->selling_currency_max_amount) {
            return true;
        }

        return false;
    }
}

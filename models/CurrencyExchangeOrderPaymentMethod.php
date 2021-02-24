<?php

namespace app\models;

use app\models\CurrencyExchangeOrderPaymentMethodQuery;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "currency_exchange_order_payment_method".
 *
 * @property int $id
 * @property int $order_id
 * @property int $payment_method_id
 * @property int $type
 */
class CurrencyExchangeOrderPaymentMethod extends ActiveRecord
{

    const PAYMENT_METHOD_TYPE_SELL = 1;
    const PAYMENT_METHOD_TYPE_BUY = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'currency_exchange_order_payment_method';
    }

    public static function find()
    {
        return new CurrencyExchangeOrderPaymentMethodQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['order_id', 'payment_method_id', 'type'], 'required'],
            [['order_id', 'payment_method_id', 'type'], 'integer'],
            ['type', 'in', 'range' => [self::PAYMENT_METHOD_TYPE_BUY, self::PAYMENT_METHOD_TYPE_SELL]]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'payment_method_id' => 'Payment Method ID',
            'type' => 'Type',
        ];
    }

    public function getPaymentMethod(): ActiveQuery
    {
        return $this->hasOne(PaymentMethod::class, ['id' => 'payment_method_id']);
    }

    public function getCurrencyExchangeOrder(): ActiveQuery
    {
        return $this->hasOne(CurrencyExchangeOrder::class, ['id' => 'order_id']);
    }
}

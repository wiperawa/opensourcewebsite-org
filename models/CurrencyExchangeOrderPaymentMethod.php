<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "currency_exchange_order_payment_method".
 *
 * @property int $id
 * @property int $order_id
 * @property int $payment_method_id
 * @property int $type
 */
class CurrencyExchangeOrderPaymentMethod extends \yii\db\ActiveRecord
{

    const PAYMENT_METHOD_TYPE_SELL = 1;
    const PAYMENT_METHOD_TYPE_BUY = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currency_exchange_order_payment_method';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'payment_method_id', 'type'], 'required'],
            [['order_id', 'payment_method_id', 'type'], 'integer'],
            ['type', 'in', [self::PAYMENT_METHOD_TYPE_BUY, self::PAYMENT_METHOD_TYPE_SELL]]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'payment_method_id' => 'Payment Method ID',
            'type' => 'Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrencyExchangeOrder()
    {
        return $this->hasOne(CurrencyExchangeOrder::class, ['id' => 'order_id']);
    }
}

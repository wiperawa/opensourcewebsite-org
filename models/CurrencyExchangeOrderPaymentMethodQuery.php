<?php

namespace app\models;

use yii\db\ActiveQuery;

class CurrencyExchangeOrderPaymentMethodQuery extends ActiveQuery
{
    public function sell()
    {
        return $this->andFilterWhere(['type' => CurrencyExchangeOrderPaymentMethod::PAYMENT_METHOD_TYPE_SELL]);
    }

    public function buy()
    {
        return $this->andFilterWhere(['type' => CurrencyExchangeOrderPaymentMethod::PAYMENT_METHOD_TYPE_BUY]);
    }
}

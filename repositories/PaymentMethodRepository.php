<?php

namespace app\repositories;

use app\models\PaymentMethod;
use yii\db\ActiveQuery;

class PaymentMethodRepository {

    public function getPaymentMethods(): array
    {
        return PaymentMethod::find()->all();
    }

    public function getPaymentMethodsAsArray(): array
    {
        return PaymentMethod::find()->asArray()->all();
    }
}

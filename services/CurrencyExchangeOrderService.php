<?php
namespace app\services;

use app\models\CurrencyExchangeOrder;
use app\models\CurrencyExchangeOrderPaymentMethod;
use Yii;
use yii\helpers\ArrayHelper;

class CurrencyExchangeOrderService {


    public function updateOrderPaymentMethods( CurrencyExchangeOrder $order, array $paymentMethodsIds, int $paymentType)
    {

        $oldOrderPaymentMethodsIds = ArrayHelper::getColumn(
            $order->getCurrencyExchangeOrderPaymentMethods()
                ->where(['type' => $paymentType])
                ->all(),
    'payment_method_id'
        );
        $paymentMethodsIds = array_map(fn($val) => intval($val), $paymentMethodsIds );

        $toDeleteIds = array_diff($oldOrderPaymentMethodsIds, $paymentMethodsIds);
        $toCreateIds = array_diff($paymentMethodsIds, $oldOrderPaymentMethodsIds);

        $this->deleteOldOrderPaymentMethods($order, $toDeleteIds);
        $this->createOrderPaymentMethods($order, $toCreateIds, $paymentType);
    }

    private function deleteOldOrderPaymentMethods(CurrencyExchangeOrder $order, array $paymentMethodsIds)
    {
        CurrencyExchangeOrderPaymentMethod::deleteAll(['and', ['order_id' => $order->id], ['in', 'payment_method_id', $paymentMethodsIds]]);
    }

    private function createOrderPaymentMethods(CurrencyExchangeOrder $order, array $paymentMethodsIds, $paymentType)
    {
        foreach ($paymentMethodsIds as $paymentMethodId) {
            (new CurrencyExchangeOrderPaymentMethod([
                'order_id' => $order->id,
                'payment_method_id' => $paymentMethodId,
                'type' => $paymentType,
            ]))->save();
        }
    }
}



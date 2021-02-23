<?php
namespace app\services;

use app\models\CurrencyExchangeOrder;
use app\models\CurrencyExchangeOrderPaymentMethod;

class CurrencyExchangeOrderService {

    public function createOrUpdateSellBuyMethods(CurrencyExchangeOrder $model, int $sellPaymentId = null, int $buyPaymentId = null)
    {

        if ($sellPaymentId) {
            $this->createOrUpdatePaymentMethod(
                $model,
                $sellPaymentId,
                CurrencyExchangeOrderPaymentMethod::PAYMENT_METHOD_TYPE_SELL
            );
        }

        if ($buyPaymentId) {
            $this->createOrUpdatePaymentMethod(
                $model,
                $buyPaymentId,
                CurrencyExchangeOrderPaymentMethod::PAYMENT_METHOD_TYPE_BUY
            );
        }
    }

    private function createOrUpdatePaymentMethod(CurrencyExchangeOrder $order, int $paymentMethodId, int $type): CurrencyExchangeOrderPaymentMethod
    {
        if (!$orderPayment = $order
            ->getCurrencyExchangeOrderPaymentMethod()
            ->where(['type' => $type])
            ->one()) {
            $orderPayment = new CurrencyExchangeOrderPaymentMethod([
                'order_id' => $order->id,
                'payment_method_id' => $paymentMethodId,
                'type' => $type
            ]);
        } else {
            $orderPayment->payment_method_id = $paymentMethodId;
        }
        $orderPayment->save();
        return $orderPayment;
    }
}

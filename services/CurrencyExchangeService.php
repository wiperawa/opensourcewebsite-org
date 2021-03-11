<?php

namespace app\services;

use app\models\CurrencyExchangeOrder;
use app\models\CurrencyExchangeOrderBuyingPaymentMethod;
use app\models\CurrencyExchangeOrderSellingPaymentMethod;
use app\models\PaymentMethod;

class CurrencyExchangeService
{
    /**
     * Update Payment Methods for [[CurrencyExchangeOrder]]
     * if either buying or selling payment methods updated, it clear matched offers for this [[CurrencyExchangeOrder]] model
     * @param CurrencyExchangeOrder $order
     * @param array $sellPaymentMethods
     * @param array $buyPaymentMethods
     */
    public function updatePaymentMethods(CurrencyExchangeOrder $order, array $sellPaymentMethods, array $buyPaymentMethods): void
    {
        if ($this->updateSellingPaymentMethods($order, $sellPaymentMethods) || $this->updateBuyingPaymentMethods($order, $buyPaymentMethods)) {
            $order->clearMatches();
        }
    }

    /**
     * Meant to be called after each [[CurrencyExchangeOrder]] model create/update.
     * Update payment methods in order is cash method on or off.
     * @param CurrencyExchangeOrder $order
     */
    public function handleOrderUpdate(CurrencyExchangeOrder $order)
    {
        $this->updatePaymentMethods($order, $order->getCurrentSellingPaymentMethodsIds(), $order->getCurrentBuyingPaymentMethodsIds());
    }

    /**
     * Update CurrencyExchangeOrder model payment methods to buy
     * @param CurrencyExchangeOrder $order
     * @param array $newMethodsIds
     * @return bool
     */
    public function updateBuyingPaymentMethods(CurrencyExchangeOrder $order, array $newMethodsIds): bool
    {

        [$toDelete, $toLink] = $this->getToDeleteAndToLinkIds(
            $order->getCurrentBuyingPaymentMethodsIds(),
            $newMethodsIds,
            (bool)$order->buying_cash_on
        );

        if ($toDelete) {
            CurrencyExchangeOrderBuyingPaymentMethod::deleteAll(['AND', ['order_id' => $order->id], ['in', 'payment_method_id', $toDelete]]);
        }

        foreach ($toLink as $id) {
            $order->link('buyingPaymentMethods', PaymentMethod::findOne($id));
        }
        return (!!$toDelete || !!$toLink);
    }

    /**
     * Update CurrencyExchangeOrder model payment methods to sell
     * @param CurrencyExchangeOrder $order
     * @param array $newMethodsIds
     * @return bool
     */
    public function updateSellingPaymentMethods(CurrencyExchangeOrder $order, array $newMethodsIds): bool
    {

        [$toDelete, $toLink] = $this->getToDeleteAndToLinkIds(
            $order->getCurrentSellingPaymentMethodsIds(),
            $newMethodsIds,
            (bool)$order->selling_cash_on
        );

        if ($toDelete) {
            CurrencyExchangeOrderSellingPaymentMethod::deleteAll(['AND', ['order_id' => $order->id], ['in', 'payment_method_id', $toDelete]]);
        }
        foreach ($toLink as $id) {
            $order->link('sellingPaymentMethods', PaymentMethod::findOne($id));
        }
        return (!!$toDelete || !!$toLink);

    }

    private function getToDeleteAndToLinkIds(array $currentMethodsIds, array $newMethodsIds, bool $cashIsOn): array
    {
        $toDelete = array_values(array_diff($currentMethodsIds, $newMethodsIds));
        $toLink = array_values(array_diff($newMethodsIds, $currentMethodsIds));

        if ($cashMethod = PaymentMethod::findOne(['type' => PaymentMethod::TYPE_CASH])) {
            if ($cashIsOn) {
                $toDelete = array_diff($toDelete, [$cashMethod->id]);
                if (!in_array($cashMethod->id, $currentMethodsIds)) {
                    $toLink[] = $cashMethod->id;
                }
            } else {
                if (in_array($cashMethod->id, $currentMethodsIds)) {
                    $toDelete[] = $cashMethod->id;
                }
            }
        }
        return [$toDelete, $toLink];
    }

}

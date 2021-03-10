<?php

namespace app\models\FormModels\CurrencyExchange;

use app\models\CurrencyExchangeOrder;
use app\models\CurrencyExchangeOrderBuyingPaymentMethod;
use app\models\CurrencyExchangeOrderSellingPaymentMethod;
use app\models\PaymentMethod;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class OrderPaymentMethods extends Model {

    public $sellingPaymentMethods = [];
    public $buyingPaymentMethods = [];

    private CurrencyExchangeOrder $_order;

    /**
     * {@inheritDoc}
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->buyingPaymentMethods = $this->getBuyingPaymentMethodsIdsFromModel();
        $this->sellingPaymentMethods = $this->getSellingPaymentMethodsIdsFromModel();
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [
                ['sellingPaymentMethods', 'buyingPaymentMethods'],
                'filter', 'filter' => function ($value) {
                    return is_array($value) ? array_map('intval', $value): [];
                }
            ],
        ];
    }

    /**
     * Update CurrencyExchangeOrder model payment methods to buy
     * @throws \yii\base\InvalidConfigException
     */
    public function updateBuyingPaymentMethods()
    {
        $newMethodsIds = $this->buyingPaymentMethods;

        $order = $this->_order;

        $currentMethodsIds = $this->getBuyingPaymentMethodsIdsFromModel();

        $toDelete =  array_values(array_diff($currentMethodsIds, $newMethodsIds));
        $toLink = array_values(array_diff($newMethodsIds, $currentMethodsIds));

        if ( $cashMethod = PaymentMethod::findOne(['type' => PaymentMethod::TYPE_CASH]) ) {
            if ($order->buying_cash_on) {
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
        if ($toDelete) {
            CurrencyExchangeOrderBuyingPaymentMethod::deleteAll(['AND', ['order_id' => $order->id], ['in', 'payment_method_id', $toDelete]]);
        }

        foreach ($toLink as $id) {
            $order->link('buyingPaymentMethods', PaymentMethod::findOne($id));
        }
    }

    /**
     * Update CurrencyExchangeOrder model payment methods to sell
     * @throws \yii\base\InvalidConfigException
     */
    public function updateSellingPaymentMethods()
    {
        $newMethodsIds = $this->sellingPaymentMethods;

        $order = $this->_order;

        $currentMethodsIds = $this->getSellingPaymentMethodsIdsFromModel();

        $toDelete = array_values(array_diff($currentMethodsIds, $newMethodsIds));
        $toLink = array_values(array_diff($newMethodsIds, $currentMethodsIds));

        if ($cashMethod = PaymentMethod::findOne(['type' => PaymentMethod::TYPE_CASH])) {
            if ($order->selling_cash_on) {
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
        if ($toDelete) {
            CurrencyExchangeOrderSellingPaymentMethod::deleteAll(['AND', ['order_id' => $order->id], ['in', 'payment_method_id', $toDelete]]);
        }
        foreach ($toLink as $id) {
            $order->link('sellingPaymentMethods', PaymentMethod::findOne($id));
        }
    }

    public function updatePaymentMethods()
    {
        $this->updateBuyingPaymentMethods();
        $this->updateSellingPaymentMethods();
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        return [
            'sellingPaymentMethods' => Yii::t('app', 'Payment methods for Buy'),
            'buyingPaymentMethods' => Yii::t('app', 'Payment methods for Sell'),
        ];
    }

    public function setOrder(CurrencyExchangeOrder $order)
    {
        $this->_order = $order;
    }

    public function getOrder(): CurrencyExchangeOrder
    {
        return $this->_order;
    }

    private function getSellingPaymentMethodsIdsFromModel(): array
    {
        return array_map(
            'intval',
            ArrayHelper::getColumn($this->_order->getSellingPaymentMethods()->asArray()->all(), 'id')
        );
    }

    private function getBuyingPaymentMethodsIdsFromModel(): array
    {
        return  array_map(
            'intval',
            ArrayHelper::getColumn($this->_order->getBuyingPaymentMethods()->asArray()->all(), 'id')
        );
    }
}

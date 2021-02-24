<?php

use app\models\CurrencyExchangeOrder;
use yii\widgets\ActiveForm;
use yii\web\View;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\widgets\buttons\CancelButton;
use app\widgets\buttons\DeleteButton;
use app\widgets\buttons\SaveButton;

/**
 * @var $this View
 * @var $model CurrencyExchangeOrder
 * @var $paymentsBuyTypes array
 * @var $paymentSellTypes array
 */

$this->title = Yii::t('app', 'Update Currency Exchange Order Payment Methods ' . $model->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Currency Exchange Order Payment Methods'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

?>

<div class="currency-exchange-order-form">

    <?php ActiveForm::begin() ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <label class="control-label"><?= Yii::t('app', 'Payment methods for Sell'); ?></label>
                            <?= Select2::widget([
                                'name' => 'CurrencyExchangeOrder[updateSellingPaymentMethods]',
                                'theme' => Select2::THEME_DEFAULT,
                                'data' => ArrayHelper::map($paymentsSellTypes, 'id', 'name'),
                                'value' => ArrayHelper::getColumn($model->getSellingPaymentMethods()->all(), 'id'),
                                'options' => [
                                    'placeholder' => 'Select a payment...',
                                    'multiple' => true,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'closeOnSelect' => false,
                                ],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label class="control-label"><?= Yii::t('app', 'Payment methods for Buy'); ?></label>
                            <?= Select2::widget([
                                'name' => 'CurrencyExchangeOrder[updateBuyingPaymentMethods]',
                                'theme' => Select2::THEME_DEFAULT,
                                'data' => ArrayHelper::map($paymentsBuyTypes, 'id', 'name'),
                                'value' => ArrayHelper::getColumn($model->getBuyingPaymentMethods()->all(), 'id'),
                                'options' => [
                                    'placeholder' => 'Select a payment...',
                                    'multiple' => true,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'closeOnSelect' => false,
                                ],
                            ]); ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <?= SaveButton::widget(); ?>
                    <?= CancelButton::widget(['url' => ['view', 'id' => $model->id]]); ?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>

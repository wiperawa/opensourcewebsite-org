<?php

use app\models\CurrencyExchangeOrder;
use yii\widgets\ActiveForm;
use yii\web\View;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\widgets\buttons\CancelButton;
use app\widgets\buttons\SaveButton;

/**
 * @var $this View
 * @var $model CurrencyExchangeOrder
 * @var $paymentsSellTypes array
 */

$this->title = Yii::t('app', 'Update Currency Exchange Order Payment Sell Methods ' . $model->id);

?>
<div class="modal-header">
    <h4 class="modal-title"><?= Yii::t('backend', $this->title) ?></h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class="modal-body">

    <div class="currency-exchange-order-form">

        <?php ActiveForm::begin() ?>
        <div class="row">
            <div class="col-12">
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
                <div class="modal-footer">
                    <?= SaveButton::widget(); ?>
                    <?= CancelButton::widget(['url' => ['view', 'id' => $model->id]]); ?>
                </div>

            </div>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>

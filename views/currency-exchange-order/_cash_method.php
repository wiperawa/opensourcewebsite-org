<?php

use app\models\CurrencyExchangeOrder;
use app\models\PaymentMethod;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $form ActiveForm
 * @var $model CurrencyExchangeOrder
 * @var $cashPaymentMethod PaymentMethod
 */

?>

    <div class="row">
        <div class="col">
            <?= $form->field($model, 'updateSellingPaymentMethods[]')->hiddenInput(['id' => 'sellMethods'])->label(false) ?>
            <?= $form->field($model, 'updateBuyingPaymentMethods[]')->hiddenInput(['id' => 'buyMethods'])->label(false) ?>
            <div class="custom-control custom-switch">
                <input type="checkbox"
                       name="CurrencyExchangeOrder[selling_cash_on]"
                       <?=$model->selling_cash_on?'checked':''?>
                       value="1"
                       class="custom-control-input allowCacheCheckbox"
                       id="cashSellCheckbox"
                       data-target="#sellMethods"
                >
                <label class="custom-control-label" for="cashSellCheckbox">Cash Sell</label>
            </div>
            <div class="custom-control custom-switch">
                <input type="checkbox"
                       name="CurrencyExchangeOrder[buying_cash_on]"
                       <?=$model->buying_cash_on?'checked':''?>
                       value="1"
                       class="custom-control-input allowCacheCheckbox"
                       id="cashBuyCheckbox"
                       data-target="#buyMethods"
                >
                <label class="custom-control-label" for="cashBuyCheckbox">Cash Buy</label>
            </div>
        </div>
    </div>

<?php
$this->registerJs(<<<JS
    const cashPaymentMethodId = {$cashPaymentMethod->id};
    const locationRadiusDiv = $('.location-radius-div');


    function updateVisibility() {
        ($('#cashSellCheckbox').prop('checked') || $('cashBuyCheckbox').prop('checked')) ?
         locationRadiusDiv.show() :
         locationRadiusDiv.hide();
    }

    $('.allowCacheCheckbox').on('click', function(){
        $($(this).data('target')).val($(this).prop('checked')?[cashPaymentMethodId]:[]);
        updateVisibility();
    })



    updateVisibility();
JS
);

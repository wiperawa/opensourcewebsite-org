<?php

use app\models\Currency;
use app\models\CurrencyExchangeOrder;
use app\widgets\buttons\CancelButton;
use app\widgets\buttons\DeleteButton;
use app\widgets\buttons\SaveButton;
use dosamigos\leaflet\layers\Marker;
use dosamigos\leaflet\layers\TileLayer;
use dosamigos\leaflet\types\LatLng;
use dosamigos\leaflet\widgets\Map;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use dosamigos\leaflet\LeafLet;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model CurrencyExchangeOrder */
/* @var $form yii\widgets\ActiveForm */
/* @var $currencies Currency[] */
/* @var $paymentsTypes array|null */
$a = ArrayHelper::getColumn($model->getCurrencyExchangeOrderPaymentMethods()->sell()->all(),'id');

$labelOptional = ' (' . Yii::t('app', 'optional') . ')';
?>
    <div class="currency-exchange-order-form">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <?php if ($model->isNewRecord): ?>
                            <?=$this->render('__sell_buy_currency_fields',
                                [
                                    'form' => $form,
                                    'model' => $model,
                                    'currencies' => $currencies
                                ]
                            )?>
                        <?php else: ?>
                            <div class="row">
                                <div class="col d-flex">
                                    <p>Sell Currency:</p>&nbsp;<strong><?=$model->getSellingCurrency()->name?></strong>
                                </div>
                                <div class="col d-flex">
                                    <p>Buying currency: </p>&nbsp;<strong><?=$model->getBuyingCurrency()->name?></strong>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col">
                                <?= $form->field($model, 'selling_rate')
                                    ->textInput(['maxlength' => true])
                                    ->label($model->getAttributeLabel('selling_rate')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <?= $form->field($model, 'selling_currency_min_amount')
                                    ->textInput(['maxlength' => true])
                                    ->label($model->getAttributeLabel('selling_currency_min_amount') . $labelOptional); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <?= $form->field($model, 'selling_currency_max_amount')
                                    ->textInput(['maxlength' => true])
                                    ->label($model->getAttributeLabel('selling_currency_max_amount') . $labelOptional); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <?= $form->field($model, 'delivery_radius')
                                    ->textInput(['maxlength' => true])
                                    ->label($model->getAttributeLabel('delivery_radius') . ', km' . $labelOptional); ?>
                            </div>
                        </div>
                        <strong><?= Yii::t('app', 'Location')?></strong>
                        <div class="row">
                            <div class="col">
                                <div class="input-group mb-3 align-items-start">
                                    <?= $form->field($model, 'location', ['options' => ['class' => 'form-group flex-grow-1']])
                                        ->textInput([
                                            'maxlength' => true,
                                            'id' => 'currency-exchange-order-location',
                                            'class' => 'form-control flex-grow-1'
                                        ])->label(false)
                                    ?>
                                    <span class="input-group-append">
                                        <button type="button" class="btn btn-info btn-flat" data-toggle="modal"
                                    data-target="#modal-xl">Map</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label class="control-label"><?= Yii::t('app', 'Payment method for Sell'); ?></label>
                                <?= Select2::widget([
                                    'name' => 'sellPaymentMethodsIds',
                                    'theme' => Select2::THEME_DEFAULT,
                                    'data' => ArrayHelper::map($paymentsTypes,'id', 'name'),
                                    'value' => ArrayHelper::getColumn($model->getCurrencyExchangeOrderPaymentMethods()->sell()->all(),'payment_method_id'),
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
                                <label class="control-label"><?= Yii::t('app', 'Payment method for Buy'); ?></label>
                                <?= Select2::widget([
                                    'name' => 'buyPaymentMethodsIds',
                                    'theme' => Select2::THEME_DEFAULT,
                                    'data' => ArrayHelper::map($paymentsTypes,'id', 'name'),
                                    'value' => ArrayHelper::getColumn($model->getCurrencyExchangeOrderPaymentMethods()->buy()->all(),'payment_method_id'),
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
                        <?= CancelButton::widget(['url' => '/currency-exchange-order']); ?>
                        <?php if ((string)$model->user_id === (string)Yii::$app->user->id) : ?>
                            <?= DeleteButton::widget([
                                'url' => ['currency-exchange-order/delete/', 'id' => $model->id],
                                'options' => [
                                    'id' => 'delete-currency-exchange-order'
                                ]
                            ]); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <div class="modal fade" id="modal-xl">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= Yii::t('app', 'Location') ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        <?php
                        $center = new LatLng(['lat' => 51.508, 'lng' => -0.11]);

                        $marker = new Marker([
                            'latLng' => $center,
                            'clientOptions' => [
                                'draggable' => true,
                            ],
                            'clientEvents' => [
                                'dragend' => 'function(e) {
                                    var marker = e.target;
                                    position = marker.getLatLng();
                                }'
                            ],
                        ]);

                        $tileLayer = new TileLayer([
                            'urlTemplate' => 'https://a.tile.openstreetmap.org/{z}/{x}/{y}.png',
                            'clientOptions' => [
                                'attribution' => 'Tiles Courtesy of <a href="http://www.mapquest.com/" target="_blank">MapQuest</a> ' .
                                    '<img src="http://developer.mapquest.com/content/osm/mq_logo.png">, ' .
                                    'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
                                'subdomains' => ['1', '2', '3', '4'],
                            ],
                        ]);

                        $leaflet = new LeafLet([
                            'center' => $center,
                            'clientEvents' => [
                                'load' => new JsExpression("
                                    function (e) {
                                        $(document).on('shown.bs.modal','#modal-xl',  function(){
                                            setTimeout(function() {
                                                e.sourceTarget.invalidateSize();
                                            }, 1);
                                        });
                                    }
                                ")
                            ]
                        ]);

                        $leaflet
                            ->addLayer($marker)
                            ->addLayer($tileLayer);

                        echo Map::widget([
                            'leafLet' => $leaflet,
                            'options' => [
                                'id' => 'leaflet',
                                'style' => 'height:500px',
                            ],
                        ]);
                        ?>
                    </p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="location-save-changes" type="button" class="btn btn-primary" data-dismiss="modal">Save
                        changes
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php

$urlRedirect = Yii::$app->urlManager->createUrl(['/currency-exchange-order']);
$jsMessages = [
    'delete-confirm' => Yii::t('app', 'Are you sure you want to delete this order') . '?',
    'delete-error' => Yii::t('app', 'Sorry, there was an error while trying to delete the order') . '.',
];

$this->registerJs(<<<JS

var position = {
    'lat': {$center->lat},
    'lng': {$center->lng}
};
var location = $('#currency-exchange-order-location');

$('#location-save-changes').on('click', function(e) {
    location.val(position.lat + ", " + position.lng);
})

$("#delete-currency-exchange-order").on("click", function(event) {
    event.preventDefault();
    var url = $(this).attr("href");

    if (confirm("{$jsMessages['delete-confirm']}")) {
        $.post(url, {}, function(result) {
            if (result == "1") {
                location.href = "$urlRedirect";
            } else {
                alert("{$jsMessages['delete-error']}");
            }
        });
    }

    return false;
});
JS
);

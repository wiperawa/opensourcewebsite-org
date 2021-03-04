<?php

use \app\models\CurrencyExchangeOrder;
use app\widgets\buttons\EditButton;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\CurrencyExchangeOrder */

$this->title = Yii::t('app', 'Currency Exchange Order') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Currency Exchange Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = '#' . $model->id;

?>

    <div class="currency-exchange-order-view">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex p-0">
                        <ul class="nav nav-pills ml-auto p-2">
                            <li class="nav-item align-self-center mr-3">
                                <div class="input-group-prepend">
                                    <div class="dropdown">
                                        <a class="btn <?= $model->isActive() ? 'btn-primary' : 'btn-default' ?> dropdown-toggle"
                                           href="#" role="button"
                                           id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true"
                                           aria-expanded="false">
                                            <?= $model->isActive() ? 'active' : 'inactive' ?>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <h6 class="dropdown-header"><?= $model->getAttributeLabel('Status') ?></h6>

                                            <a class="dropdown-item status-update <?= $model->isActive() ? 'active' : '' ?>"
                                               href="#"
                                               data-value="<?= CurrencyExchangeOrder::STATUS_ON ?>">Active</a>

                                            <a class="dropdown-item status-update <?= !$model->isActive() ? 'active' : '' ?>"
                                               href="#"
                                               data-value="<?= CurrencyExchangeOrder::STATUS_OFF ?>">Inactive</a>
                                        </div>
                                    </div>
                            </li>
                            <li class="nav-item align-self-center mr-3">
                                <?= EditButton::widget([
                                    'url' => ['currency-exchange-order/update', 'id' => $model->id],
                                    'options' => [
                                        'title' => 'Edit Currency Exchange Order',
                                    ]
                                ]); ?>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <div id="w0" class="grid-view">
                                <table class="table table-condensed table-hover" style="margin-bottom: 0;">
                                    <caption>Currency exchange orders</caption>
                                    <tbody>
                                    <tr>
                                        <th class="align-middle"
                                            scope="col"><?= $model->getAttributeLabel('selling_currency_id') . '/' . $model->getAttributeLabel('buying_currency_id'); ?></th>
                                        <td class="align-middle"><?= $model->sellingCurrency->code . '/' . $model->buyingCurrency->code; ?></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th class="align-middle"
                                            scope="col"><?= $model->getAttributeLabel('selling_rate'); ?></th>
                                        <td class="align-middle"><?= round($model->getCurrentSellingRate(), 8) ?></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th class="align-middle"
                                            scope="col"><?= $model->getAttributeLabel('buying_rate'); ?></th>
                                        <td class="align-middle"><?= round($model->getCurrentBuyingRate(), 8) ?></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th class="align-middle"
                                            scope="col"><?= $model->getAttributeLabel('selling_currency_min_amount'); ?></th>
                                        <td class="align-middle"><?= $model->getSellingCurrencyMinAmount() ?></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th class="align-middle"
                                            scope="col"><?= $model->getAttributeLabel('selling_currency_max_amount'); ?></th>
                                        <td class="align-middle"><?= $model->getSellingCurrencyMaxAmount() ?></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th class="align-middle"
                                            scope="col"><?= $model->getAttributeLabel('delivery_radius'); ?></th>
                                        <td class="align-middle"><?= $model->delivery_radius; ?></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th class="align-middle" scope="col"><?= Yii::t('app', 'Location'); ?></th>
                                        <td class="align-middle"><?= $model->location_lat . ', ' . $model->location_lon; ?></td>
                                        <td></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?=Yii::t('app', 'Payment methods for Sell')?></h3>
                    <div class="card-tools">
                        <a class="edit-btn edit-btn-ajax"
                           href="/currency-exchange-order/update-sell-methods/<?= $model->id ?>"
                           title="Edit" style="float: right">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <div id="w0" class="grid-view">
                            <table class="table table-condensed table-hover" style="margin-bottom: 0;">
                                <tbody>
                                    <?php foreach ($model->getSellingPaymentMethods()->all() as $method):?>
                                        <tr>
                                            <td>
                                                <?=$method->name?>
                                            </td>
                                        </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?=Yii::t('app', 'Payment methods for Buy')?></h3>
                    <div class="card-tools">
                        <a class="edit-btn edit-btn-ajax"
                           href="/currency-exchange-order/update-buy-methods/<?= $model->id ?>"
                           title="Edit" style="float: right">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <div id="w0" class="grid-view">
                            <table class="table table-condensed table-hover" style="margin-bottom: 0;">
                                <tbody>
                                    <?php foreach ($model->getBuyingPaymentMethods()->all() as $method):?>
                                        <tr>
                                            <td>
                                                <?=$method->name?>
                                            </td>
                                        </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$url = Yii::$app->urlManager->createUrl(['currency-exchange-order/status?id=' . $model->id]);
$script = <<<JS

$('.edit-btn-ajax').on('click', function(e){
    e.preventDefault();
    $('#main-modal').find('.modal-content').load($(this).attr('href'), function(){ $('#main-modal').modal('show') });
    return false;
});

$('.status-update').on("click", function(event) {
    var status = $(this).data('value');
        $.post('{$url}', {'status': status}, function(result) {
            if (result === "1") {
                location.reload();
            }
            else {
                var response = $.parseJSON(result);
                $('#main-modal-header').text('Warning!');
                $('#main-modal-body').html(response);
                $('#main-modal').show();
                $('.close').on('click', function() {
                    $("#main-modal-body").html("");
                    $('#main-modal').hide();
                });
                // alert('Sorry, there was an error while trying to change status');
            }
        });

    return false;
});
JS;
$this->registerJs($script);

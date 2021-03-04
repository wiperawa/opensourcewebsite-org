<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this \yii\web\View
 * @var $model \app\models\CurrencyExchangeOrder
 */
?>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <div id="w0" class="grid-view">
                <table class="table table-condensed table-hover" style="margin-bottom: 0;">
                    <tbody>
                    <tr>
                        <th class="align-middle" scope="col">
                            <?= $model->getAttributeLabel('selling_currency_id') . '/' . $model->getAttributeLabel('buying_currency_id'); ?>
                        </th>
                        <td class="align-middle"><?= $model->sellingCurrency->code . '/' . $model->buyingCurrency->code; ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="align-middle"
                            scope="col"><?= $model->getAttributeLabel('selling_rate') ?></th>
                        <td class="align-middle"><?= round($model->getCurrentSellingRate(), 8) ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="align-middle"
                            scope="col"><?= $model->getAttributeLabel('buying_rate') ?></th>
                        <td class="align-middle"><?= round($model->getCurrentBuyingRate(), 8) ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="align-middle"
                            scope="col"><?= $model->getAttributeLabel('selling_currency_min_amount') ?></th>
                        <td class="align-middle"><?= $model->getSellingCurrencyMinAmount() ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="align-middle"
                            scope="col"><?= $model->getAttributeLabel('selling_currency_max_amount') ?></th>
                        <td class="align-middle"><?= $model->getSellingCurrencyMaxAmount() ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="align-middle"
                            scope="col"><?= $model->getAttributeLabel('delivery_radius') ?></th>
                        <td class="align-middle"><?= $model->delivery_radius; ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="align-middle" scope="col"><?= Yii::t('app', 'Location') ?></th>
                        <td class="align-middle">
                            <?= ($model->selling_cash_on || $model->buying_cash_on) ?
                                Html::a('view', Url::to(['view-order-location', 'id' => $model->id]), ['class' => 'modal-btn-ajax']) : ''
                            ?>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="align-middle" scope="col"><?= Yii::t('app', 'Selling Payment Methods') ?>:</th>
                        <td class="align-middle">
                            <?= implode(',', ArrayHelper::getColumn($model->getSellingPaymentMethods()->asArray()->all(), 'name')) ?>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="align-middle" scope="col"><?= Yii::t('app', 'Buying Payment Methods') ?>:</th>
                        <td class="align-middle">
                            <?= implode(',', ArrayHelper::getColumn($model->getBuyingPaymentMethods()->asArray()->all(), 'name')) ?>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="align-middle" scope="col"><?= Yii::t('app', 'User Profile') ?>:</th>
                        <td class="align-middle">
                            <?= Html::a('view', Url::to(['/contact/view', 'id' => $model->user_id]), ['target' => '_blank']) ?>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th class="align-middle" scope="col"><?= Yii::t('app', 'Email') ?>:</th>
                        <td class="align-middle">
                            <?= Html::a($model->user->email, 'mailto:'.$model->user->email, ['target' => '_blank']) ?>
                        </td>
                        <td></td>
                    </tr>
                    <?php if ($model->user->botUser): ?>
                        <tr>
                            <th class="align-middle" scope="col"><?= Yii::t('app', 'Telegram') ?>:</th>
                            <td class="align-middle">
                                <?= $model->user->botUser->getFullLink() ?>
                            </td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

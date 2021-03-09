<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this \yii\web\View
 * @var $model \app\models\CurrencyExchangeOrder
 */
$this->title = Yii::t('app', 'Offer');
?>
<div class="modal-header">
    <h4 class="modal-title"><?= $this->title ?></h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
</div>
<div class="modal-body">
    <div class="table-responsive">
        <div id="w0" class="grid-view">
            <table class="table table-condensed table-hover" style="margin-bottom: 0;">
                <tbody>
                <tr>
                    <th class="align-middle" scope="col" style="width: 50%">
                        <?= $model->getAttributeLabel('selling_currency_id') . '/' . $model->getAttributeLabel('buying_currency_id'); ?>
                    </th>
                    <td class="align-middle"><?= $model->sellingCurrency->code . '/' . $model->buyingCurrency->code; ?></td>
                    <td></td>
                </tr>
                <tr>
                    <th class="align-middle"
                        scope="col"><?= $model->getAttributeLabel('selling_rate'); ?></th>
                    <td class="align-middle">
                        <?=
                        !$model->cross_rate_on ?
                            round($model->selling_rate, 8) :
                            Yii::t('app', 'Cross Rate')
                        ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th class="align-middle"
                        scope="col"><?= $model->getAttributeLabel('buying_rate'); ?></th>
                    <td class="align-middle">
                        <?=
                        !$model->cross_rate_on ?
                            round($model->buying_rate, 8) :
                            Yii::t('app', 'Cross Rate')
                        ?>
                    </td>
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
                    <th class="align-middle" scope="col"><?= Yii::t('app', 'User Profile') ?>:</th>
                    <td class="align-middle">
                        <?= Html::a('view', Url::to(['/contact/view', 'id' => $model->user_id]), ['target' => '_blank']) ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th class="align-middle" scope="col"><?= Yii::t('app', 'Email') ?>:</th>
                    <td class="align-middle">
                        <?= Html::a($model->user->email, 'mailto:' . $model->user->email, ['target' => '_blank']) ?>
                    </td>
                    <td></td>
                </tr>
                <?php if ($model->user->botUser): ?>
                    <tr>
                        <th class="align-middle" scope="col"><?= Yii::t('app', 'Telegram') ?>:</th>
                        <td class="align-middle">
                            <?= Html::a($model->user->botUser->getFullName(),
                                'https://t.me/user?id=' . $model->user->botUser->provider_user_id,
                                ['target' => '_blank']
                            ) ?>
                        </td>
                        <td></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table table-condensed table-hover">
                    <tr>
                        <th>Payment Methods to Sell</th>
                    </tr>
                    <?php foreach ($model->getSellingPaymentMethods()->asArray()->all() as $method): ?>
                        <tr>
                            <td><?= $method['name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table table-condensed table-hover">
                    <tr>
                        <th>Payment Methods to Buy</th>
                    </tr>
                    <?php foreach ($model->getBuyingPaymentMethods()->asArray()->all() as $method): ?>
                        <tr>
                            <td><?= $method['name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>


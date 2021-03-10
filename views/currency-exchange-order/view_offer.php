<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this \yii\web\View
 * @var $orderModel \app\models\CurrencyExchangeOrder
 * @var $matchOrderModel \app\models\CurrencyExchangeOrder
 */
$this->title = Yii::t('app', 'Offer');

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Currency exchange Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $orderModel->id, 'url' => ['view', 'id' => $orderModel->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Offers');
$this->params['breadcrumbs'][] = ['label' => $matchOrderModel->id, 'url' => ['view-offer', 'order_id' => $orderModel->id,'match_order_id' => $matchOrderModel->id]];

?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div id="w0" class="grid-view">
                        <table class="table table-condensed table-hover" style="margin-bottom: 0;">
                            <tbody>
                            <tr>
                                <th class="align-middle" scope="col" style="width: 50%;">
                                    <?= $matchOrderModel->getAttributeLabel('id') ?>
                                </th>
                                <td class="align-middle">
                                    <?=$matchOrderModel->id?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <th class="align-middle" scope="col" style="width: 50%">
                                    <?= $matchOrderModel->getAttributeLabel('selling_currency_id') . '/' . $matchOrderModel->getAttributeLabel('buying_currency_id'); ?>
                                </th>
                                <td class="align-middle"><?= $matchOrderModel->sellingCurrency->code . '/' . $matchOrderModel->buyingCurrency->code; ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <th class="align-middle"
                                    scope="col"><?= $matchOrderModel->getAttributeLabel('selling_rate'); ?></th>
                                <td class="align-middle">
                                    <?=
                                    !$matchOrderModel->cross_rate_on ?
                                        round($matchOrderModel->selling_rate, 8) :
                                        Yii::t('app', 'Cross Rate')
                                    ?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <th class="align-middle"
                                    scope="col"><?= $matchOrderModel->getAttributeLabel('buying_rate'); ?></th>
                                <td class="align-middle">
                                    <?=
                                    !$matchOrderModel->cross_rate_on ?
                                        round($matchOrderModel->buying_rate, 8) :
                                        Yii::t('app', 'Cross Rate')
                                    ?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <th class="align-middle"
                                    scope="col"><?= $matchOrderModel->getAttributeLabel('selling_currency_min_amount') ?></th>
                                <td class="align-middle"><?= $matchOrderModel->getSellingCurrencyMinAmount() ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <th class="align-middle"
                                    scope="col"><?= $matchOrderModel->getAttributeLabel('selling_currency_max_amount') ?></th>
                                <td class="align-middle"><?= $matchOrderModel->getSellingCurrencyMaxAmount() ?></td>
                                <td></td>
                            </tr>

                            <tr>
                                <th class="align-middle" scope="col"><?= Yii::t('app', 'User Profile') ?>:</th>
                                <td class="align-middle">
                                    <?= Html::a('view', Url::to(['/contact/view', 'id' => $matchOrderModel->user_id]), ['target' => '_blank']) ?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <th class="align-middle" scope="col"><?= Yii::t('app', 'Email') ?>:</th>
                                <td class="align-middle">
                                    <?= Html::a($matchOrderModel->user->email, 'mailto:' . $matchOrderModel->user->email, ['target' => '_blank']) ?>
                                </td>
                                <td></td>
                            </tr>
                            <?php if ($matchOrderModel->user->botUser): ?>
                                <tr>
                                    <th class="align-middle" scope="col"><?= Yii::t('app', 'Telegram') ?>:</th>
                                    <td class="align-middle">
                                        <?= Html::a($matchOrderModel->user->botUser->getFullName(),
                                            'https://t.me/user?id=' . $matchOrderModel->user->botUser->provider_user_id,
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
                                <?php foreach ($matchOrderModel->getSellingPaymentMethods()->asArray()->all() as $method): ?>
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
                                <?php foreach ($matchOrderModel->getBuyingPaymentMethods()->asArray()->all() as $method): ?>
                                    <tr>
                                        <td><?= $method['name'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php

/* @var $this yii\web\View */
/* @var $model app\models\CurrencyExchangeOrder */
/* @var $currencies Currency[] */
/* @var $paymentsTypes array|null */


use app\models\Currency;

$this->title = Yii::t('app', 'Update Currency Exchange Order ' . $model->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Currency Exchange Order'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

?>
<div class="currency-exchange-order-update">
    <?= $this->render('_form', [
        'model' => $model,
        'paymentsTypes' => $paymentsTypes,
        'currencies' => $currencies,
    ]); ?>
</div>

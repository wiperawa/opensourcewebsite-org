<?php

use app\models\Currency;
use app\models\CurrencyExchangeOrder;

/* @var $this yii\web\View */
/* @var $model CurrencyExchangeOrder */
/* @var $paymentsTypes array */
/* @var $currencies Currency */

$this->title = Yii::t('app', 'Create Currency Exchange Order');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Currency Exchange Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="currency-exchange-order-create">

    <?= $this->render('_form', [
        'model' => $model,
        'currencies' => $currencies,
        'paymentsTypes' => $paymentsTypes,
    ]); ?>

</div>

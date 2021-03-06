<?php
declare(strict_types=1);

use app\models\AdSearch;
use app\models\search\AdSearchSearch;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use app\widgets\buttons\AddButton;
use yii\grid\GridView;
use app\models\AdOffer;
/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var AdSearchSearch $searchModel
 */

$this->title = Yii::t('app', 'Searches');
$this->params['breadcrumbs'][] = $this->title;

$displayActiveAdSearch = (int)$searchModel->status === AdSearch::STATUS_ON;

?>
<div class="ad-search-index">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex p-0">
                    <ul class="nav nav-pills ml-auto p-2">
                        <li class="nav-item mx-1">
                            <?= Html::a(Yii::t('app', 'Active'),
                                ['/ad-search/index', 'AdSearchSearch[status]' => AdSearch::STATUS_ON],
                                [
                                    'class' => 'nav-link show ' .
                                        ($displayActiveAdSearch ? 'active' : '')
                                ]);
                            ?>
                        </li>
                        <li class="nav-item  mx-1">
                            <?= Html::a(Yii::t('app', 'Inactive'),
                                ['/ad-search/index', 'AdSearchSearch[status]' => AdSearch::STATUS_OFF],
                                [
                                    'class' => 'nav-link show ' .
                                        ($displayActiveAdSearch ? '' : 'active')
                                ]);
                            ?>
                        </li>
                        <li class="nav-item align-self-center mr-4 mx-1">
                            <?= AddButton::widget([
                                'url' => ['create'],
                                'options' => [
                                    'title' => 'New Search',
                                ]
                            ]); ?>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-0">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'summary' => false,
                        'tableOptions' => ['class' => 'table table-hover'],
                        'columns' => [
                            'id',
                            [
                                'attribute' => 'sectionName',
                                'label' => Yii::t('app', 'Section'),
                                'value' => function($model) {
                                    return $model->sectionName;
                                },
                                'enableSorting' => false,
                            ],
                            [
                                'attribute' => 'title',
                                'enableSorting' => false,
                            ],
                            [
                                'attribute' => 'max_price',
                                'content' => function (AdSearch $model) {
                                    return $model->max_price ? $model->max_price . ' ' . $model->currency->code : '∞';
                                },
                                'enableSorting' => false,
                            ],
                            [
                                'label' => 'Offers',
                                'enableSorting' => false,
                                'format' => 'raw',
                                'value' => function(AdSearch $model){
                                    return $model->getMatches()->count() ?
                                        Html::a(
                                            $model->getMatches()->count(),
                                            Url::to(['/ad-offer/show-matches', 'adSearchId' => $model->id]),
                                        ) : '';
                                }
                            ],
                            [
                                'class' => ActionColumn::class,
                                'template' => '{view}',
                                'buttons' => [
                                    'view' => function ($url) {
                                        $icon = Html::tag('span', '', ['class' => 'fa fa-eye', 'data-toggle' => 'tooltip', 'title' => 'view']);
                                        return Html::a($icon, $url, ['class' => 'btn btn-outline-primary mx-1']);
                                    },

                                ],
                            ],
                        ],

                        'layout' => "{summary}\n{items}\n<div class='card-footer clearfix'>{pager}</div>",
                        'pager' => [
                            'options' => [
                                'class' => 'pagination float-right',
                            ],
                            'linkContainerOptions' => [
                                'class' => 'page-item',
                            ],
                            'linkOptions' => [
                                'class' => 'page-link',
                            ],
                            'maxButtonCount' => 5,
                            'disabledListItemSubTagOptions' => [
                                'tag' => 'a',
                                'class' => 'page-link',
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

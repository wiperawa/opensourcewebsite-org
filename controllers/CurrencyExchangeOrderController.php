<?php

namespace app\controllers;

use app\models\Currency;
use app\repositories\PaymentMethodRepository;
use app\services\CurrencyExchangeOrderService;
use Yii;
use app\models\CurrencyExchangeOrder;
use app\models\CurrencyExchangeOrderPaymentMethod;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \app\components\helpers\ArrayHelper;
use \app\models\PaymentMethod;

/**
 * CurrencyExchangeOrderController implements the CRUD actions for CurrencyExchangeOrder model.
 */
class CurrencyExchangeOrderController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['@'],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all CurrencyExchangeOrder models.
     * @param int $status
     * @return mixed
     */
    public function actionIndex(int $status = CurrencyExchangeOrder::STATUS_ON)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => CurrencyExchangeOrder::find()
                ->where(['status' => $status])
                ->andWhere(['user_id' => Yii::$app->user->identity->id])
                ->orderBy(['selling_currency_id' => SORT_ASC, 'created_at' => SORT_DESC]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CurrencyExchangeOrder model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id)
    {
        $order = $this->findModel($id);



        return $this->render('view', [
            'model' => $order,
        ]);
    }

    /**
     * Creates a new CurrencyExchangeOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CurrencyExchangeOrder();
        $model->user_id = Yii::$app->user->identity->id;

        $cashPaymentMethod = PaymentMethod::findOne(['type' => PaymentMethod::TYPE_CASH]);

        if ($model->load(($post = Yii::$app->request->post())) && $model->save()) {

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'currencies' => Currency::find()->all(),
            'cashPaymentMethod' => $cashPaymentMethod,
        ]);
    }

    /**
     * Updates an existing CurrencyExchangeOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);

        if ($model->load($post = Yii::$app->request->post()) && $model->save()) {


            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'currencies' => Currency::find()->all(),
        ]);
    }


    public function actionUpdateSellBuyMethods($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save() ) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update_sell_buy_methods', [
            'model' => $model,
            'paymentsBuyTypes' => PaymentMethod::find()->joinWith('currencies')->where(['currency.id' => $model->buying_currency_id])->all(),
            'paymentsSellTypes' => PaymentMethod::find()->joinWith('currencies')->where(['currency.id' => $model->selling_currency_id])->all(),
        ]);
    }

    /**
     * Change status.
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionStatus($id)
    {
        if (Yii::$app->request->isAjax) {
            $postdata = Yii::$app->request->post();
            $order = $this->findModel($id);

            if ($postdata['status'] && $notFilledFields = $order->notPossibleToChangeStatus()) {
                return json_encode($notFilledFields);
            }
            $order->status = $postdata['status'];
            return $order->save();
        }
        return false;
    }

    /**
     * Deletes an existing CurrencyExchangeOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CurrencyExchangeOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CurrencyExchangeOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): CurrencyExchangeOrder
    {
        $model = CurrencyExchangeOrder::findOne($id);
        if ($model !== null && $model->user_id == Yii::$app->user->identity->id) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

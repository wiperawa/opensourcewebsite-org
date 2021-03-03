<?php

namespace app\commands;

use app\models\CurrencyExchangeOrder;
use Yii;
use yii\console\Controller;
use app\interfaces\CronChainedInterface;
use app\commands\traits\ControllerLogTrait;
use app\models\AdOffer;
use app\models\AdSearch;
use yii\console\Exception;

/**
 * Class CurrencyExchangeMatchController
 *
 * @package app\commands
 */
class CurrencyExchangeMatchController extends Controller implements CronChainedInterface
{
    use ControllerLogTrait;

    public function actionIndex()
    {
        $this->update();
    }

    protected function update()
    {
        $this->updateAdSearches();
    }

    protected function updateAdSearches()
    {
        $updatesCount = 0;

        $tblName = CurrencyExchangeOrder::tableName();
        $ordersQuery = CurrencyExchangeOrder::find()
            ->where([ "{$tblName}.processed_at" => null])
            ->andWhere(["{$tblName}.status" => CurrencyExchangeOrder::STATUS_ON])
            ->joinWith('globalUser')
            ->andWhere(['>=', 'user.last_activity_at', time() - CurrencyExchangeOrder::LIVE_DAYS * 24 * 60 * 60])
            ->orderBy(['user.rating' => SORT_DESC])
            ->addOrderBy(['user.created_at' => SORT_ASC]);

        /** @var CurrencyExchangeOrder $order */
        foreach ($ordersQuery->all() as $order) {
            try {
                $order->updateMatches();

                $order->setAttributes([
                    'processed_at' => time(),
                ]);
                $order->save();
                $updatesCount++;
            } catch (Exception $e) {
                echo 'ERROR: AdSearch #' . $order->id . ': ' . $e->getMessage() . "\n";
            }
        }

        if ($updatesCount) {
            $this->output('Currency Exchange Orders matches updated: ' . $updatesCount);
        }
    }


    public function actionClearMatches()
    {
        Yii::$app->db->createCommand()
            ->truncateTable('{{%currency_exchange_order_match}}')
            ->execute();

        Yii::$app->db->createCommand()
            ->update(CurrencyExchangeOrder::tableName(), [
                'processed_at' => null,
            ])
            ->execute();
    }
}

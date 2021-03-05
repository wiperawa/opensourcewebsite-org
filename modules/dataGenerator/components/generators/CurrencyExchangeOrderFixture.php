<?php

namespace app\modules\dataGenerator\components\generators;

use app\components\helpers\ArrayHelper;
use app\models\Currency;
use app\models\CurrencyExchangeOrder;
use app\models\PaymentMethod;
use app\models\User;
use Yii;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\Console;

class CurrencyExchangeOrderFixture extends ARGenerator
{
    /**
     * @throws ARGeneratorException
     */
    public function init()
    {
        if (!Currency::find()->exists()) {
            throw new ARGeneratorException('Impossible to create Exchange Order - there are no Currency in DB!');
        }
        parent::init();
    }

    protected function factoryModel(): ?ActiveRecord
    {
        $user = $this->findUser();

        $currencies = $this->getRandCurrenciesPair();

        if (!$user || !$currencies) {
            return null;
        }

        $londonCenter = [51.509865, -0.118092];

        [$orderLat, $orderLon] = $this->generateRandomPoint($londonCenter, 100);

        $crossRateOn = (int)static::getFaker()->boolean();

        $sellingCashOn = (int)static::getFaker()->boolean();
        $buyingCashOn = (int)static::getFaker()->boolean();

        $cashPaymentMethodId = $this->getCashPaymentMethodId();
        $paymentMethodsIds = $this->getPaymentMethodsIds();

        if (!$cashPaymentMethodId || !$paymentMethodsIds) {
            $class = self::classNameModel();
            $message = "\n$class: creation skipped. There is no Cash Payment method or no Payment Methods at all, yet.\n";
            $message .= "It's not error - few iterations later new ExchangeOrder will be generated.\n";
            Yii::$app->controller->stdout($message, Console::BG_GREY);
            return null;
        }

        $sellPaymentMethodsIds = static::getFaker()->randomElements(
            $paymentMethodsIds,
            static::getFaker()->numberBetween(1, count($paymentMethodsIds))
        );
        if ($sellingCashOn) {
            $sellPaymentMethodsIds[] = $cashPaymentMethodId;
        }

        $buyPaymentMethodsIds = static::getFaker()->randomElements(
            $paymentMethodsIds,
            static::getFaker()->numberBetween(1, count($paymentMethodsIds))
        );
        if ($buyingCashOn) {
            $buyPaymentMethodsIds[] = $cashPaymentMethodId;
        }


        return new CurrencyExchangeOrder([
            'selling_currency_id' => $currencies[0],
            'buying_currency_id' => $currencies[1],
            'user_id' => $user->id,
            'selling_rate' => $crossRateOn ? null :
                static::getFaker()->valid(static function ($v) {
                    return (bool)$v;
                })->randomFloat(1, 0.01, 10),
            'selling_currency_min_amount' => $min_amount = static::getFaker()->randomNumber(2),
            'selling_currency_max_amount' => $min_amount + static::getFaker()->randomNumber(2),
            'status' => CurrencyExchangeOrder::STATUS_ON,
            'delivery_radius' => static::getFaker()->numberBetween(1, 50),
            'location_lat' => $orderLat,
            'location_lon' => $orderLon,
            'selling_cash_on' => $sellingCashOn,
            'buying_cash_on' => $buyingCashOn,
            'cross_rate_on' => $crossRateOn,
            'updateBuyingPaymentMethods' => $buyPaymentMethodsIds,
            'updateSellingPaymentMethods' => $sellPaymentMethodsIds,
        ]);
    }

    private function getCashPaymentMethodId(): string
    {
        return PaymentMethod::find()->where(['type' => PaymentMethod::TYPE_CASH])->select('id')->scalar();
    }

    /**
     * @return int[] array
     */
    private function getPaymentMethodsIds(): array
    {
        return  PaymentMethod::find()
            ->where(['!=', 'type', PaymentMethod::TYPE_CASH])
            ->select('id')
            ->limit(8)
            ->asArray()
            ->all();
    }

    /**
     * @return Currency[]
     */
    private function getRandCurrenciesPair(): array
    {
        $currenciesPairIds = Currency::find()
            ->select('id')
            ->where(['in', 'code', ['USD', 'EUR', 'RUB', 'ALL']])
            ->orderByRandAlt(2)
            ->asArray()
            ->all();

        if (!$currenciesPairIds || count($currenciesPairIds) !== 2) {
            $class = self::classNameModel();
            $message = "\n$class: creation skipped. There is no Currencies yet.\n";
            $message .= "It's not error - few iterations later new ExchangeOrder will be generated.\n";
            Yii::$app->controller->stdout($message, Console::BG_GREY);
            return [];
        }
        return [$currenciesPairIds[0]['id'], $currenciesPairIds[1]['id']];
    }

    private function findUser(): ?User
    {
        $user = User::find()
            ->orderByRandAlt(1)
            ->one();

        if (!$user) {
            $class = self::classNameModel();
            $message = "\n$class: creation skipped. There is no Users\n";
            $message .= "It's not error - few iterations later new ExchangeOrder will be generated.\n";
            Yii::$app->controller->stdout($message, Console::BG_GREY);
        }
        return $user;
    }

    private function generateRandomPoint($centre, $radius)
    {
        $radius_earth = 3959; //miles

        //Pick random distance within $distance;
        $distance = lcg_value() * $radius;

        //Convert degrees to radians.
        $centre_rads = array_map('deg2rad', $centre);

        //First suppose our point is the north pole.
        //Find a random point $distance miles away
        $lat_rads = (pi() / 2) - $distance / $radius_earth;
        $lng_rads = lcg_value() * 2 * pi();


        //($lat_rads,$lng_rads) is a point on the circle which is
        //$distance miles from the north pole. Convert to Cartesian
        $x1 = cos($lat_rads) * sin($lng_rads);
        $y1 = cos($lat_rads) * cos($lng_rads);
        $z1 = sin($lat_rads);


        //Rotate that sphere so that the north pole is now at $centre.

        //Rotate in x axis by $rot = (pi()/2) - $centre_rads[0];
        $rot = (pi() / 2) - $centre_rads[0];
        $x2 = $x1;
        $y2 = $y1 * cos($rot) + $z1 * sin($rot);
        $z2 = -$y1 * sin($rot) + $z1 * cos($rot);

        //Rotate in z axis by $rot = $centre_rads[1]
        $rot = $centre_rads[1];
        $x3 = $x2 * cos($rot) + $y2 * sin($rot);
        $y3 = -$x2 * sin($rot) + $y2 * cos($rot);
        $z3 = $z2;


        //Finally convert this point to polar co-ords
        $lng_rads = atan2($x3, $y3);
        $lat_rads = asin($z3);

        return array_map('rad2deg', array($lat_rads, $lng_rads));
    }

}

<?php

declare(strict_types=1);

namespace Api\Module;

use \Api\Engine\Classes\Module;

class moduleOrder extends Module {


    # определяем пенсионер или нет с учетом пола
    private function isDiscountPensioner(): bool
    {
        $age = date('Y') - date('Y', strtotime($this->r['datebirth']));
        $ageDiscount = $this->r['gender'] == 'woman' ? PENSIONER_AGE_WOMAN : PENSIONER_AGE_MAN;
        return $age >= $ageDiscount ? true : false;
    }


    # определяем, если заказ сделан за неделю и более, скидка
    private function isDiscountEarlyOrder(): bool
    {
        $daysBeforeOrder = round(abs(strtotime(date('Y-m-d H:i:s')) - strtotime($this->r['datedelivery'])) / 86400, 1);
        return $daysBeforeOrder >= DISCOUNT_EARLY_ORDER_DAY ? true : false;
    }


    # считаем количество товаров
    private function isRow()
    {
        return count($this->r['product']??[]) ? true : false;
    }


    # сумму с учетом скидки
    protected function total(): void
    {

        if ($_SERVER["REQUEST_METHOD"] === 'POST') {

            if (
                !$this->isRow() ||
                !\dateFormatValid($this->r['datebirth']) ||
                !in_array($this->r['gender'], ['woman', 'man']) ||
                !\dateFormatValid($this->r['datedelivery'], 'Y-m-d H:i:s')
            ) {
                $this->http->responseCode(400);
            }

            # -- проверить получаемые параметры
            # httpResponseCode(400)

            $total = 0;

            foreach ($this->r['product'] as $row) {

                if (!is_numeric($row['price']??false) || !is_numeric($row['amount']??false)) {
                    $this->http->responseCode(400);
                }

                $row = $this->rowRuleFields($row);
                $total += $row['sum:total'];

                unset($row);
            }

            $this->result = [
                'total' => $total
            ];

            $this->http->responseCode(200);
            print json_encode($this->result);
            exit;

        }
        else {

            $this->http->responseCode(404);

        }

    }


    # манипуляции с полями данных продукта
    private function rowRuleFields(array $row = []): array
    {

        $row['sum'] = $row['amount'] * $row['price'];

        # -- 1. Скидка для пенсионеров 5 % (мужчины старше 63 лет включительно,
        # -- женщины - старше 58 включительно)

        $row['discount:pensioner'] = \percent(
                $row['sum'],
                $this->isDiscountPensioner() ? DISCOUNT_PENSIONER_PERCENT : 0
            );

        # после применеия скидки 1
        $row['sum:on:discount:pensioner'] = $row['sum'] - $row['discount:pensioner'];

        # -- 2. Скидка на ранний заказ - если заказ сделан за неделю и более, скидка
        # -- составит 4 %

        $row['discount:early:order'] = \percent(
                $row['sum:on:discount:pensioner'],
                $this->isDiscountEarlyOrder() ? DISCOUNT_EARLY_ORDER_DAY_PERCENT : 0
            );

        # после применеия скидки 2
        $row['sum:on:discount:early:order'] = $row['sum:on:discount:pensioner'] - $row['discount:early:order'];

        # -- 3. Скидка на количество товаров - если их больше 10 (не 10 разных видов
        # -- товаров, а 10 единиц, например если выбрано 10 единиц пиццы, то скидка уже дается), дается скидка 3 %.

        $row['discount:amount:product'] = \percent(
                $row['sum'],
                DISCOUNT_AMOUNT_PRODUCT >= $row['amount'] ? DISCOUNT_AMOUNT_PRODUCT_PERCENT : 0
            );

        # после применеия скидки 3
        $row['sum:on:discount:amount:product'] = $row['sum:on:discount:early:order'] - $row['discount:amount:product'];

        $row['sum:total'] = round($row['sum:on:discount:amount:product'], 2);

        return $row;
    }


}


?>
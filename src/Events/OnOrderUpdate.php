<?php
/**
 * Created by PhpStorm.
 * User: gibz
 * Date: 02.09.16
 * Time: 7:15
 */

namespace Warezgibzzz\Events;


class OnOrderUpdate
{
    /**
     * @param \Bitrix\Main\Event $params
     */
    function checkOrderStatus($params)
    {
        /**
         * @var \Bitrix\Sale\Order $orderEntity
         */
        $orderEntity = $params->getParameter('ENTITY');

        /**
         * if equals "F" - then finished
         *
         * @var string $statusId
         */
        $statusId = $orderEntity->getField('STATUS_ID');

        if ($statusId != 'F') {
            return;
        }

        $orderBaseCurrencyPrice = \CCurrencyRates::ConvertCurrency($orderEntity->getPrice(), $orderEntity->getCurrency(), \CCurrency::GetBaseCurrency());
        if ($orderBaseCurrencyPrice >= 5000) {
            if (!\CSaleUserAccount::GetByUserID($orderEntity->getUserId(), "BNS")) {
                $arFields = Array(
                    "USER_ID" => $orderEntity->getUserId(),
                    "CURRENCY" => "BNS",
                    "CURRENT_BUDGET" => 0
                );
                $saleAccountId = \CSaleUserAccount::Add($arFields);
            } else {
                $saleAccountId = \CSaleUserAccount::GetByUserID($orderEntity->getUserId(), 'BNS')['ID'];
            }
            \CSaleUserAccount::UpdateAccount($saleAccountId, ($orderBaseCurrencyPrice / 20), 'BNS', 'Bonus from paid and completed order', $orderEntity->getId());
        }
    }
}
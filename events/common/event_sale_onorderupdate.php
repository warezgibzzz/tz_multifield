<?php

/**
 * Created by PhpStorm.
 * User: gibz
 * Date: 01.09.16
 * Time: 18:48
 */
class OnSaleOrderUpdate
{
    function checkOrderStatus($id, $fields)
    {
        AddMessage2Log(print_r([$id, $fields], true));
    }

    function addBonusMoneyToUserAccount()
    {

    }

    /**
     * Dumper
     *
     * @param array $value
     * @param bool $toFile
     */
    private static function dump($value, $toFile = false)
    {
        if (!$toFile) {
            echo '<pre>';
            var_dump($_SERVER['DOCUMENT_ROOT'], $value);
            echo '</pre>';
            return;
        }

        $handle = fopen($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'dump.txt', 'a+');
        fwrite($handle, print_r($value, true));
        fclose($handle);
    }
}
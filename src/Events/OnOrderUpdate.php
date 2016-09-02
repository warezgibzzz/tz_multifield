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
    function checkOrderStatus($params) {
        AddMessage2Log(print_r($params, true));l
    }
}
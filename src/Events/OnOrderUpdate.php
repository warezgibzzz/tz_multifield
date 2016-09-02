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
    function checkOrderStatus($id, $params) {
        AddMessage2Log(print_r([$id, $params], true));
    }
}
<?php
/**
 * Socket Chat Cli App
 *
 * Author: flycorn
 * Email: ym1992it@163.com
 * Date: 2017/2/12
 * Time: 17:06
 */
//set_time_limit(0);
ob_implicit_flush();
date_default_timezone_set('PRC');

require_once('./flycorn/SocketChat.php');

new Flycorn\SocketChat('192.168.1.14', 8099);
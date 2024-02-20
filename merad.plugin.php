<?php
/**
 * Plugin Name: Merad
 * Plugin URI: https://github.com/drajathasan/slims-merad
 * Description: Plugin untuk me-"merad" (Pergi/Keluar/Pindah) kan data anda dari otomasi lain ke SLiMS
 * Version: 1.0.0
 * Author: Drajat Hasan
 * Author URI: https://t.me/drajathasan
 */
use SLiMS\Merad\Bootstrap;
use SLiMS\Merad\Commands\Kuli;
use SLiMS\Plugins;

require __DIR__ . '/vendor/autoload.php';

Bootstrap::setupDatabase()->withOrm();
Plugins::getInstance()->registerCommand(new Kuli);

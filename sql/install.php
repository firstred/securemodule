<?php
/**
 *            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
 *                  Version 2, December 2004
 *
 * Copyright (C) 2016 Michael Dekker <prestashop@michaeldekker.com>
 *
 * Everyone is permitted to copy and distribute verbatim or modified
 * copies of this license document, and changing it is allowed as long
 * as the name is changed.
 *
 *           DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
 * TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
 *
 *  @author    Michael Dekker <prestashop@michaeldekker.com>
 *  @copyright 2016 Michael Dekker
 *  @license   http://www.wtfpl.net/about/ Do What The Fuck You Want To Public License (WTFPL v2)
 */

if (!defined('_PS_VERSION_')) {
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');

    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');

    header('Location: ../');
}

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.bqSQL(JustAModel::$definition['table']).'` (
  `'.bqSQL(JustAModel::$definition['primary']).'` int(11) NOT NULL AUTO_INCREMENT,
  `active` varchar(12),
  PRIMARY KEY  (`'.bqSQL(JustAModel::$definition['primary']).'`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

for ($i = 0; $i < 10; $i++) {
    Db::getInstance()->insert(
        bqSQL(JustAModel::$definition['table']),
        array(
            array(
                'active' => rand(0, 1),
            ),
        )
    );
}


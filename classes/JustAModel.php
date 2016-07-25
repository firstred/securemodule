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

/**
 * Class JustAModel
 */
class JustAModel extends ObjectModel
{
    /** @var int $active */
    public $active;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'just_a_model',
        'primary' => 'id_just_a_model',
        'fields' => array(
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    /**
     * Get models
     *
     * @return array JustAModels
     */
    public static function getAll()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from(bqSQL(self::$definition['table']));

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
}

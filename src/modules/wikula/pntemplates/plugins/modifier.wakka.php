<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: modifier.wakka.php 107 2009-02-22 08:51:33Z mateo $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

function smarty_modifier_wakka($text, $method=null)
{
    return pnModAPIFunc('wikula', 'user', 'wakka',
                        array('text'   => $text,
                              'method' => $method));
}

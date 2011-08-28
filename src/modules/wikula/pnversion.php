<?php
/**
 * Wikula
 *
 * @copyright (c) Wikula Development Team
 * @link      http://code.zikula.org/wikula/
 * @version   $Id: pnversion.php 157 2010-04-20 21:35:31Z yokav $
 * @license   GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

$dom = ZLanguage::getModuleDomain('wikula');
$modversion['name']           = 'wikula';
$modversion['displayname']    = __('Wikula', $dom);
$modversion['oldnames']       = array('pnWikka');
$modversion['description']    = __('The Wikula module provides a wiki to your website.', $dom);
$modversion['url']            = __(/*!module name that appears in URL*/'wikula', $dom);
$modversion['version']        = '1.2';

$modversion['credits']        = 'pndocs/credits.txt';
$modversion['help']           = 'pndocs/install.txt';
$modversion['changelog']      = 'pndocs/changelog.txt';
$modversion['license']        = 'pndocs/license.txt';

$modversion['official']       = false;

$modversion['author']         = 'Frank Chestnut, Chris Hildebrandt, Florian Schießl, Mateo Tibaquirá, Gilles Pilloud';
$modversion['contact']        = 'http://code.zikula.org/wikula';

$modversion['securityschema'] = array('wikula::' => '::',
                                      'wikula::' => 'page::Page Tag');

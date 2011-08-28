<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: pnadmin.php 152 2010-04-20 14:16:09Z yokav $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

// Preload common stuff
Loader::requireOnce('modules/wikula/common.php');

function wikula_admin_main()
{
    $dom = ZLanguage::getModuleDomain('wikula');
    // Permission check
    if (!SecurityUtil::checkPermission('wikula::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
    }

    $pagecount = pnModAPIFunc('wikula', 'user', 'CountAllPages');
    $owners    = pnModAPIFunc('wikula', 'admin', 'GetOwners');

    $render = pnRender::getInstance('wikula', false);

    $render->assign('pagecount', $pagecount);
    $render->assign($owners);

    return $render->fetch('wikula_admin_main.tpl');
}

function wikula_admin_pages()
{
    $dom = ZLanguage::getModuleDomain('wikula');
    // Permission check
    if (!SecurityUtil::checkPermission('wikula::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
    }

    $q            = FormUtil::getPassedValue('q');
    $sort         = FormUtil::getPassedValue('sort');
    $order        = FormUtil::getPassedValue('order');
    $startnum     = FormUtil::getPassedValue('startnum');
    $itemsperpage = FormUtil::getPassedValue('itemsperpage');
    $modvars      = pnModGetVar('wikula');

    if (empty($itemsperpage) || !is_numeric($itemsperpage)) {
        $itemsperpage = $modvars['itemsperpage'];
    }
    if (empty($startnum) || !is_numeric($startnum)) {
        $startnum = 1;
    }

    $items = pnModAPIFunc('wikula', 'admin', 'getall', array('sort'     => $sort,
                                                              'order'    => $order,
                                                              'startnum' => $startnum,
                                                              'numitems' => $itemsperpage,
                                                              'q'        => $q));

    $total = pnModAPIFunc('wikula', 'user', 'CountAllPages');

    $render = pnRender::getInstance('wikula', false);

    $render->assign('sort',         $sort);
    $render->assign('order',        $order);
    $render->assign('itemcount',    count($items));
    $render->assign('total',        $total);
    $render->assign('items',        $items);
    $render->assign('modvars',      $modvars);
    $render->assign('startnum',     $startnum);
    $render->assign('itemsperpage', $itemsperpage);
    $render->assign('pageroptions', array(5, 10, 20, 30, 40, 50, 100, 200, 300, 400, 500));

    $render->assign('pager', array('numitems'     => $total,
                                   'itemsperpage' => $itemsperpage));

    return $render->fetch('wikula_admin_pageadmin.tpl', $order . $sort . $startnum);
}

function wikula_admin_modifyconfig()
{
    $dom = ZLanguage::getModuleDomain('wikula');
    // Permission check
    if (!SecurityUtil::checkPermission('wikula::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
    }

    $render = pnRender::getInstance('wikula', false);

    $modvars = pnModGetVar('wikula');

    $render->assign($modvars);

    return $render->fetch('wikula_admin_modifyconfig.tpl');
}

function wikula_admin_updateconfig()
{
    $dom = ZLanguage::getModuleDomain('wikula');
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerError(__("Invalid 'authkey':  this probably means that you pressed the 'Back' button, or that the page 'authkey' expired. Please refresh the page and try again.", $dom),
                                      null,
                                      pnModURL('wikula', 'admin', 'modifyconfig'));
    }

    $root_page    = FormUtil::getPassedValue('root_page');
    $savewarning  = FormUtil::getPassedValue('savewarning');
    $hidehistory  = FormUtil::getPassedValue('hidehistory');
    $itemsperpage = FormUtil::getPassedValue('itemsperpage');
    $hideeditbar  = FormUtil::getPassedValue('hideeditbar');
    $logreferers  = FormUtil::getPassedValue('logreferers');
    $excludefromhistory = FormUtil::getPassedValue('excludefromhistory');

    // Initialize the modvars array
    $modvars = array();

    $modvars['excludefromhistory'] = $excludefromhistory;

    if (empty($hideeditbar)) {
        $hideeditbar = false;
    }
    $modvars['hideeditbar'] = (bool)$hideeditbar;

    if (empty($savewarning)) {
        $savewarning = false;
    }
    $modvars['savewarning'] = (bool)$savewarning;

    if (empty($hidehistory)) {
        $hidehistory = false;
    }
    $modvars['hidehistory'] = (bool)$hidehistory;

    if (empty($logreferers)) {
        $logreferers = false;
    }
    $modvars['logreferers'] = (bool)$logreferers;

    if (empty($itemsperpage) || !is_numeric($itemsperpage) || (int)$itemsperpage < 1) {
        $itemsperpage = 25;
    }
    $modvars['itemsperpage'] = (int)$itemsperpage;

    if (!empty($root_page)) {
        $modvars['root_page'] = $root_page;
    }

    pnModSetVars('wikula', $modvars);

    $render = pnRender::getInstance('wikula');
    $render->clear_cache();

    LogUtil::registerStatus(__('Done! Module configuration updated.', $dom));

    pnModCallHooks('module', 'updateconfig', 'wikula', array('module' => 'wikula'));

    return pnRedirect(pnModURL('wikula', 'admin', 'modifyconfig'));
}

function wikula_admin_ClearReferrers()
{
    $dom = ZLanguage::getModuleDomain('wikula');
    // Permission check
    if (!SecurityUtil::checkPermission('wikula::', '::', ACCESS_ADMIN)) {
        return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
    }

    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerError(__("Invalid 'authkey':  this probably means that you pressed the 'Back' button, or that the page 'authkey' expired. Please refresh the page and try again.", $dom), null, pnModURL('wikula', 'admin', 'pages'));
    }

    $tag    = FormUtil::getPassedValue('tag');
    $global = FormUtil::getPassedValue('global');

    $result = pnModAPIFunc('wikula', 'admin', 'ClearReferrers',
                           array('tag'    => $tag,
                                 'global' => $global));

    return pnRedirect(pnModUrl('wikula', 'user', 'referrers', array('tag' => $tag)));
}

function wikula_admin_delete()
{
    $dom = ZLanguage::getModuleDomain('wikula');
    // Permission check
    if (!SecurityUtil::checkPermission('wikula::', '::', ACCESS_DELETE)) {
        return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
    }

    $tag    = FormUtil::getPassedValue('tag');
    $submit = FormUtil::getPassedValue('submit');

    if (!empty($submit)) {
        if (!SecurityUtil::confirmAuthKey()) {
            return LogUtil::registerError(__("Invalid 'authkey':  this probably means that you pressed the 'Back' button, or that the page 'authkey' expired. Please refresh the page and try again.", $dom), null, pnModURL('wikula', 'admin', 'pages'));
        }

        $revids = FormUtil::getPassedValue('revids');

        if (empty($revids)) {
            return LogUtil::registerArgsError(pnModURL('wikula', 'admin', 'pages'));
        }

        $ids = array_keys($revids);

        foreach($ids as $id) {
            $revisions[] = pnModAPIFunc('wikula', 'user', 'LoadPagebyId', array('id' => $id));
            //echo $page['tag'] . ' - '. $page['time'] . ' - '. $page['note'] . '<br />';
        }

    } else {

        $revisions = pnModAPIFunc('wikula', 'user', 'LoadRevisions', array('tag' => $tag));

    }

    $render = pnRender::getInstance('wikula', false);

    $modvars = pnModGetVar('wikula');

    $render->assign($modvars);
    $render->assign('tag',       $tag);
    $render->assign('revisions', $revisions);
    $render->assign('submit',    $submit);

    return $render->fetch('wikula_admin_deletepages.tpl');
}

function wikula_admin_confirmdeletepage()
{
    $dom = ZLanguage::getModuleDomain('wikula');
    if (!SecurityUtil::confirmAuthKey()) {
        return LogUtil::registerError(__("Invalid 'authkey':  this probably means that you pressed the 'Back' button, or that the page 'authkey' expired. Please refresh the page and try again.", $dom), null, pnModURL('wikula', 'admin', 'pages'));
    }

    $revids = FormUtil::getPassedValue('revids');
    $tag    = FormUtil::getPassedValue('tag');

    if (empty($revids) || empty($tag)) {
        return LogUtil::registerArgsError(pnModURL('wikula', 'admin', 'pages'));
    }

    $revisions = array_keys($revids);

    foreach ($revisions as $revision) {
        $action = pnModAPIFunc('wikula', 'admin', 'deletepageid', array('id' => $revision));
        if ($action === false) {
            return pnRedirect(pnModURL('wikula', 'admin', 'pages'));
        }
    }

    // Set the latest
    $pages = pnModAPIFunc('wikula', 'user', 'LoadRevisions', array('tag' => $tag));

    if ($pages) {
        $setlatest = pnModAPIFunc('wikula', 'admin', 'setlatest', array('pages' => $pages));
    }

    LogUtil::registerStatus('Pages deleted');

    return pnRedirect(pnModURL('wikula', 'admin', 'pages'));
/*
    echo '<pre>';
    print_r($revids);
    echo '</pre>';
    //exit;
*/
    //revisions to delete
/*
    echo '<pre>';
    print_r($revisions);
    echo '</pre>';
    //exit;

    echo 'Please confirm you want to delete these revisions (The most recent revision left will be set as "Latest")<br />';
    echo 'If there is no revision left, the page will be completly deleted.<br /><br />';
    foreach($revisions as $revision) {
        $page = pnModAPIFunc('wikula', 'user', 'LoadPagebyId', array('id' => $revision));
        echo $page['tag'] . ' - '. $page['time'] . ' - '. $page['note'] . '<br />';
    }

    exit;
*/
}

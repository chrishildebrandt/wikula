<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: pnuser.php 168 2010-04-22 15:16:18Z yokav $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

// Preload common stuff
Loader::requireOnce('modules/wikula/common.php');

/**
 * Main function
 * Displays a wiki page
 *
 * @param string $args['tag'] Tag of the wiki page to show
 * @TODO Improve the authors box grouping the same users contribs
 * @TODO Do not show the Last edit in the authorsbox if it's the same creation
 * @return unknown
 */
function wikula_user_main($args)
{
    $dom = ZLanguage::getModuleDomain('wikula');
    // Permission check
    if (!SecurityUtil::checkPermission('wikula::', '::', ACCESS_OVERVIEW)) {
        return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), 403);
    }

    // Get input parameters
    $tag  = isset($args['tag']) ? $args['tag'] : FormUtil::getPassedValue('tag');
    $time = isset($args['time']) ? $args['time'] : FormUtil::getPassedValue('time');
    $raw  = isset($args['raw']) ? $args['raw'] : FormUtil::getPassedValue('raw');
    unset($args);

    // Get module variables for later use
    $modvars = pnModGetVar('wikula');

    // Default values
    if (empty($tag)) {
        $tag = $modvars['root_page'];
    }
    if (empty($time)) {
        $time = null;
    }

    if ($modvars['logreferers']) {
        pnModAPIFunc('wikula', 'user', 'LogReferer', array('tag' => $tag));
    }

    // Get the page
    $page = pnModAPIFunc('wikula', 'user', 'LoadPage',
                         array('tag'  => $tag,
                               'time' => $time));

    // Validate invalid petition
    if (!$page && !empty($time)) {
        LogUtil::registerError(__("The page you requested doesn't exists", $dom), null, pnModURL('wikula'));
    }

    // Get the latest version
    if (empty($time)) {
        $latest = $page;
    } else {
        $latest = pnModAPIFunc('wikula', 'user', 'LoadPage', array('tag' => $tag));
    }

    // Check if this tag doesn't exists
    if (!$page && !$latest) {
        LogUtil::registerStatus(__('The page does not exist yet! do you want to create it?', $dom).'<br />'.__('Feel free to participate and be the first who creates content for this page!', $dom));
        pnRedirect(pnModURL('wikula', 'user', 'edit', array('tag' => $tag)));
    }

    $canedit = pnModAPIFunc('wikula', 'user', 'isAllowedToEdit', array('tag' => $tag));

    // Resetting session access and previous
    SessionUtil::delVar('wikula_access');
    SessionUtil::setVar('wikula_previous', $tag);

    $render = pnRender::getInstance('wikula');

    // TODO: check if this can be migrated to an action
    // we'll get later revisions too because we want to display the history and the last editors next to the page

    // assign the modvars
    $render->assign('modvars', $modvars);

    $render->assign('tag',      $tag);
    $render->assign('time',     $time);
    $render->assign('showpage', $page);
    $render->assign('canedit',  $canedit);

    return $render->fetch('wikula_user_show.tpl', md5($page['id'].$page['time']));
}

/**
 * Edit method
 */
function wikula_user_edit()
{
    $dom = ZLanguage::getModuleDomain('wikula');
    $id       = FormUtil::getPassedValue('id');
    $tag      = FormUtil::getPassedValue('tag');
    $newtag   = FormUtil::getPassedValue('newtag');
    $time     = FormUtil::getPassedValue('time');
    $note     = FormUtil::getPassedValue('note');
    $previous = FormUtil::getPassedValue('previous');

    if (!empty($newtag)) {
        return pnRedirect(pnModUrl('wikula', 'user', 'edit', array('tag' => $newtag)));
    }

    // Permission check
    if (!SecurityUtil::checkPermission('wikula::', 'page::'.$tag, ACCESS_COMMENT)) {
        return LogUtil::registerError(__('You do not have the authorization to edit this page!', $dom), null, pnModUrl('wikula', 'user', 'main', array('tag' => $tag)));
    }

    $latestid = pnModAPIFunc('wikula', 'user', 'PageExists', array('tag' => $tag));
    $submit   = FormUtil::getPassedValue('submit');

    // process the submit request
    if ($submit == __('Cancel', $dom)) {
        return pnRedirect(pnModUrl('wikula', 'user', 'main', array('tag' => $tag)));

    } elseif ($submit == __('Store', $dom) || $submit == __('Preview', $dom)) {
        $body = FormUtil::getPassedValue('body');
        // strip CRLF line endings down to LF to achieve consistency ... plus it saves database space.
        $body = str_replace("\r\n", "\n", $body);

    // or get the data of the requested page to edit
    } else {
        if (!empty($id)) {
            $page = pnModAPIFunc('wikula', 'user', 'LoadPagebyId',
                                 array('id'  => $id));

            // check that the id matches with the tag
            if (!$page || $page['tag'] != $tag) {
                return LogUtil::registerError(__('The revision ID does not exist for the requested page', $dom));
            }

        } else {
            $page = pnModAPIFunc('wikula', 'user', 'LoadPage',
                                 array('tag'  => $tag,
                                       'time' => $time));
            // If the page does not exist we want to open the edit form to allow the creation of a new page with the submitted tag
        }

        // update the previous value if it's an old revision
        //if (!empty($id) || !empty($time)) {
        // update the previous value if there's one
        if ($latestid) {
            $previous = $latestid;
        }

        // update the body if was retrieved
        $body = isset($page['body']) ? $page['body'] : '';
    }

    // only if saving
    if ($submit == __('Store', $dom)) {
        $valid = true;

        // check for overwriting
        if ($latestid && $latestid != $previous) {
            LogUtil::registerError(__('OVERWRITE ALERT: This page was modified by someone else while you were editing it.<br />Please copy your changes and re-edit this page.', $dom));
            $valid = false;
        }

        if ($valid) {
            // LinkTracking
            // Writing all wiki links that is on this page
            SessionUtil::setVar('linktracking', 1);
            SessionUtil::setVar('wikula_previous', $previous);

            $store = pnModAPIFunc('wikula', 'user', 'SavePage',
                                  array('tag'      => $tag,
                                        'body'     => $body,
                                        'note'     => $note,
                                        'tracking' => true));

            SessionUtil::setVar('linktracking', false);

            if ($store) {
                return pnRedirect(pnModUrl('wikula', 'user', 'main', array('tag' => $tag)));
            }
        }
    }

    $canedit = pnModAPIFunc('wikula', 'user', 'isAllowedToEdit', array('tag' => $tag));

    $hideeditbar = (int)pnModGetVar('wikula', 'hideeditbar');

    // build the output
    $render = pnRender::getInstance('wikula', false);

    $render->assign('hideeditbar',  $hideeditbar);
    $render->assign('previous',     $previous);
    $render->assign('note',         $note);
    $render->assign('canedit',      $canedit);
    $render->assign('submit',       $submit);
    $render->assign('tag',          $tag);
    $render->assign('body',         $body);

    return $render->fetch('wikula_user_edit.tpl');
}

/**
 * Show the history of the Wiki Page
 *
 * @param string $args['tag'] tag of the page
 * @TODO Implement the time parameter?
 * @TODO Add a paginator?
 * @TODO Improve this view with JavaScript sliders
 * @return unknown
 */
function wikula_user_history($args)
{
    $dom = ZLanguage::getModuleDomain('wikula');
    $tag  = FormUtil::getPassedValue('tag');
    //$time = FormUtil::getPassedValue('time');

    if (empty($tag)) {
        return LogUtil::registerError(__f('Missing argument [%s]', 'tag'),
                                      null,
                                      pnModUrl('wikula', 'user', 'main'));
    }

    $pages = pnModAPIFunc('wikula', 'user', 'LoadRevisions',
                          array('tag' => $tag));

    if (!$pages) {
        return LogUtil::registerError(__f('No %s found.', 'Rev'),
                                      null,
                                      pnModUrl('wikula', 'user', 'main'));
    }

    $objects  = array();
    $previous = array();
    foreach ($pages as $page) {

        if (empty($previous)) {
            // We filter the first one as we don't want to check it
            $previous = $page;
            continue;
        }

        $bodylast = explode("\n", $previous['body']);

        $bodynext = explode("\n", $page['body']);

        $added   = array_diff($bodylast, $bodynext);
        $deleted = array_diff($bodynext, $bodylast);

        if ($added) {
            $newcontent = implode("\n", $added)/*."\n"*/;
        } else {
            $newcontent = '';
            $added = false;
        }

        if ($deleted) {
            $oldcontent = implode("\n", $deleted)/*."\n"*/;
        } else {
            $oldcontent = '';
            $deleted = false;
        }

        $objects[] = array(
            'pageAtime'    => $previous['time'],
            'pageBtime'    => $page['time'],
            'pageAtimeurl' => urlencode($previous['time']),
            'pageBtimeurl' => urlencode($page['time']),
            'EditedByUser' => $previous['user'],
            'note'         => $previous['note'],
            'newcontent'   => $newcontent,
            'oldcontent'   => $oldcontent,
            'added'        => $added,
            'deleted'      => $deleted
        );

        $previous = $page;
    }

    $render = pnRender::getInstance('wikula', false);

    $render->assign('tag',     $tag);
    $render->assign('objects', $objects);
    $render->assign('oldest',  $page);

    return $render->fetch('wikula_user_history.tpl');
}

/**
 * XML output of the recent changes of the specified page
 *
 * @return xml maincontent for the RSS theme
 */
function wikula_user_RecentChangesXML()
{
    $dom = ZLanguage::getModuleDomain('wikula');
    if (!SecurityUtil::checkPermission('wikula::', 'xml::recentchanges', ACCESS_OVERVIEW)) {
        return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), null, pnModUrl('wikula', 'user', 'main'));
    }

    $pages = pnModAPIFunc('wikula', 'user', 'LoadRecentlyChanged');

    if (!$pages) {
        return LogUtil::registerError(__('Error during element fetching !', $dom));
    }

    $render = pnRender::getInstance('wikula', false);
    $render->force_compile = true;

    $render->assign('pages', $pages);

    return $render->fetch('wikula_xml_recentchanges.tpl');
}

/**
 * XML output of the revisions for the specified page
 *
 * @return xml maincontent for the RSS theme
 */
function wikula_user_RevisionsXML()
{
    $dom = ZLanguage::getModuleDomain('wikula');
    if (!SecurityUtil::checkPermission('wikula::', 'xml::revisions', ACCESS_OVERVIEW)) {
        return LogUtil::registerError(__('Sorry! No authorization to access this module.', $dom), null, pnModUrl('wikula', 'user', 'main'));
    }

    $tag = FormUtil::getPassedValue('tag');

    $pages = pnModAPIFunc('wikula', 'user', 'LoadRevisions', array('tag' => $tag));

    if (!$pages) {
        return LogUtil::registerError(__('Error during element fetching !', $dom));
    }

    $render = pnRender::getInstance('wikula', false);
    $render->force_compile = true;

    $render->assign('tag',   $tag);
    $render->assign('pages', $pages);

    return $render->fetch('wikula_xml_revisions.tpl');
}

function wikula_user_RecentChangesMindMap()
{

    $dom = ZLanguage::getModuleDomain('wikula');
    $cr = "\n";

    $xml  = '<map version="0.7.1">'.$cr
    .'  <node text="'.__('Recent Changes').'">'.$cr
    .'    <node text="Date" position="right">'.$cr;

    $pages = pnModAPIFunc('wikula', 'user', 'LoadRecentlyChanged', array());

    $users  = array();
    $curday = '';
    $max = pnModGetVar('wikula', 'itemsperpage');

    $c = 0;
    foreach ($pages as $page) {
        $c++;
        if (($c <= $max) || !$max) {
            $pageuser = $page['user'];
            $pagetag  = $page['tag'];

            // day header
            list($day, $time) = explode(' ', $page['time']);
            if ($day != $curday) {
                $dateformatted = date(__('D, d M Y', $dom), strtotime($day));
                if ($curday) {
                    $xml .= '      </node>'.$cr;
                }
                $xml .= '      <node text="'.$dateformatted.'">'.$cr;
                $curday = $day;
            }

            $pagelink      = pnModUrl('wikula', 'user', 'main',    array('tag' => DataUtil::formatForDisplay($pagetag)));
            $revlink       = pnModUrl('wikula', 'user', 'History', array('tag' => DataUtil::formatForDisplay($pagetag)));
            $xml          .= '        <node text="'.$pagetag.'" folded="true">'.$cr;
            $timeformatted = date('H:i T', strtotime($page['time']));
            $xml          .= '          <node link="'.$pagelink.'" text="'.__('Revision time: ').$timeformatted.'"/>'.$cr;
            if ($page['note']) {
                $xml .= '          <node text="'.$pageuser.': '.DataUtil::formatForDisplay($page['note']).'"/>'.$cr;
            } else {
                $xml .= '          <node text="'.__('Author: ').$pageuser.'"/>'.$cr;
            }

            $xml .= '          <node link="'.$revlink.'" text="'.__('View History').'"/>'.$cr.'</node>'.$cr;

            if (is_array($users[$pageuser])) {
                $u_count = count($users[$pageuser]);
                $users[$pageuser][$u_count] = $pagetag;
            } else {
                $users[$pageuser][0] = $pageuser;
                $users[$pageuser][1] = $pagetag;
            }
        }
    }

    $xml .= '    </node>'.$cr.'  </node>'.$cr
    .'  <node text="'.__('Author').'" position="left">'.$cr;
    foreach ($users as $user) {
        $start_loop = true;
        foreach ($user as $user_page) {
            if (!$start_loop) {
                $xml .= '    <node link="'.pnModUrl('wikula','user','main', array('tag' => DataUtil::formatForDisplay($user_page))).'" text="'.$user_page.'"/>'.$cr;
            } else {
                $xml .= '    <node text="'.$user_page.'">'.$cr;
                $start_loop = false;
            }
        }
        $xml .= '  </node>'.$cr;
    }

    $xml .= '</node>'.$cr.'</node>'.$cr.'</map>'.$cr;

    echo $xml;

    return true;
}

/**
 * Display a list of internal pages linking to the current page
 */
function wikula_user_backlinks()
{
    $dom = ZLanguage::getModuleDomain('wikula');
    $linkedtag = FormUtil::getPassedValue('tag');

    if (empty($linkedtag)) {
        return LogUtil::registerError(__f('Missing argument [%s]', 'tag'), null, pnModUrl('wikula', 'user', 'main'));
    }

    // Get the variables
    $page  = pnModAPIFunc('wikula', 'user', 'LoadPage', array('tag' => $linkedtag));
    $pages = pnModAPIFunc('wikula', 'user', 'LoadPagesLinkingTo', array('tag' => $linkedtag));

    $render = pnRender::getInstance('wikula', false);

    $render->assign('tag',       $linkedtag);
    $render->assign('backpage',  $page);
    $render->assign('pages',     $pages);

    return $render->fetch('wikula_user_backlinks.tpl');
}

/**
 * Clone the current page and save a copy of it as a new page
 */
function wikula_user_clone()
{
    $dom = ZLanguage::getModuleDomain('wikula');
    $tag = FormUtil::getPassedValue('tag');

    if (empty($tag)) {
        return LogUtil::registerError(__f('Missing argument [%s]', 'tag'), null, pnModUrl('wikula', 'user', 'main'));
    }

    // Permission check
    if (!SecurityUtil::checkPermission('wikula::', 'page::'.$tag, ACCESS_COMMENT)) {
        return LogUtil::registerError(__('You do not have the authorization to edit this page!', $dom), null, pnModUrl('wikula', 'user', 'main'));
    }

    if (!pnModAPIFunc('wikula', 'user', 'PageExists', array('tag' => $tag))) {
        return LogUtil::registerError(__("The page you requested doesn't exists", $dom), null, pnModUrl('wikula', 'user', 'main'));
    }

    // Default values
    $to   = $tag;
    $note = __f('Cloned from %s', $tag);
    $edit = false;

    $submit = FormUtil::getPassedValue('submit');

    if ($submit == __('Cancel', $dom)) {
        pnRedirect(pnModUrl('wikula', 'user', 'main', array('tag' => $tag)));

    } elseif ($submit == __('Submit', $dom)) {
        $to   = FormUtil::getPassedValue('to');
        $note = FormUtil::getPassedValue('note');
        $edit = (bool)FormUtil::getPassedValue('edit');

        // Validate the choosen pagename
        $validationerror = false;
        if (!pnModAPIFunc('wikula', 'user', 'isValidPagename', array('tag' => $to))) {
            LogUtil::registerError(__('That page name is not valid', $dom));
            $validationerror = true;
        }

        // check if the page already exists
        if (!$validationerror && pnModAPIFunc('wikula', 'user', 'PageExists', array('tag' => $to))) {
            return LogUtil::registerError(__('This page does already exist', $dom), null, pnModUrl('wikula', 'user', 'main'));
        }

        // check if has access to create it
        if (!$validationerror && !SecurityUtil::checkPermission('wikula::', 'page::'.$tag, ACCESS_COMMENT)) {
            return LogUtil::registerError(__('You do not have the authorization to edit this page!', $dom), null, pnModUrl('wikula', 'user', 'main'));
        }

        // if valid request
        if (!$validationerror) {
            // proceed to page cloning
            $page = pnModAPIFunc('wikula', 'user', 'LoadPage', array('tag' => $tag));
            $newpage = array(
                'tag'  => $to,
                'body' => $page['body'],
                'note' => $note
            );

            if (pnModAPIFunc('wikula', 'user', 'SavePage', $newpage)) {
                // redirect
                if ($edit) {
                    pnRedirect(pnModURL('wikula', 'user', 'edit', array('tag' => $to)));
                } else {
                    LogUtil::registerStatus(__('Clone created successfully', $dom));
                    pnRedirect(pnModURL('wikula', 'user', 'main', array('tag' => $to)));
                }
            }
        }
    }

    $render = pnRender::getInstance('wikula', false);

    $render->assign('tag',  $tag);
    $render->assign('to',   $to);
    $render->assign('note', $note);
    $render->assign('edit', $edit);

    return $render->fetch('wikula_user_clone.tpl');
}

/**
 * Display the Referrers to a page
 */
function wikula_user_Referrers()
{
    $dom = ZLanguage::getModuleDomain('wikula');
    if (!pnUserLoggedIn()) {
        return LogUtil::registerError(__('You must be logged in to be able to view referrers - Anti botspam', $dom), null, pnModUrl('wikula', 'user', 'main'));
    }

    $tag    = FormUtil::getPassedValue('tag');
    $global = FormUtil::getPassedValue('global');
    $sites  = FormUtil::getPassedValue('sites');
    $q      = FormUtil::getPassedValue('q');
    $qo     = FormUtil::getPassedValue('qo');
    $h      = FormUtil::getPassedValue('h');
    $ho     = FormUtil::getPassedValue('ho');
    $days   = FormUtil::getPassedValue('days');
    $submit = FormUtil::getPassedValue('submit');

    if (empty($sites) || !is_numeric($sites)) {
        $sites = 0;
    }
    if (empty($global) || !is_numeric($global)) {
        $global = 0;
    }

    if (empty($h) || !is_numeric($h)) {
        $h = 1;
    }
    if (!empty($ho) || !is_numeric($ho)) {
        $ho = 1;
    }
    if (!empty($days) || !is_numeric($days)) {
        $days = 1;
    }

    $modvars = pnModGetVar('wikula');

    if (empty($tag)) {
        $tag = $modvars['root_page'];
    }
    if (!empty($submit)) {
        $submit = true;
    }

    $referrers = pnModAPIFunc('wikula', 'user', 'LoadReferrers',
                              array('tag'    => $tag,
                                    'global' => $global,
                                    'sites'  => $sites,
                                    'q'      => $q,
                                    'qo'     => $qo,
                                    'h'      => $h,
                                    'ho'     => $ho,
                                    'days'   => $days,
                                    'submit' => $submit));

    if (!$referrers) {
        //return 'No Referrers';
    }

    // load the wiki page for output purposes
    $page = pnModAPIFunc('wikula', 'user', 'LoadPage',
                         array('tag' => $tag));

    $render = pnRender::getInstance('wikula', false);

    $render->assign('tag',       $tag);
    $render->assign('page',      $page);
    $render->assign('q',         $q);
    $render->assign('h',         $h);
    $render->assign('sites',     $sites);
    $render->assign('global',    $global);
    $render->assign('referrers', $referrers);
    $render->assign('total',     count($referrers));

    return $render->fetch('wikula_user_referrers.tpl');
}
function wikula_user_grabcode()
{
    $code    = FormUtil::getPassedValue('code');
    return nl2br(htmlentities(br2nl(urldecode($code))));

}

function br2nl($string){
  $return=preg_replace('`<br[[:space:]]*/?'.'[[:space:]]*>`',chr(13),$string);
  return $return;
} 

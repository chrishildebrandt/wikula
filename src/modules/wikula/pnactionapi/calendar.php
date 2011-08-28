<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: calendar.php 107 2009-02-22 08:51:33Z mateo $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Credits
 * This action was inspired mainly by the "Calendar Menu" code written by
 * Marcus Kazmierczak (http://www.blazonry.com/about.php)
 * (c) 1998-2002 Astonish Inc.) which we traced back as being the ultimate origin of this code
 * although our starting point was actually a (probably second-hand) variant found on the web which
 * did not contain any attribution.
 * However, not much of the original code is left in this version. Nevertheless, credit to
 * Marcus Kazmierczak for the original that inspired this, however indirectly: Thanks!
 */

/**
 * Display a calendar face for a specified or the current month
 * 
 * @author Frank Chestnut
 * @author JavaWoman
 * @author GmBowen
 * 
 * @param integer  $year   optional: 4-digit year of the month to be displayed;
 *                         default: current year
 *                         the default can be overridden by providing a URL parameter 'year'
 * @param integer  $month  optional: number of month (1 or 2 digits) to be displayed;
 *                         default: current month
 *                         the default can be overridden by providing a URL parameter 'month'
 * @return data table for specified or current month
 * 
 * @todo        - take care we don't go over date limits for PHP with navigation links
 *              - configurable first day of week
 *              - clean the poluted Render assignments into only one array
 */
function wikula_actionapi_calendar($vars = array())
{
    // ***** CONSTANTS section *****
    // TODO: Review this and move some defines elsewhere
    define('MIN_DATETIME', strtotime('1970-01-01 00:00:00 GMT')); // earliest timestamp PHP can handle (Windows and some others - to be safe)
    define('MAX_DATETIME', strtotime('2038-01-19 03:04:07 GMT')); // latest timestamp PHP can handle
    define('MIN_YEAR', date('Y',MIN_DATETIME));
    define('MAX_YEAR', date('Y',MAX_DATETIME)-1);                 // don't include partial January 2038

    // not-quite-constants
    $daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    define('CUR_YEAR', date('Y',mktime()));
    define('CUR_MONTH', date('n',mktime()));
    // format string for locale-specific month (%B) + 4-digit year (%Y) used for caption and title attributes
    // NOTE: monthname is locale-specific but order of month and year may need to be switched: hence the double quotes!
    /*
    define('LOC_MON_YEAR', "%B %Y");                                                                    // i18n
    define('FMT_SUMMARY', "Calendar for %s");                                                           // i18n
    define('TODAY', "today");                                                                           // i18n
    */
    // ***** END CONSTANTS section *****

    // ***** (ACTION) PARAMETERS Interface *****
    // set parameter defaults: current year and month
    $year  = CUR_YEAR;
    $month = CUR_MONTH;
    $tag   = FormUtil::getPassedValue('tag', pnModGetVar('wikula', 'root_page'));

    // Get and interpret parameters

    // 1) overrride defaults with parameters provided in URL (accept only valid values)
    $uYear  = FormUtil::getPassedValue('year');
    $uMonth = FormUtil::getPassedValue('month');
    if (!empty($uYear) && is_numeric($uYear)) {
        if ($uYear >= MIN_YEAR && $uYear <= MAX_YEAR) {
            $year = $uYear;
        }
    }
    if (!empty($uMonth) && is_numeric($uMonth)) {
        if ($uMonth >= 1 && $uMonth <= 12) {
            $month = $uMonth;
        }
    }

    // 2) override with parameters provided in action itself (accept only valid values)
    $hasActionParams = FALSE;
    if (is_array($vars)) {
        foreach ($vars as $param => $value) {
            switch ($param) {
                case 'year':
                    $uYear = (int)trim($value);
                    if ($uYear >= MIN_YEAR && $uYear <= MAX_YEAR) {
                        $year = $uYear;
                        $hasActionParams = TRUE;
                    }
                    break;
                case 'month':
                    $uMonth = (int)trim($value);
                    if ($uMonth >= 1 && $uMonth <= 12) {
                        $month = $uMonth;
                        $hasActionParams = TRUE;
                    }
                    break;
            }
        }
    }
    // ***** (ACTION) PARAMETERS Interface *****

    // ***** DERIVED VARIABLES *****
    // derive which weekday the first is on
    $datemonthfirst = sprintf('%4d-%02d-%02d',$year,$month,1);
    $firstwday      = strftime('%w',strtotime($datemonthfirst));                                             // i18n

    // derive (locale-specific) caption text
    $monthYear  = strftime(LOC_MON_YEAR,strtotime($datemonthfirst));                                    // i18n
    $summary    = sprintf(FMT_SUMMARY, $monthYear);                                                     // i18n

    // derive last day of month
    $lastmday = $daysInMonth[$month - 1];
    // correct for leap year if necessary
    if (2 == $month) {
        if (1 == date('L', strtotime(sprintf('%4d-%02d-%02d', $year, 1, 1)))) {
            $lastmday++;
        }
    }

    // derive "today" to detect when to mark this up in the calendar face
    $today = date('Y:m:d', mktime());
    //$wikkatoday = date('j', mktime());

    // build navigation variables - locale-specific (%B gets full month name)
    // FIXME: @@@ take care we don't go over date limits for PHP
    if (!$hasActionParams) {
        // previous month
        $monthPrev  = ($month-1 < 1) ? 12 : $month-1;
        $yearPrev   = ($month-1 < 1) ? $year-1 : $year;
        $parPrev    = 'month='.$monthPrev.'&amp;year='.$yearPrev;
        //$urlPrev    = $this->Href('', '', $parPrev);
        $urlPrev    = pnModUrl('wikula', 'user', 'main', array('tag' => $tag, 'month' => $monthPrev, 'year' => $yearPrev));
        $titlePrev  = strftime(LOC_MON_YEAR, strtotime(sprintf('%4d-%02d-%02d', $yearPrev, $monthPrev,1)));// i18n
        // current month
        $parCur     = 'month='.CUR_MONTH.'&amp;year='.CUR_YEAR;
        //$urlCur     = $this->Href('', '', $parCur);
        $urlCur     = pnModUrl('wikula', 'user', 'main', array('tag' => $tag, 'month' => CUR_MONTH, 'year' => CUR_YEAR));
        $titleCur   = strftime(LOC_MON_YEAR, strtotime(sprintf('%4d-%02d-%02d', CUR_YEAR, CUR_MONTH,1)));  // i18n
        // next month
        $monthNext  = ($month+1 > 12) ? 1 : $month+1;
        $yearNext   = ($month+1 > 12) ? $year+1 : $year;
        $parNext    = 'month='.$monthNext.'&amp;year='.$yearNext;
        //$urlNext    = $this->Href('', '', $parNext);
        $urlNext    = pnModUrl('wikula', 'user', 'main', array('tag' => $tag, 'month' => $monthNext, 'year' => $yearNext));
        $titleNext  = strftime(LOC_MON_YEAR,strtotime(sprintf('%4d-%02d-%02d', $yearNext, $monthNext,1)));// i18n
    }

    // build array with names of weekdays (locale-specific)
    $tmpTime    = strtotime('this Sunday');         // get a starting date that is a Sunday
    $tmpDate    = date('d',$tmpTime);
    $tmpMonth   = date('m',$tmpTime);
    $tmpYear    = date('Y',$tmpTime);
    for ($i=0; $i<=6; $i++) {
        $aWeekdaysShort[$i] = strftime('%a',mktime(0,0,0,$tmpMonth,$tmpDate+$i,$tmpYear));
        $aWeekdaysLong[$i]  = strftime('%A',mktime(0,0,0,$tmpMonth,$tmpDate+$i,$tmpYear));
    }
    // ***** END DERIVED VARIABLES *****

    // ***** OUTPUT SECTION *****

    // Cache disabled until find the right cache id
    $render = pnRender::getInstance('wikula', false);

    $render->assign('summary', $summary);
    $render->assign('monthYear', $monthYear);
    $render->assign('aWeekdaysShort', $aWeekdaysShort);
    $render->assign('aWeekdaysLong', $aWeekdaysLong);
    $render->assign('firstwday', $firstwday);
    $render->assign('monthPrev', $monthPrev);
    $render->assign('yearPrev', $yearPrev);
    $render->assign('parPrev', $parPrev);
    $render->assign('urlPrev', $urlPrev);
    $render->assign('titlePrev', $titlePrev);
    $render->assign('parCur', $parCur);
    $render->assign('urlCur', $urlCur);
    $render->assign('titleCur', $titleCur);
    $render->assign('monthNext', $monthNext);
    $render->assign('yearNext', $yearNext);
    $render->assign('parNext', $parNext);
    $render->assign('urlNext', $urlNext);
    $render->assign('titleNext', $titleNext);
    $emptyfcells = array();
    for ($i = 1; $i <= $firstwday; $i++) {
        $emptyfcells[] = '&nbsp;';
    }

    $render->assign('emptyfcells', $emptyfcells);

    // loop through all the days of the month
    $day  = 1;
    $wday = $firstwday;
    $monthcontent = array();
    while ($day <= $lastmday) {
        // start week row
        if ($wday == 0) {
            $monthcontent[] = 'start';
            //echo "  <tr>\n";
        }
        // handle markup for current day or any other day
        $calday = sprintf('%4d:%02d:%02d', $year, $month, $day);
        if ($calday == $today) {
            $wikkatoday = $day;
            $monthcontent[] = $day; //$calday;
            //echo '      <td title="'._TODAY.'" class="currentday">'.$day."</td>\n";
        } else {
            $monthcontent[] = $day;
            //echo '      <td>'.$day."</td>\n";
        }
        // end week row
        if ($wday == 6) {
            $monthcontent[] = 'end';
            //echo "  </tr>\n";
        }
        // next day
        $wday = ++$wday % 7;
        $day++;

    }

    $render->assign('today', $today);
    $render->assign('wikkatoday', $wikkatoday);
    $render->assign('calday', $calday);
    $render->assign('monthcontent', $monthcontent);

    // fill week with blank cells after end of month
    $emptylcells = array();
    if ($wday > 0) {
        for ($i=$wday; $i<=6; $i++) {
            //echo '      <td>&nbsp;</td>'."\n";
            $emptylcells[] = '&nbsp;';
        }
    }
    $render->assign('emptylcells', $emptylcells);
    $render->assign('wday', $wday);

    $render->assign('hasActionParams', $hasActionParams);

    return $render->fetch('wikula_action_calendar.tpl');
}

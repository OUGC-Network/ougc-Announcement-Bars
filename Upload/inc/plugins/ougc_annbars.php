<?php

/***************************************************************************
 *
 *    OUGC Announcement Bars plugin (/inc/plugins/ougc_annbars.php)
 *    Author: Omar Gonzalez
 *    Copyright: © 2012 - 2020 Omar Gonzalez
 *
 *    Website: https://ougc.network
 *
 *    Manage custom announcement notifications that render to users in the page.
 *
 ***************************************************************************
 ****************************************************************************
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 ****************************************************************************/

declare(strict_types=1);

use function ougc\AnnouncementBars\Admin\pluginActivation;
use function ougc\AnnouncementBars\Admin\pluginInformation;
use function ougc\AnnouncementBars\Admin\pluginIsInstalled;
use function ougc\AnnouncementBars\Admin\pluginUninstallation;
use function ougc\AnnouncementBars\Core\addHooks;
use function ougc\AnnouncementBars\Core\cacheUpdate;
use function ougc\AnnouncementBars\Core\languageLoad;

use const ougc\AnnouncementBars\ROOT;

defined('IN_MYBB') || die('This file cannot be accessed directly.');

// You can uncomment the lines below to avoid storing some settings in the DB
define('ougc\AnnouncementBars\SETTINGS', [
    //'key' => '',
]);

define('ougc\AnnouncementBars\DEBUG', false);

define('ougc\AnnouncementBars\ROOT', MYBB_ROOT . 'inc/plugins/ougc/AnnouncementBars');

require_once ROOT . '/core.php';

defined('PLUGINLIBRARY') || define('PLUGINLIBRARY', MYBB_ROOT . 'inc/plugins/pluginlibrary.php');

if (defined('IN_ADMINCP')) {
    require_once ROOT . '/admin.php';
    require_once ROOT . '/hooks/admin.php';

    addHooks('ougc\AnnouncementBars\Hooks\Admin');
} else {
    require_once ROOT . '/hooks/forum.php';

    addHooks('ougc\AnnouncementBars\Hooks\Forum');
}

function ougc_annbars_info(): array
{
    return pluginInformation();
}

function ougc_annbars_activate(): void
{
    pluginActivation();
}

function ougc_annbars_is_installed(): bool
{
    return pluginIsInstalled();
}

function ougc_annbars_uninstall(): void
{
    pluginUninstallation();
}

function update_ougc_annbars(): void
{
    cacheUpdate();
}

class OUGC_ANNBARS
{
    // Define our ACP url
    public $url = 'index.php?module=config-plugins';

    // Plugin Message
    public $message = '';

    // Cache
    public $cache = [
        'fromcache' => [],
        'fromdb' => [],
    ];

    // AID which has just been updated/inserted/deleted
    public $aid = 0;

    // Allowed styles
    public $styles = ['black', 'white', 'red', 'green', 'blue', 'brown', 'pink', 'orange'];

    // Set url
    public function set_url($url)
    {
        if (($url = trim($url))) {
            $this->url = $url;
        }
    }

    // Redirect admin help function
    public function admin_redirect($message = '', $error = false)
    {
        if ($message) {
            flash_message($message, ($error ? 'error' : 'success'));
        }

        admin_redirect($this->build_url());
        exit;
    }

    // Build an url parameter
    public function build_url($urlappend = [], $fetch_input_url = false)
    {
        global $PL;

        if (!is_object($PL)) {
            return $this->url;
        }

        if ($fetch_input_url === false) {
            if ($urlappend && !is_array($urlappend)) {
                $urlappend = explode('=', $urlappend);
                $urlappend = [$urlappend[0] => $urlappend[1]];
            }
        } else {
            $urlappend = $this->fetch_input_url($fetch_input_url);
        }

        return $PL->url_append($this->url, $urlappend, '&amp;', true);
    }

    // Update the bars cache
    public function update_cache()
    {
        global $db, $cache;

        $query = $db->simple_select('ougc_annbars', '*', 'startdate>\'' . TIME_NOW . '\'');

        $sqlnotin = [0];

        while ($aid = (int)$db->fetch_field($query, 'aid')) {
            $sqlnotin[] = $aid;
        }

        $query = $db->simple_select(
            'ougc_annbars',
            '*',
            'enddate>=\'' . TIME_NOW . '\' AND aid NOT IN (\'' . implode('\',\'', $sqlnotin) . '\')',
            ['order_by' => 'disporder']
        );

        $update = [];

        while ($annbar = $db->fetch_array($query)) {
            $aid = (int)$annbar['aid'];
            unset($annbar['aid'], $annbar['name']);
            $update[$aid] = $annbar;
        }

        $db->free_result($query);

        $cache->update('ougc_annbars', $update);

        return true;
    }

    // Fetch current url inputs, for multipage mostly
    public function fetch_input_url($ignore = false)
    {
        $location = parse_url(get_current_location());
        while (my_strpos($location['query'], '&amp;')) {
            $location['query'] = html_entity_decode($location['query']);
        }
        $location = explode('&', $location['query']);

        if ($ignore !== false) {
            if (!is_array($ignore)) {
                $ignore = [$ignore];
            }
            foreach ($location as $key => $input) {
                $input = explode('=', $input);
                if (in_array($input[0], $ignore)) {
                    unset($location[$key]);
                }
            }
        }

        $url = [];
        foreach ($location as $input) {
            $input = explode('=', $input);
            $url[$input[0]] = $input[1];
        }

        return $url;
    }

    // Get bar from DB or cache
    public function get_bar($aid = 0, $cache = false)
    {
        if ($cache) {
            if (!isset($this->cache['fromcache'][$aid])) {
                $this->cache['fromdb'][$aid] = false;

                global $PL;

                $bars = $PL->cache_read('ougc_annbars');

                if (isset($bars[$aid])) {
                    $this->cache['fromcache'][$aid] = $bars[$aid];
                }
            }

            return $this->cache['fromcache'][$aid];
        } else {
            if (!isset($this->cache['fromdb'][$aid])) {
                $this->cache['fromdb'][$aid] = false;

                global $db;

                $query = $db->simple_select('ougc_annbars', '*', 'aid=\'' . (int)$aid . '\'', ['limit' => 1]);
                $bar = $db->fetch_array($query);

                if (isset($bar['aid']) && (int)$bar['aid'] > 0) {
                    $this->cache['fromdb'][$aid] = $bar;
                }
            }

            return $this->cache['fromdb'][$aid];
        }
    }

    // Get bar from DB or cache
    public function delete_bar($aid = 0)
    {
        global $db, $annbars;

        $annbars->aid = (int)$aid;

        $db->delete_query('ougc_annbars', 'aid=\'' . $annbars->aid . '\'');

        cacheUpdate();
    }

    // Set rate data
    public function set_bar_data($aid = null)
    {
        if (isset($aid) && ($bar = $this->get_bar($aid))) {
            $this->bar_data = [
                'name' => $bar['name'],
                'content' => $bar['content'],
                'style' => $bar['style'],
                'groups' => explode(',', $bar['groups']),
                'visible' => (int)$bar['visible'],
                'forums' => explode(',', $bar['forums']),
                'scripts' => $bar['scripts'],
                'dismissible' => $bar['dismissible'],
                'frules' => $bar['frules'],
                'startdate' => $bar['startdate'],
                'startdate_day' => date('j', $bar['startdate']),
                'startdate_month' => date('n', $bar['startdate']),
                'startdate_year' => date('Y', $bar['startdate']),
                'enddate' => $bar['enddate'],
                'enddate_day' => date('j', $bar['enddate']),
                'enddate_month' => date('n', $bar['enddate']),
                'enddate_year' => date('Y', $bar['enddate'])
            ];
        } else {
            $this->bar_data = [
                'name' => '',
                'content' => '',
                'style' => 'black',
                'groups' => [],
                'visible' => 1,
                'forums' => [],
                'scripts' => '',
                'dismissible' => 1,
                'frules' => '',
                'startdate' => TIME_NOW,
                'startdate_day' => date('j', TIME_NOW),
                'startdate_month' => date('n', TIME_NOW),
                'startdate_year' => date('Y', TIME_NOW),
                'enddate' => TIME_NOW,
                'enddate_day' => date('j', TIME_NOW),
                'enddate_month' => date('n', TIME_NOW),
                'enddate_year' => date('Y', TIME_NOW)
            ];
        }

        global $mybb;

        if ($mybb->request_method == 'post') {
            foreach ((array)$mybb->input as $key => $value) {
                if (isset($this->bar_data[$key])) {
                    $this->bar_data[$key] = $value;
                }
            }
        }
    }

    // Validate a rate data to insert into the DB
    public function validate_data()
    {
        global $lang;

        $this->validate_errors = [];

        $name = trim($this->bar_data['name']);

        if (!$name || my_strlen($name) > 100) {
            $this->validate_errors[] = $lang->ougc_annbars_error_invalidname;
        }

        $content = trim($this->bar_data['content']);

        if (!$content) {
            $this->validate_errors[] = $lang->ougc_annbars_error_invalidcontent;
        }

        if (!trim($this->bar_data['style'])) {
            $this->validate_errors[] = $lang->ougc_annbars_error_invalidstyle;
        }

        foreach (['start', 'end'] as $key) {
            $k = $key . 'date_';

            $lang_var = 'ougc_annbars_error_invalid' . $key . 'date';

            if (
                $this->bar_data[$k . 'day'] < 1 ||
                $this->bar_data[$k . 'day'] > 31 ||
                $this->bar_data[$k . 'month'] < 1 ||
                $this->bar_data[$k . 'month'] > 12 ||
                $this->bar_data[$k . 'year'] < 2000 ||
                $this->bar_data[$k . 'year'] > 2100
            ) {
                $this->validate_errors[] = $lang->{$lang_var};

                break;
            } else {
                $maxDays = cal_days_in_month(
                    CAL_GREGORIAN,
                    $this->bar_data[$k . 'month'],
                    $this->bar_data[$k . 'year']
                );

                if (
                    $this->bar_data[$k . 'day'] > $maxDays
                ) {
                    $this->validate_errors[] = $lang->{$lang_var};

                    break;
                }
            }

            ${$k} = $this->_mktime(
                $this->bar_data[$k . 'month'],
                $this->bar_data[$k . 'day'],
                $this->bar_data[$k . 'year']
            );
        }

        if ($startdate_ > $enddate_) {
            $this->validate_errors[] = $lang->ougc_annbars_error_invalidstartdate;
        }

        return empty($this->validate_errors);
    }

    public function insert_bar($data = [], $update = false, $aid = 0)
    {
        global $db;

        $insert_data = [
            'name' => $db->escape_string((isset($data['name']) ? $data['name'] : '')),
            'content' => $db->escape_string((isset($data['content']) ? $data['content'] : '')),
            'style' => $db->escape_string((trim($data['style']) ? trim($data['style']) : 'black')),
            'groups' => '',
            'visible' => (int)$data['visible'],
            'forums' => '',
            'scripts' => $db->escape_string($data['scripts']),
            'dismissible' => (int)$data['dismissible'],
            'frules' => $db->escape_string($data['frules']),
            'startdate' => TIME_NOW,
            'enddate' => TIME_NOW
        ];

        // Groups
        if ($data['groups'] == -1) {
            $insert_data['groups'] = -1;
        } elseif (is_array($data['groups'])) {
            $gids = [];
            foreach ($data['groups'] as $gid) {
                $gids[] = (int)$gid;
            }
            $insert_data['groups'] = $db->escape_string(implode(',', $gids));
        }

        // Forums
        if ($data['forums'] == -1) {
            $insert_data['forums'] = -1;
        } elseif (is_array($data['forums'])) {
            $gids = [];
            foreach ($data['forums'] as $gid) {
                $gids[] = (int)$gid;
            }
            $insert_data['forums'] = $db->escape_string(implode(',', $gids));
        }

        // Date
        foreach (['start', 'end'] as $key) {
            $k = $key . 'date_';
            if (isset($data[$k . 'month']) && isset($data[$k . 'day']) && isset($data[$k . 'year'])) {
                $insert_data[$key . 'date'] = $this->_mktime(
                    $data[$k . 'month'],
                    $data[$k . 'day'],
                    $data[$k . 'year']
                );
            }
        }

        if ($update) {
            $this->aid = (int)$aid;
            $db->update_query('ougc_annbars', $insert_data, 'aid=\'' . $this->aid . '\'');
        } else {
            $this->aid = (int)$db->insert_query('ougc_annbars', $insert_data);
        }
    }

    // Update an announcement bar
    public function update_bar($data = [], $aid = 0)
    {
        $this->insert_bar($data, true, $aid);
    }

    // Log admin action
    public function log_action()
    {
        if ($this->aid) {
            log_admin_action($this->aid);
        } else {
            log_admin_action();
        }
    }

    // Log admin action
    public function parse_message($message, $startdate = 0, $enddate = 0)
    {
        global $mybb, $parser, $lang;

        if ($startdate === 0) {
            $startdate = TIME_NOW;
        }

        if ($enddate === 0) {
            $enddate = TIME_NOW;
        }

        if (!is_object($parser)) {
            require_once MYBB_ROOT . 'inc/class_parser.php';
            $parser = new postParser();
        }

        $message = $parser->parse_message(
            $lang->sprintf(
                $message,
                $mybb->user['username'],
                $mybb->settings['bbname'],
                $mybb->settings['bburl'],
                my_date($mybb->settings['dateformat'], $startdate),
                my_date($mybb->settings['dateformat'], $enddate)
            ),
            [
                'allow_html' => 1,
                'allow_smilies' => 1,
                'allow_mycode' => 1,
                'filter_badwords' => 1,
                'shorten_urls' => 0
            ]
        );

        return $message;
    }

    // Clean input
    public function clean_ints($val, $implode = false)
    {
        if (!is_array($val)) {
            $val = (array)explode(',', $val);
        }

        foreach ($val as $k => &$v) {
            $v = (int)$v;
        }

        $val = array_filter($val);

        if ($implode) {
            $val = (string)implode(',', $val);
        }

        return $val;
    }

    public function _mktime($month, $day, $year)
    {
        return (int)gmmktime(0, 0, 0, (int)$month, (int)$day, (int)$year);
    }

    public function update_task($action = 2)
    {
        global $db, $lang;
        languageLoad();

        $where = 'file=\'ougc_annbars\'';

        switch ($action) {
            case 1:
            case 0:
                $db->update_query('tasks', ['enabled' => $action], $where);
                break;
            case -1:
                $db->delete_query('tasks', $where);
                break;
            default:
                $query = $db->simple_select('tasks', 'tid', $where);
                if (!$db->fetch_field($query, 'tid')) {
                    include_once MYBB_ROOT . 'inc/functions_task.php';

                    $_ = $db->escape_string('*');

                    $new_task = [
                        'title' => $db->escape_string($lang->ougc_annbars_plugin),
                        'description' => $db->escape_string($lang->ougc_annbars_plugin_d),
                        'file' => $db->escape_string('ougc_annbars'),
                        'minute' => 0,
                        'hour' => $_,
                        'day' => $_,
                        'weekday' => $_,
                        'month' => $_,
                        'enabled' => 1,
                        'logging' => 1
                    ];

                    $new_task['nextrun'] = fetch_next_run($new_task);

                    $db->insert_query('tasks', $new_task);
                }
                break;
        }
    }
}

$GLOBALS['annbars'] = new OUGC_ANNBARS();

if (!function_exists('ougc_getpreview')) {
    /**
     * Shorts a message to look like a preview. 2.0
     * Based off Zinga Burga's "Thread Tooltip Preview" plugin threadtooltip_getpreview() function.
     *
     * @param string Message to short.
     * @param int Maximum characters to show.
     * @param bool Strip MyCode Quotes from message.
     * @param bool Strip MyCode from message.
     * @return string Shortened message
     **/
    function ougc_getpreview($message, $maxlen = 100, $stripquotes = true, $stripmycode = true, $parser_options = [])
    {
        // Attempt to remove quotes, skip if going to strip MyCode
        if ($stripquotes && !$stripmycode) {
            $message = preg_replace([
                '#\[quote=([\"\']|&quot;|)(.*?)(?:\\1)(.*?)(?:[\"\']|&quot;)?\](.*?)\[/quote\](\r\n?|\n?)#esi',
                '#\[quote\](.*?)\[\/quote\](\r\n?|\n?)#si',
                '#\[quote\]#si',
                '#\[\/quote\]#si'
            ], '', $message);
        }

        // Attempt to remove any MyCode
        if ($stripmycode) {
            global $parser;
            if (!is_object($parser)) {
                require_once MYBB_ROOT . 'inc/class_parser.php';
                $parser = new postParser();
            }

            $parser_options = array_merge([
                'allow_html' => 0,
                'allow_mycode' => 1,
                'allow_smilies' => 0,
                'allow_imgcode' => 1,
                'filter_badwords' => 1,
                'nl2br' => 0
            ], $parser_options);

            $message = $parser->parse_message($message, $parser_options);

            // before stripping tags, try converting some into spaces
            $message = preg_replace([
                '~\<(?:img|hr).*?/\>~si',
                '~\<li\>(.*?)\</li\>~si'
            ], [' ', "\n* $1"], $message);

            $message = unhtmlentities(strip_tags($message));
        }

        // convert \xA0 to spaces (reverse &nbsp;)
        $message = trim(
            preg_replace(['~ {2,}~', "~\n{2,}~"],
                [' ', "\n"],
                strtr($message, [utf8_encode("\xA0") => ' ', "\r" => '', "\t" => ' ']
                ))
        );

        // newline fix for browsers which don't support them
        $message = preg_replace("~ ?\n ?~", " \n", $message);

        // Shorten the message if too long
        if (my_strlen($message) > $maxlen) {
            $message = my_substr($message, 0, $maxlen - 1) . '...';
        }

        return htmlspecialchars_uni($message);
    }
}

if (!function_exists('ougc_print_selection_javascript')) {
    function ougc_print_selection_javascript()
    {
        static $already_printed = false;

        if ($already_printed) {
            return;
        }

        $already_printed = true;

        echo "<script type=\"text/javascript\">
		function checkAction(id)
		{
			var checked = '';

			$('.'+id+'_forums_groups_check').each(function(e, val)
			{
				if($(this).prop('checked') == true)
				{
					checked = $(this).val();
				}
			});

			$('.'+id+'_forums_groups').each(function(e)
			{
				$(this).hide();
			});

			if($('#'+id+'_forums_groups_'+checked))
			{
				$('#'+id+'_forums_groups_'+checked).show();
			}
		}
	</script>";
    }
}
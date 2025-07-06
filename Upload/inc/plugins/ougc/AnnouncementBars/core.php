<?php

/***************************************************************************
 *
 *    ougc Announcement Bars plugin (/inc/plugins/ougc/AnnouncementBars/core.php)
 *    Author: Omar Gonzalez
 *    Copyright: © 2012 Omar Gonzalez
 *
 *    Website: https://ougc.network
 *
 *    Manage custom announcement notifications that render to users in the page.
 *
 ***************************************************************************
 ****************************************************************************
 * This program is protected software: you can make use of it under
 * the terms of the OUGC Network EULA as detailed by the included
 * "EULA.TXT" file.
 *
 * This program is distributed with the expectation that it will be
 * useful, but WITH LIMITED WARRANTY; with a limited warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * OUGC Network EULA included in the "EULA.TXT" file for more details.
 *
 * You should have received a copy of the OUGC Network EULA along with
 * the package which includes this file.  If not, see
 * <https://ougc.network/eula.txt>.
 ****************************************************************************/

declare(strict_types=1);

namespace ougc\AnnouncementBars\Core;

use postParser;

use const ougc\AnnouncementBars\DEBUG;
use const ougc\AnnouncementBars\ROOT;
use const ougc\AnnouncementBars\SETTINGS;

const VERSION = '1.8.37';

const VERSION_CODE = 1837;

const STYLES = ['black', 'white', 'red', 'green', 'blue', 'brown', 'pink', 'orange'];

const URL = 'modcp.php';

function addHooks(string $namespace): void
{
    global $plugins;

    $namespaceLowercase = strtolower($namespace);
    $definedUserFunctions = get_defined_functions()['user'];

    foreach ($definedUserFunctions as $callable) {
        $namespaceWithPrefixLength = strlen($namespaceLowercase) + 1;

        if (substr($callable, 0, $namespaceWithPrefixLength) == $namespaceLowercase . '\\') {
            $hookName = substr_replace($callable, '', 0, $namespaceWithPrefixLength);

            $priority = substr($callable, -2);

            if (is_numeric(substr($hookName, -2))) {
                $hookName = substr($hookName, 0, -2);
            } else {
                $priority = 10;
            }

            $plugins->add_hook($hookName, $callable, $priority);
        }
    }
}

function languageLoad(): bool
{
    global $lang;

    if (!isset($lang->ougc_annbars_plugin)) {
        if (defined('IN_ADMINCP')) {
            $lang->load('ougc_annbars');
        } else {
            $lang->load('ougc_annbars', false, true);
        }
    }

    return true;
}

function getSetting(string $settingKey = '')
{
    global $mybb;

    return SETTINGS[$settingKey] ?? ($mybb->settings['ougc_annbars_' . $settingKey] ?? false);
}

function getTemplateName(string $templateName = ''): string
{
    $templatePrefix = '';

    if ($templateName) {
        $templatePrefix = '_';
    }

    return "ougcannbars{$templatePrefix}{$templateName}";
}

function getTemplate(string $templateName = '', bool $enableHTMLComments = true): string
{
    global $templates;

    if (DEBUG) {
        $filePath = ROOT . "/templates/{$templateName}.html";

        $templateContents = file_get_contents($filePath);

        $templates->cache[getTemplateName($templateName)] = $templateContents;
    } elseif (my_strpos($templateName, '/') !== false) {
        $templateName = substr($templateName, strpos($templateName, '/') + 1);
    }

    return $templates->render(getTemplateName($templateName), true, $enableHTMLComments);
}

function urlHandler(string $newUrl = ''): string
{
    static $setUrl = URL;

    if ($newUrl = trim($newUrl)) {
        $setUrl = $newUrl;
    }

    return $setUrl;
}

function urlHandlerSet(string $newUrl): void
{
    urlHandler($newUrl);
}

function urlHandlerGet(): string
{
    return urlHandler();
}

function urlHandlerBuild(array $urlAppend = [], bool $fetchImportUrl = false, bool $encode = true): string
{
    global $PL;

    if (!is_object($PL)) {
        $PL or require_once PLUGINLIBRARY;
    }

    if ($fetchImportUrl === false) {
        if ($urlAppend && !is_array($urlAppend)) {
            $urlAppend = explode('=', $urlAppend);
            $urlAppend = [$urlAppend[0] => $urlAppend[1]];
        }
    }

    return $PL->url_append(urlHandlerGet(), $urlAppend, '&amp;', $encode);
}

function announcementInsert(array $announcementData, bool $isUpdate = false, int $announcementID = 0): int
{
    global $db;

    $insertData = [];

    if (isset($announcementData['aid'])) {
        $insertData['aid'] = (int)$announcementData['aid'];
    }

    if (isset($announcementData['name'])) {
        $insertData['name'] = $db->escape_string($announcementData['name']);
    }

    if (isset($announcementData['content'])) {
        $insertData['content'] = $db->escape_string($announcementData['content']);
    }

    if (isset($announcementData['style'])) {
        $insertData['style'] = $db->escape_string($announcementData['style']);
    }

    if (isset($announcementData['groups'])) {
        $insertData['groups'] = $db->escape_string($announcementData['groups']);
    }

    if (isset($announcementData['forums'])) {
        $insertData['forums'] = $db->escape_string($announcementData['forums']);
    }

    if (isset($announcementData['scripts'])) {
        $insertData['scripts'] = $db->escape_string($announcementData['scripts']);
    }

    if (isset($announcementData['frules'])) {
        $insertData['frules'] = $db->escape_string($announcementData['frules']);
    }

    if (isset($announcementData['startdate'])) {
        $insertData['startdate'] = (int)$announcementData['startdate'];
    }

    if (isset($announcementData['enddate'])) {
        $insertData['enddate'] = (int)$announcementData['enddate'];
    }

    if (isset($announcementData['disporder'])) {
        $insertData['disporder'] = (int)$announcementData['disporder'];
    }

    if (isset($announcementData['dismissible'])) {
        $insertData['dismissible'] = (int)$announcementData['dismissible'];
    }

    if (isset($announcementData['visible'])) {
        $insertData['visible'] = (int)$announcementData['visible'];
    }

    if ($isUpdate) {
        $db->update_query('ougc_annbars', $insertData, "aid='{$announcementID}'");

        return $announcementID;
    }

    return $db->insert_query('ougc_annbars', $insertData);
}

function announcementUpdate(array $announcementData, int $announcementID): int
{
    return announcementInsert($announcementData, true, $announcementID);
}

function announcementDelete(int $announcementID): void
{
    global $db;

    $db->delete_query('ougc_annbars', "aid='{$announcementID}'");
}

function announcementGet(array $whereClauses, array $queryFields = [], array $queryOptions = []): array
{
    global $db;

    $queryFields[] = 'aid';

    $query = $db->simple_select(
        'ougc_annbars',
        implode(',', $queryFields),
        implode(' AND ', $whereClauses),
        $queryOptions
    );

    if (isset($queryOptions['limit']) && $queryOptions['limit'] === 1) {
        return (array)$db->fetch_array($query);
    }

    $announcementObjects = [];

    while ($announcementData = $db->fetch_array($query)) {
        $announcementObjects[(int)$announcementData['aid']] = $announcementData;
    }

    return $announcementObjects;
}

function cacheUpdate(): void
{
    global $cache;

    $timeNow = TIME_NOW;

    $cacheData = array_map(function ($announcementData) {
        return $announcementData;
    },
        announcementGet(
            ["startdate<'{$timeNow}'", "enddate>='{$timeNow}'", "visible='1'"],
            ['aid', 'content', 'style', 'groups', 'forums', 'scripts', 'frules', 'startdate', 'enddate', 'dismissible'],
            ['order_by' => 'disporder']
        ));

    $cache->update('ougc_annbars', $cacheData);
}

function cacheGet(): array
{
    global $mybb;

    return $mybb->cache->read('ougc_annbars') ?? [];
}

function parseMessage(string $message, array $parserOptions = []): string
{
    global $mybb, $parser, $lang;

    if (!is_object($parser)) {
        require_once MYBB_ROOT . 'inc/class_parser.php';

        $parser = new postParser();
    }

    return $parser->parse_message($message, array_merge([
        'allow_html' => false,
        'allow_mycode' => true,
        'allow_smilies' => false,
        'allow_imgcode' => true,
        'filter_badwords' => true,
        'nl2br' => true
    ], $parserOptions));
}

function announcementBuildJavaScript(): string
{
    global $mybb;

    $timeNow = TIME_NOW;

    $cutOffTime = TIME_NOW - (60 * 60 * 24 * getSetting('dismisstime'));

    $fileVersion = VERSION_CODE;

    if (DEBUG) {
        $fileVersion = TIME_NOW;
    }

    return eval(getTemplate('globalJavaScript'));
}

function announcementBuildBar(array $announcementData, int $announcementID, bool $addIdentifier = true): string
{
    global $mybb, $lang;
    global $theme;

    $announcementStyleClass = $announcementData['style'];

    if (!in_array($announcementData['style'], STYLES)) {
        $announcementStyleClass = 'custom ' . htmlspecialchars_uni($announcementData['style']);
    }

    $announcementElementIdentifier = $addIdentifier ? "ougcannbars_bar_{$announcementID}" : '';

    $dismissButton = '';

    if ($announcementData['dismissible']) {
        $dismissButton = eval(getTemplate('announcementBarDismissButton'));
    }

    $lang_val = 'ougcAnnouncementBarsCustomBarMessage' . $announcementID;

    $announcementMessage = $announcementData['content'];

    if (!empty($lang->{$lang_val})) {
        $announcementMessage = $lang->{$lang_val};
    }

    if (!empty($replacementParams)) {
        $announcementMessage = str_replace(
            array_keys($replacementParams),
            array_values($replacementParams),
            $announcementMessage
        );
    }

    $replacements = [
        '{1}' => $mybb->user['username'],
        '{username}' => $mybb->user['username'],
        '{2}' => $mybb->settings['bbname'],
        '{forum_name}' => $mybb->settings['bbname'],
        '{3}' => $mybb->settings['bburl'],
        '{forum_url}' => $mybb->settings['bburl'],
        '{4}' => is_numeric($announcementData['startdate']) ?
            my_date($mybb->settings['dateformat'], $announcementData['startdate']) : $announcementData['startdate'],
        '{start_date}' => is_numeric($announcementData['startdate']) ?
            my_date($mybb->settings['dateformat'], $announcementData['startdate']) : $announcementData['startdate'],
        '{5}' => is_numeric($announcementData['enddate']) ?
            my_date($mybb->settings['dateformat'], $announcementData['enddate']) : $announcementData['enddate'],
        '{end_date}' => is_numeric($announcementData['enddate']) ?
            my_date($mybb->settings['dateformat'], $announcementData['enddate']) : $announcementData['enddate'],
    ];

    $announcementMessage = parseMessage(
        str_replace(array_keys($replacements), array_values($replacements), $announcementMessage)
    );

    $hiddenStyleClass = '';

    if (!($announcementData['startdate'] < TIME_NOW && $announcementData['enddate'] >= TIME_NOW && !empty($announcementData['visible']))) {
        $hiddenStyleClass = 'ougcAnnouncementBarsHidden';
    }

    return eval(getTemplate('announcementBar'));
}

function executeTask(): void
{
    cacheUpdate();
}
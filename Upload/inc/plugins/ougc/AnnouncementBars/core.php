<?php

/***************************************************************************
 *
 *    ougc Announcement Bars plugin (/inc/plugins/ougc/AnnouncementBars/core.php)
 *    Author: Omar Gonzalez
 *    Copyright: © 2012 Omar Gonzalez
 *
 *    Website: https://ougc.network
 *
 *    Manage announcement notification bars with multiple display rules.
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

const VERSION = '2.0.0';

const VERSION_CODE = 2000;

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

function languageLoad(): void
{
    global $lang;

    isset($lang->ougcAnnouncementBars) || $lang->load('ougc_annbars');
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

    if (isset($announcementData['announcement_id'])) {
        $insertData['announcement_id'] = (int)$announcementData['announcement_id'];
    }

    if (isset($announcementData['name'])) {
        $insertData['name'] = $db->escape_string($announcementData['name']);
    }

    if (isset($announcementData['message'])) {
        $insertData['message'] = $db->escape_string($announcementData['message']);
    }

    if (isset($announcementData['style_class'])) {
        $insertData['style_class'] = $db->escape_string($announcementData['style_class']);
    }

    if (isset($announcementData['display_groups'])) {
        $insertData['display_groups'] = $db->escape_string($announcementData['display_groups']);
    }

    if (isset($announcementData['display_forums'])) {
        $insertData['display_forums'] = $db->escape_string($announcementData['display_forums']);
    }

    if (isset($announcementData['display_scripts'])) {
        $insertData['display_scripts'] = $db->escape_string($announcementData['display_scripts']);
    }

    if (isset($announcementData['display_rules'])) {
        $insertData['display_rules'] = $db->escape_string($announcementData['display_rules']);
    }

    if (isset($announcementData['start_date'])) {
        $insertData['start_date'] = (int)$announcementData['start_date'];
    }

    if (isset($announcementData['end_date'])) {
        $insertData['end_date'] = (int)$announcementData['end_date'];
    }

    if (isset($announcementData['display_order'])) {
        $insertData['display_order'] = (int)$announcementData['display_order'];
    }

    if (isset($announcementData['is_dismissable'])) {
        $insertData['is_dismissable'] = (int)$announcementData['is_dismissable'];
    }

    if (isset($announcementData['is_visible'])) {
        $insertData['is_visible'] = (int)$announcementData['is_visible'];
    }

    if ($isUpdate) {
        $db->update_query('ougc_annbars', $insertData, "announcement_id='{$announcementID}'");

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

    $db->delete_query('ougc_annbars', "announcement_id='{$announcementID}'");
}

function announcementGet(array $whereClauses, array $queryFields = [], array $queryOptions = []): array
{
    global $db;

    $queryFields[] = 'announcement_id';

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
        $announcementObjects[(int)$announcementData['announcement_id']] = $announcementData;
    }

    return $announcementObjects;
}

function cacheUpdate(): array
{
    global $cache;

    $timeNow = TIME_NOW;

    $cacheData = array_map(function ($announcementData) {
        return $announcementData;
    },
        announcementGet(
            [
                "(start_date<'{$timeNow}' OR start_date='' OR start_date='0')",
                "(end_date>='{$timeNow}' OR end_date='' OR end_date='0')",
                "is_visible='1'",
                "display_groups!=''",
                'display_groups IS NOT NULL',
            ],
            [
                'announcement_id',
                'message',
                'style_class',
                'display_groups',
                'display_forums',
                'display_scripts',
                'display_rules',
                'start_date',
                'end_date',
                'is_dismissable'
            ],
            ['order_by' => 'display_order']
        ));

    $cache->update('ougc_annbars', $cacheData);

    return $cacheData;
}

function cacheGet(): array
{
    global $mybb;

    $cacheData = $mybb->cache->read('ougc_annbars') ?? [];

    if (!$cacheData || DEBUG) {
        $cacheData = cacheUpdate();
    }

    return $cacheData;
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
    static $done = false;

    if ($done) {
        return '';
    }

    $done = true;

    global $mybb;

    $timeNow = TIME_NOW;

    $cutOffTime = TIME_NOW - (60 * 60 * 24 * getSetting('dismisstime'));

    $fileVersion = VERSION_CODE;

    $debug = 'false';

    if (DEBUG) {
        $fileVersion = TIME_NOW;

        $debug = 'true';
    }

    return eval(getTemplate('globalJavaScript'));
}

function announcementBuildBar(
    array $announcementData,
    int $announcementID,
    bool $addIdentifier = false,
    array $replacementParams = []
): string {
    global $mybb, $lang;
    global $theme;

    $announcementStyleClass = ucfirst($announcementData['style_class']);

    if (!in_array($announcementData['style_class'], STYLES)) {
        $announcementStyleClass = 'Custom ' . htmlspecialchars_uni($announcementData['style_class']);
    }

    $announcementElementIdentifier = $addIdentifier ? "announcementBarItem{$announcementID}" : '';

    $dismissButton = '';

    if ($announcementData['is_dismissable']) {
        $dismissButton = eval(getTemplate('announcementBarDismissButton'));
    }

    $lang_val = 'ougcAnnouncementBarsCustomBarMessage' . $announcementID;

    $announcementMessage = $announcementData['message'];

    if (!empty($lang->{$lang_val})) {
        $announcementMessage = $lang->{$lang_val};
    }

    $replacementParams = array_merge($replacementParams, [
        '{1}' => $mybb->user['username'],
        '{username}' => $mybb->user['username'],
        '{2}' => $mybb->settings['bbname'],
        '{forum_name}' => $mybb->settings['bbname'],
        '{3}' => $mybb->settings['bburl'],
        '{forum_url}' => $mybb->settings['bburl'],
        '{4}' => is_numeric($announcementData['start_date']) ?
            my_date($mybb->settings['dateformat'], $announcementData['start_date']) : $announcementData['start_date'],
        '{start_date}' => is_numeric($announcementData['start_date']) ?
            my_date($mybb->settings['dateformat'], $announcementData['start_date']) : $announcementData['start_date'],
        '{5}' => is_numeric($announcementData['end_date']) ?
            my_date($mybb->settings['dateformat'], $announcementData['end_date']) : $announcementData['end_date'],
        '{end_date}' => is_numeric($announcementData['end_date']) ?
            my_date($mybb->settings['dateformat'], $announcementData['end_date']) : $announcementData['end_date'],
    ]);

    $announcementMessage = parseMessage(
        str_replace(array_keys($replacementParams), array_values($replacementParams), $announcementMessage)
    );

    $hiddenStyleClass = '';

    if ((!empty($announcementData['start_date']) && $announcementData['start_date'] > TIME_NOW) ||
        (!empty($announcementData['end_date']) && $announcementData['end_date'] < TIME_NOW) ||
        isset($announcementData['is_visible']) && empty($announcementData['is_visible'])) {
        $hiddenStyleClass = 'ougcAnnouncementBarsHidden';
    }

    return eval(getTemplate('announcementBar'));
}

function executeTask(): void
{
    cacheUpdate();
}
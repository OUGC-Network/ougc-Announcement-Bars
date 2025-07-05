<?php

/***************************************************************************
 *
 *    OUGC Announcement Bars plugin (/inc/plugins/ougc/AnnouncementBars/core.php)
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

const VERSION = '1.8.37';

const VERSION_CODE = 1837;

function addHooks(string $namespace)
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
            ['aid', 'content', 'style', 'groups', 'forums', 'scripts', 'dismissible', 'frules', 'startdate', 'enddate'],
            ['order_by' => 'disporder']
        ));

    $cache->update('ougc_annbars', $cacheData);
}
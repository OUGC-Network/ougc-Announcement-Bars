<?php

/***************************************************************************
 *
 *    ougc Announcement Bars plugin (/inc/plugins/ougc_annbars.php)
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

use const ougc\AnnouncementBars\ROOT;

defined('IN_MYBB') || die('This file cannot be accessed directly.');

// You can uncomment the lines below to avoid storing some settings in the DB
define('ougc\AnnouncementBars\SETTINGS', [
    //'key' => '',
    'allowHtml' => false,
    'pageAction' => 'ougcAnnouncementBars',
    'inputTimeFormat' => 'Y-m-d',
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
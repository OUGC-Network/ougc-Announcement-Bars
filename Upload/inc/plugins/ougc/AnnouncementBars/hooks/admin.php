<?php

/***************************************************************************
 *
 *    ougc Announcement Bars plugin (/inc/plugins/ougc/AnnouncementBars/hooks/admin.php)
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

namespace ougc\AnnouncementBars\Hooks\Admin;

use MyBB;

use function ougc\AnnouncementBars\Core\announcementGet;
use function ougc\AnnouncementBars\Core\languageLoad;

function admin_config_plugins_deactivate(): void
{
    global $mybb, $page;

    if (
        $mybb->get_input('action') != 'deactivate' ||
        $mybb->get_input('plugin') != 'ougc_annbars' ||
        !$mybb->get_input('uninstall', MyBB::INPUT_INT)
    ) {
        return;
    }

    if ($mybb->request_method != 'post') {
        $page->output_confirm_action(
            'index.php?module=config-plugins&amp;action=deactivate&amp;uninstall=1&amp;plugin=ougc_annbars'
        );
    }

    if ($mybb->get_input('no')) {
        admin_redirect('index.php?module=config-plugins');
    }
}

function admin_tools_get_admin_log_action(array &$hookArguments): void
{
    if ($hookArguments['logitem']['module'] !== 'forum-ougc_annbars' ||
        empty($hookArguments['logitem']['data'][0])) {
        return;
    }

    global $lang;

    languageLoad();

    $announcementData = announcementGet(["aid='{$hookArguments['logitem']['data'][0]}'"]);

    if (isset($announcementData['aid'])) {
        $lang->{$hookArguments['lang_string']} = $lang->sprintf(
            $lang->{$hookArguments['lang_string']},
            1,
            $announcementData['aid']
        );
    }
}
<?php

/***************************************************************************
 *
 *    ougc Announcement Bars plugin (/inc/tasks/ougc_annbars.php)
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

use function ougc\AnnouncementBars\Core\languageLoad;
use function ougc\AnnouncementBars\Core\executeTask;

function task_ougc_annbars(array $task): void
{
    global $lang;

    languageLoad();

    executeTask();

    add_task_log($task, $lang->ougcAnnouncementBarsTaskRan);
}
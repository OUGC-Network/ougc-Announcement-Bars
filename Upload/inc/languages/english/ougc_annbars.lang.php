<?php

/***************************************************************************
 *
 *    ougc Announcement Bars plugin (/inc/languages/english/ougc_annbars.php)
 *    Author: Omar Gonzalez
 *    Copyright: © 2012 - 2016 Omar Gonzalez
 *
 *    Website: http://omarg.me
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

$l = [
    'ougcAnnouncementBarsModeratorControlPanel' => 'Announcement Bars',
    'ougcAnnouncementBarsModeratorControlPanelNavigation' => 'Announcement Bars',
    'ougcAnnouncementBarsModeratorControlPanelBreadcrumb' => 'Announcement Bars',

    'ougcAnnouncementBarsModeratorControlPanelTableTitle' => 'Announcement Bars',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderName' => 'Name',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayGroups' => 'Display Groups',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayForums' => 'Display Forums',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayScripts' => 'Display Scripts',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderOptions' => 'Options',

    'ougcAnnouncementBarsModeratorControlPanelEmpty' => 'There are currently no announcement bars to display.',

    'ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayGroupsAll' => 'All Groups',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayGroupsNone' => 'None',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayForumsAll' => 'All Forums',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayForumsNone' => 'None',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayScriptsAll' => 'All Scripts',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayScriptsNone' => 'None',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderOptionsEdit' => 'Edit',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderOptionsDelete' => 'Delete',
    'ougcAnnouncementBarsModeratorControlPanelTableHeaderOptionsDeleteConfirm' => 'Are you sure you want to delete this announcement bar? This can not be reverted.',

    'ougcAnnouncementBarsModeratorControlPanelButtonNew' => 'New Announcement',

    'ougcAnnouncementBarsModeratorControlPanelRedirectNew' => 'The announcement bar was successfully added.',
    'ougcAnnouncementBarsModeratorControlPanelRedirectEdit' => 'The announcement bar was successfully edited.',
    'ougcAnnouncementBarsModeratorControlPanelRedirectDelete' => 'The announcement bar was successfully deleted.',

    'ougcAnnouncementBarsModeratorControlPanelLogActionDelete' => 'Announcement Bar Deletion',
    'ougcAnnouncementBarsModeratorControlPanelLogInformation' => 'Announcement Identifier: {1}, Announcement Name: {2}',

    'ougcAnnouncementBarsModeratorControlPanelNewEditTableTitleNew' => 'New Announcement Bar',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableTitleEdit' => 'Edit Announcement Bar',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderName' => 'Name',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderNameDescription' => 'Select a name to identify this announcement bar in the moderator control panel.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderMessage' => 'Message',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderMessageDescription' => 'The announcement bar message displayed to users.<pre>
{username} = Current user username
{forum_name} = Forum name
{forum_url} = Forum URL
{start_date} = Start date
{end_date} = End date
{displayKey?} = For a Display Rule result value
</pre>',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClass' => 'Style Class',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassDescription' => 'The announcement bar CSS style class.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStylePredefined' => 'Predefined',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassBlack' => 'Black',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassWhite' => 'White',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassRed' => 'Red',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassGreen' => 'Green',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassBlue' => 'Blue',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassBrown' => 'Brown',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassPink' => 'Pink',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassOrange' => 'Orange',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClassCustom' => 'Custom',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayGroups' => 'Display Groups',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayGroupsDescription' => 'Select the user groups that will see this announcement bar.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayGroupsAll' => 'All Groups',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayGroupsCustom' => 'Selected Groups',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayGroupsNone' => 'None',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayForums' => 'Display Forums',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayForumsDescription' => 'Select the forums in which this announcement bar will be displayed.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayForumsAll' => 'All Forums',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayForumsCustom' => 'Selected Forums',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayForumsNone' => 'None',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayScripts' => 'Display Scripts',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayScriptsDescription' => 'Select the scripts in which this announcement bar will be displayed.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayScriptsCustomPlaceholder' => 'One script per line',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStartDate' => 'Start Date',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStartDateDescription' => 'Select a start date since from when this announcement bar will start being displayed.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderEndDate' => 'End Date',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderEndDateDescription' => 'Select a start date since from when this announcement bar will stop being displayed.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayRules' => 'Display Rules',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayRulesDescription' => 'A JSON format list of conditionals to manipulate the display of this announcement. Refer to the <a href="https://github.com/OUGC-Network/ougc-Announcement-Bars?tab=readme-ov-file#usage">README in the repository</a> for more information.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayOrder' => 'Display Order',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderDisplayOrderDescription' => 'Select a display order for this announcement bar.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderAllowDismissal' => 'Allow Dismissal',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderAllowDismissalDescription' => 'Allow users to temporarily dismiss this announcement bar.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderEnabled' => 'Enabled',
    'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderEnabledDescription' => 'Enable this announcement bar.',

    'ougcAnnouncementBarsModeratorControlPanelNewEditButtonNew' => 'Add Announcement',
    'ougcAnnouncementBarsModeratorControlPanelNewEditButtonEdit' => 'Edit Announcement',

    'ougcAnnouncementBarsModeratorControlPanelNewEditErrorInvalidName' => 'The announcement bar name must be between 1 and 100 characters.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditErrorInvalidMessage' => 'The announcement bar message is invalid.',
    'ougcAnnouncementBarsModeratorControlPanelNewEditErrorInvalidDisplayRules' => 'The display rules code is invalid. Make sure the code is JSON compatible.',

    // You can change bars message by announcement id, for example, if you uncomment next line it will show up as the message of bar which id is 5.
    'ougcAnnouncementBarsCustomBarMessage5' => '<strong>Title:</strong> Hi {username}, you are visiting [url={forum_url}]{forum_name}[/url].',
    'ougcAnnouncementBarsCustomBarMessageX' => '<strong>{username}!!</strong> Click [u][url={forum_url}]here[/url][/u] to be the first one that actually clicked it!.',

    'ougc_announcement_bars_task_ran' => 'The announcement bars task successfully ran.',
];
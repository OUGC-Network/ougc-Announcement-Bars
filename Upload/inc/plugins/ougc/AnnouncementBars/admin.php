<?php

/***************************************************************************
 *
 *    ougc Announcement Bars plugin (/inc/plugins/ougc/AnnouncementBars/admin.php)
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

namespace ougc\AnnouncementBars\Admin;

use DirectoryIterator;

use function ougc\AnnouncementBars\Core\cacheUpdate;
use function ougc\AnnouncementBars\Core\languageLoad;

use const ougc\AnnouncementBars\ROOT;
use const ougc\AnnouncementBars\Core\VERSION;
use const ougc\AnnouncementBars\Core\VERSION_CODE;

const TASK_ENABLE = 1;

const TASK_DEACTIVATE = 0;

const TASK_DELETE = -1;

const TABLES_DATA = [
    'ougc_annbars' => [
        'announcement_id' => [
            'type' => 'INT',
            'unsigned' => true,
            'auto_increment' => true,
            'primary_key' => true
        ],
        'name' => [
            'type' => 'VARCHAR',
            'size' => 100,
            'default' => ''
        ],
        'message' => [
            'type' => 'TEXT',
            'null' => true,
        ],
        'style_class' => [
            'type' => 'VARCHAR',
            'size' => 20,
            'default' => ''
        ],
        'display_groups' => [
            'type' => 'TEXT',
            'null' => true,
        ],
        'display_forums' => [
            'type' => 'TEXT',
            'null' => true,
        ],
        'display_scripts' => [
            'type' => 'TEXT',
            'null' => true,
        ],
        'display_rules' => [
            'type' => 'TEXT',
            'null' => true,
        ],
        'start_date' => [
            'type' => 'INT',
            'unsigned' => true,
            'default' => 0
        ],
        'end_date' => [
            'type' => 'INT',
            'unsigned' => true,
            'default' => 0
        ],
        'display_order' => [
            'type' => 'INT',
            'unsigned' => true,
            'default' => 1
        ],
        'is_dismissable' => [
            'type' => 'TINYINT',
            'unsigned' => true,
            'default' => 1
        ],
        'is_visible' => [
            'type' => 'TINYINT',
            'unsigned' => true,
            'default' => 1
        ],
    ]
];

function pluginInformation(): array
{
    global $lang;

    languageLoad();

    return [
        'name' => 'ougc Announcement Bars',
        'description' => $lang->ougcAnnouncementBarsDescription,
        'website' => 'https://ougc.network',
        'author' => 'Omar G.',
        'authorsite' => 'https://ougc.network',
        'version' => VERSION,
        'versioncode' => VERSION_CODE,
        'compatibility' => '18*',
        'codename' => 'ougc_annbars',
        'pl' => [
            'version' => 13,
            'url' => 'https://community.mybb.com/mods.php?action=view&pid=573'
        ]
    ];
}

function pluginActivation(): void
{
    global $PL, $lang, $cache;

    pluginLibraryLoad();

    $settingsContents = file_get_contents(ROOT . '/settings.json');

    $settingsData = json_decode($settingsContents, true);

    foreach ($settingsData as $settingKey => &$settingData) {
        if (empty($lang->{"setting_ougcAnnouncementBars_{$settingKey}"})) {
            continue;
        }

        if ($settingData['optionscode'] == 'select' || $settingData['optionscode'] == 'checkbox') {
            foreach ($settingData['options'] as $optionKey) {
                $settingData['optionscode'] .= "\n{$optionKey}={$lang->{"setting_ougcAnnouncementBars_{$settingKey}_{$optionKey}"}}";
            }
        }

        $settingData['title'] = $lang->{"setting_ougcAnnouncementBars_{$settingKey}"};

        $settingData['description'] = $lang->{"setting_ougcAnnouncementBars_{$settingKey}_desc"};
    }

    $PL->settings(
        'ougc_annbars',
        $lang->setting_group_ougcAnnouncementBars_rules,
        $lang->setting_group_ougcAnnouncementBars_rules_desc,
        $settingsData
    );

    $templates = [];

    if (file_exists($templateDirectory = ROOT . '/templates')) {
        $templatesDirIterator = new DirectoryIterator($templateDirectory);

        foreach ($templatesDirIterator as $template) {
            if (!$template->isFile()) {
                continue;
            }

            $pathName = $template->getPathname();

            $pathInfo = pathinfo($pathName);

            if ($pathInfo['extension'] === 'html') {
                $templates[$pathInfo['filename']] = file_get_contents($pathName);
            }
        }
    }

    if ($templates) {
        $PL->templates('ougcannbars', 'ougc Announcement Bars', $templates);
    }

    if ($styleSheetContents = file_get_contents(ROOT . '/stylesheet.css')) {
        $PL->stylesheet('ougc_annbars', $styleSheetContents);
    }

    // Insert/update version into cache
    $plugins = $cache->read('ougc_plugins');

    if (!$plugins) {
        $plugins = [];
    }

    $pluginInformation = pluginInformation();

    if (!isset($plugins['annbars'])) {
        $plugins['annbars'] = $pluginInformation['versioncode'];
    }

    /*~*~* RUN UPDATES START *~*~*/
    global $db;

    /*
    if($plugins['annbars'] <= 1836)
    {
        foreach(['frules_fid', 'frules_closed', 'frules_visible', 'frules_dateline'] as $fieldName)
        {
            if($db->field_exists('ougc_annbars', $fieldName))
            {
                $db->drop_column('ougc_annbars', $fieldName);
            }
        }
    }
    */

    if (pluginIsInstalled()) {
        foreach (
            [
                'aid' => 'announcement_id',
                'content' => 'message',
                'style' => 'style_class',
                'groups' => 'display_groups',
                'forums' => 'display_forums',
                'scripts' => 'display_scripts',
                'frules' => 'display_rules',
                'startdate' => 'start_date',
                'enddate' => 'end_date',
                'disporder' => 'display_order',
                'dismissible' => 'is_dismissable',
                'visible' => 'is_visible',
            ] as $oldFieldName => $newFieldName
        ) {
            if ($db->field_exists($oldFieldName, 'ougc_annbars')) {
                $db->rename_column(
                    'ougc_annbars',
                    $oldFieldName,
                    $newFieldName,
                    dbBuildFieldDefinition(TABLES_DATA['ougc_annbars'][$newFieldName])
                );
            }
        }
    }

    /*~*~* RUN UPDATES END *~*~*/

    dbVerifyTables();

    enableTask();

    $plugins['annbars'] = $pluginInformation['versioncode'];

    $cache->update('ougc_plugins', $plugins);

    cacheUpdate();

    change_admin_permission('forums', 'ougc_annbars', -1);
}

function pluginIsInstalled(): bool
{
    global $db;

    static $isInstalled = null;

    if ($isInstalled === null) {
        $isInstalled = true;

        foreach (dbTables() as $tableName => $tableData) {
            $isInstalled = $db->table_exists($tableName) && $isInstalled;
        }
    }

    return $isInstalled;
}

function pluginUninstallation(): void
{
    global $db, $PL, $cache;

    pluginLibraryLoad();

    // Drop DB entries
    foreach (TABLES_DATA as $tableName => $tableData) {
        if ($db->table_exists($tableName)) {
            $db->drop_table($tableName);
        }
    }

    $PL->settings_delete('ougc_annbars');

    $PL->templates_delete('ougcannbars');

    $PL->stylesheet_delete('ougc_annbars');

    deleteTask();

    // Delete version from cache
    $plugins = (array)$cache->read('ougc_plugins');

    if (isset($plugins['annbars'])) {
        unset($plugins['annbars']);
    }

    if (!empty($plugins)) {
        $cache->update('ougc_plugins', $plugins);
    } else {
        $cache->delete('ougc_plugins');
    }

    $cache->delete('ougc_annbars');
}

function pluginLibraryLoad(): void
{
    global $PL, $lang;

    languageLoad();

    if ($fileExists = file_exists(PLUGINLIBRARY)) {
        global $PL;

        $PL || require_once PLUGINLIBRARY;
    }

    $pluginInformation = pluginInformation();

    if (!$fileExists || $PL->version < $pluginInformation['pl']['version']) {
        flash_message(
            $lang->sprintf(
                $lang->ougcAnnouncementBarsPluginLibrary,
                $pluginInformation['pl']['url'],
                $pluginInformation['pl']['version']
            ),
            'error'
        );

        admin_redirect('index.php?module=config-plugins');
    }
}

function enableTask(int $action = TASK_ENABLE): bool
{
    global $db, $lang;

    languageLoad();

    if ($action === TASK_DELETE) {
        $db->delete_query('tasks', "file='ougc_annbars'");

        return true;
    }

    $query = $db->simple_select('tasks', '*', "file='ougc_annbars'", ['limit' => 1]);

    $task = $db->fetch_array($query);

    if ($task) {
        $db->update_query('tasks', ['enabled' => $action], "file='ougc_annbars'");
    } else {
        include_once MYBB_ROOT . 'inc/functions_task.php';

        $_ = $db->escape_string('*');

        $new_task = [
            'title' => $db->escape_string($lang->setting_group_ougcAnnouncementBars_rules),
            'description' => $db->escape_string($lang->setting_group_ougcAnnouncementBars_rules_desc),
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

    return true;
}

function disableTask(): bool
{
    enableTask(TASK_DEACTIVATE);

    return true;
}

function deleteTask(): bool
{
    enableTask(TASK_DELETE);

    return true;
}

function dbTables(): array
{
    $tables_data = [];

    foreach (TABLES_DATA as $tableName => $tableColumns) {
        foreach ($tableColumns as $fieldName => $fieldData) {
            if (!isset($fieldData['type'])) {
                continue;
            }

            $tables_data[$tableName][$fieldName] = dbBuildFieldDefinition($fieldData);
        }

        foreach ($tableColumns as $fieldName => $fieldData) {
            if (isset($fieldData['primary_key'])) {
                $tables_data[$tableName]['primary_key'] = $fieldName;
            }

            if ($fieldName === 'unique_key') {
                $tables_data[$tableName]['unique_key'] = $fieldData;
            }
        }
    }

    return $tables_data;
}

function dbVerifyTables(): bool
{
    global $db;

    $collation = $db->build_create_table_collation();

    foreach (dbTables() as $tableName => $tableColumns) {
        if ($db->table_exists($tableName)) {
            foreach ($tableColumns as $fieldName => $fieldData) {
                if ($fieldName == 'primary_key' || $fieldName == 'unique_key') {
                    continue;
                }

                if ($db->field_exists($fieldName, $tableName)) {
                    $db->modify_column($tableName, "`{$fieldName}`", $fieldData);
                } else {
                    $db->add_column($tableName, $fieldName, $fieldData);
                }
            }
        } else {
            $query_string = "CREATE TABLE IF NOT EXISTS `{$db->table_prefix}{$tableName}` (";

            foreach ($tableColumns as $fieldName => $fieldData) {
                if ($fieldName == 'primary_key') {
                    $query_string .= "PRIMARY KEY (`{$fieldData}`)";
                } elseif ($fieldName != 'unique_key') {
                    $query_string .= "`{$fieldName}` {$fieldData},";
                }
            }

            $query_string .= ") ENGINE=MyISAM{$collation};";

            $db->write_query($query_string);
        }
    }

    dbVerifyIndexes();

    return true;
}

function dbVerifyIndexes(): bool
{
    global $db;

    foreach (dbTables() as $tableName => $tableColumns) {
        if (!$db->table_exists($tableName)) {
            continue;
        }

        if (isset($tableColumns['unique_key'])) {
            foreach ($tableColumns['unique_key'] as $key_name => $key_value) {
                if ($db->index_exists($tableName, $key_name)) {
                    continue;
                }

                $db->write_query(
                    "ALTER TABLE {$db->table_prefix}{$tableName} ADD UNIQUE KEY {$key_name} ({$key_value})"
                );
            }
        }
    }

    return true;
}

function dbBuildFieldDefinition(array $fieldData): string
{
    $field_definition = '';

    $field_definition .= $fieldData['type'];

    if (isset($fieldData['size'])) {
        $field_definition .= "({$fieldData['size']})";
    }

    if (isset($fieldData['unsigned'])) {
        if ($fieldData['unsigned'] === true) {
            $field_definition .= ' UNSIGNED';
        } else {
            $field_definition .= ' SIGNED';
        }
    }

    if (!isset($fieldData['null'])) {
        $field_definition .= ' NOT';
    }

    $field_definition .= ' NULL';

    if (isset($fieldData['auto_increment'])) {
        $field_definition .= ' AUTO_INCREMENT';
    }

    if (isset($fieldData['default'])) {
        $field_definition .= " DEFAULT '{$fieldData['default']}'";
    }

    return $field_definition;
}
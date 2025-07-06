<?php

/***************************************************************************
 *
 *    ougc Announcement Bars plugin (/inc/plugins/ougc/AnnouncementBars/hooks/forum.php)
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

namespace ougc\AnnouncementBars\Hooks\Forum;

use DateTimeImmutable;
use MyBB;

use function ougc\AnnouncementBars\Core\announcementBuildBar;
use function ougc\AnnouncementBars\Core\announcementBuildJavaScript;
use function ougc\AnnouncementBars\Core\announcementDelete;
use function ougc\AnnouncementBars\Core\announcementGet;
use function ougc\AnnouncementBars\Core\announcementInsert;
use function ougc\AnnouncementBars\Core\announcementUpdate;
use function ougc\AnnouncementBars\Core\cacheGet;
use function ougc\AnnouncementBars\Core\cacheUpdate;
use function ougc\AnnouncementBars\Core\executeTask;
use function ougc\AnnouncementBars\Core\getSetting;
use function ougc\AnnouncementBars\Core\getTemplate;
use function ougc\AnnouncementBars\Core\languageLoad;
use function ougc\AnnouncementBars\Core\urlHandlerBuild;

use const ougc\AnnouncementBars\DEBUG;
use const ougc\AnnouncementBars\Core\STYLES;

function global_start05(): void
{
    global $templatelist;

    if (isset($templatelist)) {
        $templatelist .= ',';
    } else {
        $templatelist = '';
    }

    $templatelist .= 'ougcannbars_' . implode(',ougcannbars_', [
            'announcementBar',
            'announcementBarDismissButton',
            'globalAnnouncements',
            'globalJavaScript',
            'globalWrapper',
            'moderatorControlPanel',
            'moderatorControlPanelNavigation',
            'moderatorControlPanelNewEdit',
            'moderatorControlPanelNewEditPreview',
            'moderatorControlPanelSelect',
            'moderatorControlPanelSelectOption',
            'moderatorControlPanelTable',
            'moderatorControlPanelTableEmpty',
            'moderatorControlPanelTableRow'
        ]);

    if (DEBUG) {
        executeTask();
    }
}

function pre_output_page(string &$pageContents): string
{
    if (my_strpos($pageContents, '<!--OUGC_ANNBARS-->') === false) {
        return $pageContents;
    }

    global $mybb, $theme;

    $announcementsLimit = (int)getSetting('limit');

    if ($announcementsLimit < 0) {
        return $pageContents;
    }

    $announcementObjects = cacheGet();

    if (!$announcementObjects) {
        return $pageContents;
    }

    global $lang;

    languageLoad();

    $forumID = 0;

    switch (THIS_SCRIPT) {
        // $fid
        case 'announcements.php':
        case 'editpost.php':
        case 'forumdisplay.php':
        case 'newreply.php':
        case 'newthread.php':
        case 'printthread.php':
        case 'showthread.php':
        case 'ratethread.php':
        case 'moderation.php':
            // $forum
        case 'polls.php':
        case 'sendthread.php':
        case 'report.php':
            // $mybb
        case 'misc.php':
            global $fid, $forum;

            empty($fid) || $forumID = (int)$fid;

            empty($forum['fid']) || $forumID = (int)$forum['fid'];

            empty($mybb->input['fid']) || $forumID = $mybb->get_input('fid', MyBB::INPUT_INT);

            break;
    }

    if (!empty($_SERVER['PATH_INFO'])) {
        $currentScript = $_SERVER['PATH_INFO'];
    } elseif (!empty($_ENV['PATH_INFO'])) {
        $currentScript = $_ENV['PATH_INFO'];
    } elseif (!empty($_ENV['PHP_SELF'])) {
        $currentScript = $_ENV['PHP_SELF'];
    } elseif (defined('THIS_SCRIPT')) {
        $currentScript = THIS_SCRIPT;
    } else {
        $currentScript = $_SERVER['PHP_SELF'];
    }

    $currentScript = my_strtolower(basename($currentScript));

    $announcementsList = [];

    foreach ($announcementObjects as $announcementID => $announcementData) {
        if (!is_member($announcementData['groups']) || (
                $forumID && (
                    empty($announcementData['forums']) ||
                    !is_member($announcementData['forums'], ['usergroup' => $forumID, 'additionalgroups' => ''])
                )
            )) {
            continue;
        }

        if (!empty($announcementData['scripts'])) {
            $validScript = false;

            foreach (array_map('trim', explode("\n", $announcementData['scripts'])) as $scriptName) {
                if (my_strpos($scriptName, '{|}') !== false) {
                    $scriptInputs = explode('{|}', $scriptName);

                    $scriptName = $scriptInputs[0];

                    $scriptInputs = explode('|', $scriptInputs[1]);
                }

                if (my_strtolower($scriptName) !== $currentScript) {
                    continue;
                }

                if (empty($scriptInputs)) {
                    $validScript = true;
                } else {
                    foreach ($scriptInputs as $param) {
                        if (my_strpos($param, '=') !== false) {
                            list($paramName, $paramValue) = explode('=', $param, 2);

                            if (str_contains($paramValue, ',')) {
                                $paramValue = explode(',', $paramValue);
                            }
                        } else {
                            $paramName = $param;
                        }

                        if (!isset($paramValue) && isset($mybb->input[$paramName])) {
                            $validScript = true;
                        } elseif (!empty($paramValue) && (
                                (!is_array($paramValue) && $mybb->get_input($paramName) === $paramValue) ||
                                (is_array($paramValue) && in_array($mybb->get_input($paramName), $paramValue))
                            )) {
                            $validScript = true;
                        }

                        if ($validScript) {
                            break;
                        }
                    }
                }

                if ($validScript) {
                    break;
                }
            }

            if (!$validScript) {
                continue;
            }
        }
        //script_name.php{|}param_name=param_value1,param_value2|param_name=param_value
        //script_name.php{|}param_name|param_name=param_value

        $replacementParams = [];

        $displayBar = true;

        if (!empty($announcementData['frules']) && $rulesScripts = json_decode($announcementData['frules'], true)) {
            global $db;

            $whereClauses = [];

            if (isset($rulesScripts['threadCountRules']) || isset($rulesScripts['threadCountRule'])) {
                $threadCountRules = $rulesScripts['threadCountRules'] ?? [$rulesScripts['threadCountRule']];

                foreach ($threadCountRules as $threadCountRule) {
                    if (isset($threadCountRule['forumIDs'])) {
                        $forumIDs = implode("','", array_map('intval', $threadCountRule['forumIDs']));

                        $whereClauses[] = "fid IN ('{$forumIDs}')";
                    }

                    $prefixNot = '';

                    if (isset($threadCountRule['hasPrefix'])) {
                        if ($threadCountRule['hasPrefix'] === true) {
                            $whereClauses['prefix'] = "prefix>'0'";
                        } else {
                            $whereClauses['prefix'] = "prefix='0'";

                            $prefixNot = 'NOT';
                        }
                    }

                    if (isset($threadCountRule['prefixIDs'])) {
                        $prefixIDs = implode("','", array_map('intval', $threadCountRule['prefixIDs']));

                        $whereClauses['prefix'] = "prefix {$prefixNot} IN ('{$prefixIDs}')";
                    }

                    if (isset($threadCountRule['hasPoll'])) {
                        if ($threadCountRule['hasPoll'] === true) {
                            $whereClauses[] = "poll='1'";
                        } else {
                            $whereClauses[] = "poll='0'";
                        }
                    }

                    if (isset($threadCountRule['createDaysCut'])) {
                        $createDaysStamp = TIME_NOW - 60 * 60 * 24 * 7 * (int)$threadCountRule['createDaysCut'];

                        $whereClauses[] = "dateline>'{$createDaysStamp}'";
                    }

                    $repliesOperator = '>';

                    if (isset($threadCountRule['hasReplies'])) {
                        if ($threadCountRule['hasReplies'] === true) {
                            $whereClauses['replies'] = "replies>'0'";
                        } else {
                            $whereClauses['replies'] = "replies='0'";

                            $repliesOperator = '<';
                        }
                    }

                    if (isset($threadCountRule['hasRepliesCount'])) {
                        $hasRepliesCount = (int)$threadCountRule['hasRepliesCount'];

                        $whereClauses['replies'] = "replies{$repliesOperator}'{$hasRepliesCount}'";
                    }

                    if (isset($threadCountRule['closedThreads'])) {
                        if ($threadCountRule['closedThreads'] === true) {
                            $whereClauses[] = "closed='1'";
                        } else {
                            $whereClauses[] = "closed NOT LIKE 'moved|%'";
                        }
                    }

                    if (isset($threadCountRule['stuckThreads'])) {
                        if ($threadCountRule['stuckThreads'] === true) {
                            $whereClauses[] = "sticky='1'";
                        } else {
                            $whereClauses[] = "sticky='0'";
                        }
                    }

                    $visibleStatuses = ['in' => [], 'inNot' => []];

                    if (isset($threadCountRule['visibleThreads'])) {
                        if ($threadCountRule['visibleThreads'] === true) {
                            $visibleStatuses['in'][] = 1;
                        } else {
                            $visibleStatuses['notIn'][] = 1;
                        }
                    }

                    if (isset($threadCountRule['unapprovedThreads'])) {
                        if ($threadCountRule['unapprovedThreads'] === true) {
                            $visibleStatuses['in'][] = 0;
                        } else {
                            $visibleStatuses['notIn'][] = 0;
                        }
                    }

                    if (isset($threadCountRule['deletedThreads'])) {
                        if ($threadCountRule['deletedThreads'] === true) {
                            $visibleStatuses['in'][] = -1;
                        } else {
                            $visibleStatuses['notIn'][] = -1;
                        }
                    }

                    if (!empty($visibleStatuses)) {
                        if (!empty($visibleStatuses['in'])) {
                            $inString = implode("','", $visibleStatuses['in']);

                            $whereClauses[] = "visible IN ('{$inString}')";
                        }

                        if (!empty($visibleStatuses['notIn'])) {
                            $notInString = implode("','", $visibleStatuses['notIn']);

                            $whereClauses[] = "visible NOT IN ('{$notInString}')";
                        }
                    }

                    if (function_exists('ougc_showinportal_info') && isset($threadCountRule['showInPortal'])) {
                        if ($threadCountRule['showInPortal'] === true) {
                            $whereClauses[] = "showinportal='1'";
                        } else {
                            $whereClauses[] = "showinportal='0'";
                        }
                    }

                    $dbQuery = $db->simple_select(
                        'threads',
                        'COUNT(tid) AS total_threads',
                        implode(' AND ', $whereClauses)
                    );

                    $queryResult = (int)$db->fetch_field($dbQuery, 'total_threads');

                    if (isset($threadCountRule['displayComparisonOperator'])) {
                        $displayComparisonValue = $threadCountRule['displayComparisonValue'] ?? 1;

                        switch ($threadCountRule['displayComparisonOperator']) {
                            case '<':
                                if (!($queryResult < $displayComparisonValue)) {
                                    $displayBar = false;
                                }
                                break;
                            case '>':
                                if (!($queryResult > $displayComparisonValue)) {
                                    $displayBar = false;
                                }
                                break;
                        }
                        /*$replacementParams["{$threadCountRule['displayKey']}"] = my_number_format(
                            $queryResult
                        );*/
                    } elseif (!$queryResult) {
                        $displayBar = false;
                    }

                    if (isset($threadCountRule['displayKey']) && my_strlen($threadCountRule['displayKey']) > 2) {
                        $replacementParams["{{$threadCountRule['displayKey']}}"] = my_number_format(
                            $queryResult
                        );
                    }

                    // we only allow single forum rule for the time being
                    break;
                }
            }
        }

        if (!$displayBar) {
            continue;
        }

        $announcementBar = announcementBuildBar(
            $announcementData,
            $announcementID,
            replacementParams: $replacementParams
        );

        $announcementsList[] = eval(getTemplate('globalAnnouncements'));

        if ($announcementsLimit !== 0 && count($announcementsList) >= $announcementsLimit) {
            break;
        }
    }

    if (!$announcementsList) {
        return $pageContents;
    }

    $javaScript = announcementBuildJavaScript();

    $announcementsList = implode(',', $announcementsList);

    $announcementsList = eval(getTemplate('globalWrapper'));

    return str_replace('<!--OUGC_ANNBARS-->', $announcementsList, $pageContents);
}

function modcp_start(): void
{
    global $mybb, $lang;
    global $modcp_nav;

    $pageAction = getSetting('pageAction');

    $urlParams = ['action' => $pageAction];

    $pageUrl = urlHandlerBuild($urlParams);

    $hasPermission = is_member(getSetting('moderatorGroups'));

    if (my_strpos($modcp_nav, '<!--ougcAnnouncementBarsModeratorControlPanel-->') !== false && $hasPermission) {
        languageLoad();

        $modcp_nav = str_replace(
            '<!--ougcAnnouncementBarsModeratorControlPanel-->',
            eval(getTemplate('moderatorControlPanelNavigation')),
            $modcp_nav
        );
    }

    if ($mybb->get_input('action') !== $pageAction) {
        return;
    }

    $hasPermission || error_no_permission();

    languageLoad();

    $announcementID = $mybb->get_input('announcementID', MyBB::INPUT_INT);

    if ($mybb->get_input('manage') === 'delete') {
        verify_post_check($mybb->get_input('my_post_key'));

        $announcementData = announcementGet(["aid='{$announcementID}'"], ['name'], ['limit' => 1]);

        if (empty($announcementData)) {
            error_no_permission();
        }

        announcementDelete($announcementID);

        log_moderator_action(
            ['announcementID' => $announcementID, 'announcementName' => $announcementData['name']],
            'ougc_announcement_bars'
        );

        cacheUpdate();

        redirect($pageUrl, $lang->ougcAnnouncementBarsModeratorControlPanelRedirectDelete);
    }

    global $theme;
    global $headerinclude, $header, $footer;

    $groupsCache = $mybb->cache->read('usergroups');

    $forumsCache = cache_forums();

    $javaScript = announcementBuildJavaScript();

    if (in_array($mybb->get_input('manage'), ['new', 'edit'])) {
        $isEditPage = $mybb->get_input('manage') === 'edit';

        if ($isEditPage) {
            $announcementData = announcementGet(
                ["aid='{$announcementID}'"],
                [
                    'name',
                    'content',
                    'style',
                    'groups',
                    'forums',
                    'scripts',
                    'frules',
                    'startdate',
                    'enddate',
                    'disporder',
                    'dismissible',
                    'visible',
                ],
                ['limit' => 1]
            );

            if (empty($announcementData)) {
                error_no_permission();
            }

            $tableTitle = $lang->ougcAnnouncementBarsModeratorControlPanelNewEditTableTitleEdit;

            $buttonText = $lang->ougcAnnouncementBarsModeratorControlPanelNewEditButtonEdit;
        } else {
            $announcementData = [
                'name' => '',
                'content' => '',
                'style' => 'black',
                'groups' => -1,
                'forums' => -1,
                'scripts' => '',
                'frules' => '',
                'startdate' => 0,
                'enddate' => 0,
                'disporder' => 1,
                'dismissible' => 1,
                'visible' => 1,
            ];

            $tableTitle = $lang->ougcAnnouncementBarsModeratorControlPanelNewEditTableTitleNew;

            $buttonText = $lang->ougcAnnouncementBarsModeratorControlPanelNewEditButtonNew;
        }

        if ($mybb->request_method === 'post') {
            $announcementData = array_merge($announcementData, $mybb->input);
        }

        if (!isset($mybb->input['styleClassType'])) {
            if (in_array($announcementData['style'], STYLES, true)) {
                $mybb->input['styleClassType'] = 1;

                $announcementData['styleClassSelect'] = $announcementData['style'];
            } else {
                $mybb->input['styleClassType'] = 2;

                $announcementData['styleClassCustom'] = $announcementData['style'];
            }
        }

        if (!isset($mybb->input['displayGroupsType'])) {
            if ((int)$announcementData['groups'] === -1) {
                $mybb->input['displayGroupsType'] = 1;
            } elseif (!empty($announcementData['groups'])) {
                $mybb->input['displayGroupsType'] = 2;

                $mybb->input['displayGroupsSelect'] = explode(',', $announcementData['groups']);
            } else {
                $mybb->input['displayGroupsType'] = 3;
            }
        }

        if (!isset($mybb->input['displayForumsType'])) {
            if ((int)$announcementData['forums'] === -1) {
                $mybb->input['displayForumsType'] = 1;
            } elseif (!empty($announcementData['forums'])) {
                $mybb->input['displayForumsType'] = 2;

                $mybb->input['displayForumsSelect'] = explode(',', $announcementData['forums']);
            } else {
                $mybb->input['displayForumsType'] = 3;
            }
        }

        if (!isset($mybb->input['displayScriptsType'])) {
            if ((int)$announcementData['scripts'] === -1) {
                $mybb->input['displayScriptsType'] = 1;
            } else {
                $mybb->input['displayScriptsType'] = 2;
            }
        }

        $inputData = [
            'name' => $announcementData['name'],
            'message' => $announcementData['content'],
            'style' => $announcementData['style'],
            'styleClassSelect' => $announcementData['styleClassSelect'] ?? '',
            'styleClassCustom' => $announcementData['styleClassCustom'] ?? '',
            'groups' => $announcementData['groups'] ?? '',
            'groupsSelect' => $mybb->get_input('displayGroupsSelect', MyBB::INPUT_ARRAY),
            'forums' => $announcementData['forums'] ?? '',
            'forumsSelect' => $mybb->get_input('displayForumsSelect', MyBB::INPUT_ARRAY),
            'scripts' => $announcementData['scripts'] ?? '',
            'startDate' => $announcementData['startdate'],
            'endDate' => $announcementData['enddate'],
            'displayRules' => $announcementData['frules'] ?? '',
            'displayOrder' => $announcementData['disporder'],
            'dismissible' => $announcementData['dismissible'],
            'visible' => $announcementData['visible'],
        ];

        if (!empty($inputData['startDate']) && is_numeric($inputData['startDate'])) {
            $inputData['startDate'] = (new DateTimeImmutable())->setTimestamp((int)$inputData['startDate'])->format(
                getSetting('inputTimeFormat')
            );
        }

        if (!empty($inputData['endDate']) && is_numeric($inputData['endDate'])) {
            $inputData['endDate'] = (new DateTimeImmutable())->setTimestamp((int)$inputData['endDate'])->format(
                getSetting('inputTimeFormat')
            );
        }

        $errorMessages = [];

        if ($mybb->request_method === 'post') {
            $inputData['name'] = trim($inputData['name']);

            if (my_strlen($inputData['name']) < 1 || my_strlen($inputData['name']) > 100) {
                $errorMessages[] = $lang->ougcAnnouncementBarsModeratorControlPanelNewEditErrorInvalidName;
            }

            $inputData['message'] = trim($inputData['message']);

            if (!$inputData['message']) {
                $errorMessages[] = $lang->ougcAnnouncementBarsModeratorControlPanelNewEditErrorInvalidContent;
            }

            if (!empty($inputData['displayRules']) && !json_decode($inputData['displayRules'])) {
                $errorMessages[] = $lang->ougcAnnouncementBarsModeratorControlPanelNewEditErrorInvalidDisplayRules;
            }

            if (!$errorMessages && !$mybb->get_input('preview')) {
                $insertData = [
                    'name' => $inputData['name'],
                    'content' => $inputData['message'],
                    'scripts' => $inputData['scripts'],
                    'disporder' => max($inputData['displayOrder'], 0),
                    'dismissible' => $inputData['dismissible'],
                    'visible' => $inputData['visible'],
                ];

                if ($mybb->get_input('styleClassType', MyBB::INPUT_INT) === 1) {
                    $insertData['style'] = $inputData['styleClassSelect'];
                } elseif ($mybb->get_input('styleClassType', MyBB::INPUT_INT) === 2) {
                    $insertData['style'] = $inputData['styleClassCustom'];
                }

                if ($mybb->get_input('displayGroupsType', MyBB::INPUT_INT) === 1) {
                    $insertData['groups'] = -1;
                } elseif ($mybb->get_input('displayGroupsType', MyBB::INPUT_INT) === 2) {
                    $insertData['groups'] = implode(',', $inputData['groupsSelect']);
                } elseif ($mybb->get_input('displayGroupsType', MyBB::INPUT_INT) === 3) {
                    $insertData['groups'] = '';
                }

                if ($mybb->get_input('displayForumsType', MyBB::INPUT_INT) === 1) {
                    $insertData['forums'] = -1;
                } elseif ($mybb->get_input('displayForumsType', MyBB::INPUT_INT) === 2) {
                    $insertData['forums'] = implode(',', $inputData['forumsSelect']);
                } elseif ($mybb->get_input('displayForumsType', MyBB::INPUT_INT) === 3) {
                    $insertData['forums'] = '';
                }

                if (!empty($inputData['displayRules']) && json_decode($inputData['displayRules'])) {
                    $insertData['frules'] = $inputData['displayRules'];
                }

                if (!empty($inputData['startDate'])) {
                    $insertData['startdate'] = (new DateTimeImmutable(
                        "{$inputData['startDate']} 00:00:00"
                    ))->getTimestamp();
                }

                if (!empty($inputData['endDate'])) {
                    $insertData['enddate'] = (new DateTimeImmutable(
                        "{$inputData['endDate']} 00:00:00"
                    ))->getTimestamp();
                }

                if ($isEditPage) {
                    announcementUpdate($insertData, $announcementID);

                    log_moderator_action(
                        ['announcementID' => $announcementID, 'announcementName' => $announcementData['name']],
                        'ougc_announcement_bars'
                    );

                    cacheUpdate();

                    redirect($pageUrl, $lang->ougcAnnouncementBarsModeratorControlPanelRedirectEdit);
                }

                announcementInsert($insertData);

                log_moderator_action(
                    ['announcementID' => $announcementID, 'announcementName' => $announcementData['name']],
                    'ougc_announcement_bars'
                );

                cacheUpdate();

                redirect($pageUrl, $lang->ougcAnnouncementBarsModeratorControlPanelRedirectNew);
            }
        }

        $preview = '';

        if (!$errorMessages && $mybb->get_input('preview')) {
            $announcementBar = announcementBuildBar($announcementData, $announcementID, false);

            $preview = eval(getTemplate('moderatorControlPanelNewEditPreview'));
        }

        $errorMessages = $errorMessages ? inline_error($errorMessages) : '';

        $codeButtons = build_mycode_inserter();

        $styleClassSelect = (function () use ($mybb, $lang, $inputData): string {
            $name = 'styleClassSelect';

            $selectOptions = '';

            foreach (STYLES as $value) {
                $option = 'ougcAnnouncementBarsModeratorControlPanelNewEditTableHeaderStyleClass' . ucfirst($value);

                $option = $lang->{$option};

                $selectedElement = '';

                if ($value === $inputData['styleClassSelect']) {
                    $selectedElement = 'selected="selected"';
                }

                $multipleElement = '';

                $selectOptions .= eval(getTemplate('moderatorControlPanelSelectOption'));
            }

            return eval(getTemplate('moderatorControlPanelSelect'));
        })();

        $checkedElementStyleClassPredefined = $checkedElementStyleClassCustom = '';

        if ($mybb->get_input('styleClassType', MyBB::INPUT_INT) === 1) {
            $checkedElementStyleClassPredefined = 'checked="checked"';
        } else {
            $checkedElementStyleClassCustom = 'checked="checked"';
        }

        $displayGroupsSelect = (function () use ($mybb, $lang, $groupsCache, $inputData): string {
            $name = 'displayGroupsSelect[]';

            $selectOptions = '';

            foreach ($groupsCache as $value => $groupData) {
                $option = htmlspecialchars_uni($groupData['title']);

                $selectedElement = '';

                if (in_array($value, $inputData['groupsSelect'])) {
                    $selectedElement = 'selected="selected"';
                }

                $multipleElement = 'multiple="multiple"';

                $selectOptions .= eval(getTemplate('moderatorControlPanelSelectOption'));
            }

            return eval(getTemplate('moderatorControlPanelSelect'));
        })();

        $checkedElementDisplayGroupsAll = $checkedElementDisplayGroupsCustom = $checkedElementDisplayGroupsNone = '';

        if ($mybb->get_input('displayGroupsType', MyBB::INPUT_INT) === 1) {
            $checkedElementDisplayGroupsAll = 'checked="checked"';
        } elseif ($mybb->get_input('displayGroupsType', MyBB::INPUT_INT) === 2) {
            $checkedElementDisplayGroupsCustom = 'checked="checked"';
        } else {
            $checkedElementDisplayGroupsNone = 'checked="checked"';
        }

        $displayForumsSelect = (function () use ($mybb, $lang, $forumsCache, $inputData): string {
            $name = 'displayForumsSelect[]';

            $selectOptions = '';

            foreach ($forumsCache as $value => $forumData) {
                $option = htmlspecialchars_uni(strip_tags($forumData['name']));

                $selectedElement = '';

                if (in_array($value, $inputData['forumsSelect'])) {
                    $selectedElement = 'selected="selected"';
                }

                $multipleElement = 'multiple="multiple"';

                $selectOptions .= eval(getTemplate('moderatorControlPanelSelectOption'));
            }

            return eval(getTemplate('moderatorControlPanelSelect'));
        })();

        $checkedElementDisplayForumsAll = $checkedElementDisplayForumsCustom = $checkedElementDisplayForumsNone = '';

        if ($mybb->get_input('displayForumsType', MyBB::INPUT_INT) === 1) {
            $checkedElementDisplayForumsAll = 'checked="checked"';
        } elseif ($mybb->get_input('displayForumsType', MyBB::INPUT_INT) === 2) {
            $checkedElementDisplayForumsCustom = 'checked="checked"';
        } else {
            $checkedElementDisplayForumsNone = 'checked="checked"';
        }

        $checkedElementAllowDismissalNo = $checkedElementAllowDismissalYes = '';

        if ($inputData['dismissible']) {
            $checkedElementAllowDismissalYes = 'checked="checked"';
        } else {
            $checkedElementAllowDismissalNo = 'checked="checked"';
        }

        $checkedElementAllowEnabledNo = $checkedElementAllowEnabledYes = '';

        if ($inputData['visible']) {
            $checkedElementAllowEnabledYes = 'checked="checked"';
        } else {
            $checkedElementAllowEnabledNo = 'checked="checked"';
        }

        $inputData = array_map('htmlspecialchars_uni', $inputData);

        $pageContents = eval(getTemplate('moderatorControlPanelNewEdit'));

        $pageContents = eval(getTemplate('moderatorControlPanel'));

        output_page($pageContents);

        exit;
    }

    $announcementsList = '';

    foreach (
        announcementGet(
            [],
            [
                'aid',
                'name',
                'content',
                'style',
                'groups',
                'forums',
                'scripts',
                'frules',
                'startdate',
                'enddate',
                'dismissible',
                'visible',
            ],
            ['order_by' => 'disporder']
        ) as $announcementID => $announcementData
    ) {
        $announcementName = htmlspecialchars_uni($announcementData['name']);

        $displayGroups = $lang->ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayGroupsAll;

        if ($announcementData['groups'] === '') {
            $displayGroups = $lang->ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayGroupsNone;
        } elseif ((int)$announcementData['groups'] !== -1) {
            $displayGroups = [];

            foreach (explode(',', $announcementData['groups']) as $groupID) {
                if (!empty($groupsCache[$groupID]['title'])) {
                    $displayGroups[] = htmlspecialchars_uni($groupsCache[$groupID]['title']);
                }
            }

            $displayGroups = implode($lang->comma, $displayGroups);
        }

        $displayForums = $lang->ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayForumsAll;

        if ($announcementData['forums'] === '') {
            $displayForums = $lang->ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayForumsNone;
        } elseif ((int)$announcementData['forums'] !== -1) {
            $displayForums = [];

            foreach (explode(',', $announcementData['forums']) as $groupID) {
                if (!empty($forumsCache[$groupID]['name'])) {
                    $displayForums[] = htmlspecialchars_uni(strip_tags($forumsCache[$groupID]['name']));
                }
            }

            $displayForums = implode($lang->comma, $displayForums);
        }

        $displayScripts = $lang->ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayForumsAll;

        if ($announcementData['forums'] === '') {
            $displayScripts = $lang->ougcAnnouncementBarsModeratorControlPanelTableHeaderDisplayForumsNone;
        } elseif ((int)$announcementData['forums'] !== -1) {
            $displayScripts = [];

            foreach (explode(',', $announcementData['forums']) as $groupID) {
                if (!empty($forumsCache[$groupID]['name'])) {
                    $displayScripts[] = htmlspecialchars_uni(strip_tags($forumsCache[$groupID]['name']));
                }
            }

            $displayScripts = implode($lang->comma, $displayScripts);
        }

        $announcementBar = announcementBuildBar($announcementData, $announcementID, false);

        $editUrl = urlHandlerBuild(
            array_merge(
                $urlParams,
                ['manage' => 'edit', 'announcementID' => $announcementID]
            )
        );

        $deleteUrl = urlHandlerBuild(
            array_merge(
                $urlParams,
                ['manage' => 'delete', 'announcementID' => $announcementID, 'my_post_key' => $mybb->post_code]
            )
        );

        $announcementsList .= eval(getTemplate('moderatorControlPanelTableRow'));
    }

    if (!$announcementsList) {
        $announcementsList = eval(getTemplate('moderatorControlPanelTableEmpty'));
    }

    $newUrl = urlHandlerBuild(
        array_merge(
            $urlParams,
            ['manage' => 'new']
        )
    );

    $pageContents = eval(getTemplate('moderatorControlPanelTable'));

    $pageContents = eval(getTemplate('moderatorControlPanel'));

    output_page($pageContents);

    exit;
}

function modcp_modlogs_result(): void
{
    global $logitem;

    if ($logitem['action'] !== 'ougc_announcement_bars') {
        return;
    }

    global $lang;

    languageLoad();

    $logitem['action'] = $lang->ougcAnnouncementBarsModeratorControlPanelLogActionDelete;

    $data = my_unserialize($logitem['data']);

    if (empty($data['announcementID'])) {
        return;
    }

    global $information;

    $information .= $lang->sprintf(
        $lang->ougcAnnouncementBarsModeratorControlPanelLogInformation,
        $data['announcementID'],
        $data['announcementName']
    );
}
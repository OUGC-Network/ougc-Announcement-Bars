<p align="center">
    <a href="" rel="noopener">
        <img width="700" height="400" src="https://github.com/OUGC-Network/OUGC-Announcement-Bars/assets/1786584/e3645efa-3585-4467-aa6b-e129079d60ba" alt="Project logo">
    </a>
</p>

<h3 align="center">ougc Announcement Bars</h3>

<div align="center">

[![Status](https://img.shields.io/badge/status-active-success.svg)]()
[![GitHub Issues](https://img.shields.io/github/issues/OUGC-Network/OUGC-Announcement-Bars.svg)](./issues)
[![GitHub Pull Requests](https://img.shields.io/github/issues-pr/OUGC-Network/OUGC-Announcement-Bars.svg)](./pulls)
[![License](https://img.shields.io/badge/license-GPL-blue)](/LICENSE)

</div>

---

<p align="center"> Manage announcement notification bars with multiple display rules.
    <br> 
</p>

## 📜 Table of Contents <a name = "table_of_contents"></a>

- [About](#about)
- [Getting Started](#getting_started)
    - [Dependencies](#dependencies)
    - [File Structure](#file_structure)
    - [Install](#install)
    - [Update](#update)
    - [Template Modifications](#template_modifications)
- [Usage](#usage)
- [Built Using](#built_using)
- [Authors](#authors)
- [Acknowledgments](#acknowledgement)
- [Support & Feedback](#support)

## 🚀 About <a name = "about"></a>

Announcement Bars is the must-have plugin for managing announcement bars on your MyBB forum! With this user-friendly
tool, administrators can create and manage announcement bars, keeping users informed and engaged. Customize visibility
permissions per group, script, date, or advanced display rules for each bar, ensuring targeted messaging to specific
user segments. Plus, empower users with the option to dismiss announcements on a per-bar basis, providing a seamless
browsing experience.

[Go up to Table of Contents](#table_of_contents)

## 📍 Getting Started <a name = "getting_started"></a>

The following information will assist you into getting a copy of this plugin up and running on your forum.

### Dependencies <a name = "dependencies"></a>

A setup that meets the following requirements is necessary to use this plugin.

- [MyBB](https://mybb.com/) >= 1.8
- PHP >= 7.0
- [PluginLibrary for MyBB](https://github.com/frostschutz/MyBB-PluginLibrary) >= 13

### File structure <a name = "file_structure"></a>

  ```
   .
   ├── inc
   │ ├── languages
   │ │ ├── english
   │ │ │ ├── admin
   │ │ │ │ ├── ougc_annbars.lang.php
   │ │ │ ├── ougc_annbars.lang.php
   │ ├── plugins
   │ │ ├── ougc_custompromotionfield.php
   ```

### Installing <a name = "install"></a>

Follow the next steps in order to install a copy of this plugin on your forum.

1. Download the latest package from the [MyBB Extend](https://community.mybb.com/mods.php?action=view&pid=134) site or
   from the [repository releases](https://github.com/OUGC-Network/OUGC-Announcement-Bars/releases/latest).
2. Upload the contents of the _Upload_ folder to your MyBB root directory.
3. Browse to _Configuration » Plugins_ and install this plugin by clicking _Install & Activate_.

### Updating <a name = "update"></a>

Follow the next steps in order to update your copy of this plugin.

1. Browse to _Configuration » Plugins_ and deactivate this plugin by clicking _Deactivate_.
2. Follow step 1 and 2 from the [Install](#install) section.
3. Browse to _Configuration » Plugins_ and activate this plugin by clicking _Activate_.

[Go up to Table of Contents](#table_of_contents)

### Template Modifications <a name = "template_modifications"></a>

To display the announcements bars it is required that you edit the following template for each of your themes.

1. Place `<!--OUGC_ANNBARS-->` after `<navigation>` in the `header` template.
   Alternatively, it is possible to place this code in almost any template to display the announcement bars.
2. Place `<!--ougcAnnouncementBarsModeratorControlPanel-->` after `{$nav_modlogs}` in the `modcp_nav_forums_posts`
   template to display the moderator control panel link.

[Go up to Table of Contents](#table_of_contents)

## 📖 Usage <a name="usage"></a>

### Display Rules

Custom display rules are an advanced feature that allow you to filter the display of announcement bars or render dynamic
values within their messages.

Below is a JSON format list of conditionals to manipulate the display of this announcement bars.

#### Filter based on thread count

Only display the announcement bar if the thread count from forums `1` and `2`, counting only approved (visible) threads,
from the last `30` days, is greater than `0`.

```JSON
{
  "threadCountRule": {
    "forumIDs": [1, 2],
    "closedThreads": false,
    "visibleThreads": true,
    "unapprovedThreads": false,
    "deletedThreads": false,
    "createDaysCut": 30,
    "displayComparisonOperator": ">",
    "displayComparisonValue": 0,
    "displayKey": "exampleCounter"
  }
}
```

You can now use `{exampleCounter}` inside "Message" to display the count result.

[Go up to Table of Contents](#table_of_contents)

## ⛏ Built Using <a name = "built_using"></a>

- [MyBB](https://mybb.com/) - Web Framework
- [MyBB PluginLibrary](https://github.com/frostschutz/MyBB-PluginLibrary) - A collection of useful functions for MyBB
- [PHP](https://www.php.net/) - Server Environment

[Go up to Table of Contents](#table_of_contents)

## ✍️ Authors <a name = "authors"></a>

- [@Omar G](https://github.com/Sama34) - Idea & Initial work

See also the list of [contributors](https://github.com/OUGC-Network/OUGC-Announcement-Bars/contributors)
who participated
in this project.

[Go up to Table of Contents](#table_of_contents)

## 🎉 Acknowledgements <a name = "acknowledgement"></a>

- [The Documentation Compendium](https://github.com/kylelobo/The-Documentation-Compendium)

[Go up to Table of Contents](#table_of_contents)

## 🎈 Support & Feedback <a name="support"></a>

This is free development and any contribution is welcome. Get support or leave feedback at the
official [MyBB Community](https://community.mybb.com/thread-221815.html).

Thanks for downloading and using our plugins!

[Go up to Table of Contents](#table_of_contents)
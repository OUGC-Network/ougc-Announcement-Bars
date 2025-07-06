/***************************************************************************
 *
 *    ougc Announcement Bars plugin (/jscripts/ougc_annbars.js)
 *    Author: Omar Gonzalez
 *    Copyright: © 2012 - 2020 Omar Gonzalez
 *
 *    Website: https://ougc.network
 *
 *    Manage custom announcement notifications that render to users in the page.
 *
 ***************************************************************************

 ****************************************************************************
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 ****************************************************************************/

const ougcAnnouncementBars = {
    init: function () {
        const AnnouncementItems = document.querySelectorAll('[id^="announcementBarItem"]');

        AnnouncementItems.forEach(AnnouncementItem => {
            const AnnouncementItemIdentifier = AnnouncementItem.id;

            const CookieVal = Cookie.get(AnnouncementItemIdentifier);

            if (CookieVal) {

                if (CookieVal < parseInt(ougcAnnouncementBarsCutoffDays) || ougcAnnouncementBarsDebug) {
                    Cookie.unset(AnnouncementItemIdentifier);
                } else {
                    AnnouncementItem.style.display = 'none';
                }
            }

            const DismissElement = document.querySelector('#' + AnnouncementItemIdentifier + ' .ougcAnnouncementBarsDismissNotice');

            if (DismissElement) {
                DismissElement.addEventListener('click', (event) => {

                    const fadeEffect = setInterval(function () {
                        if (!AnnouncementItem.style.opacity) {
                            AnnouncementItem.style.opacity = '1';
                        }

                        if (AnnouncementItem.style.opacity > 0) {
                            AnnouncementItem.style.opacity -= '0.05';
                        } else {
                            clearInterval(fadeEffect);

                            AnnouncementItem.style.display = 'none';

                            Cookie.set(AnnouncementItemIdentifier, parseInt(ougcAnnouncementBarsTimeNow));
                        }
                    }, 10);
                });
            }
        })
    },

    checkAction: function (identifier) {
        let checked = '';

        const InputElements = document.querySelectorAll('.' + identifier + 'Input');

        InputElements.forEach(InputElement => {
            if (InputElement.checked) {
                checked = document.querySelector('.' + identifier + 'Input:checked').value;
            }
        })

        const InputDefinitions = document.querySelectorAll('.' + identifier + 'Definition');

        InputDefinitions.forEach(InputElement => {
            InputElement.style.display = 'none';
        })

        const CheckedDefinition = document.getElementById(identifier + checked);

        if (CheckedDefinition) {
            CheckedDefinition.style.display = '';
        }
    }
};

ougcAnnouncementBars.init();
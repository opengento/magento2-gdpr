/**
 * This file is part of the Flurrybox EnhancedPrivacy package.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Flurrybox EnhancedPrivacy
 * to newer versions in the future.
 *
 * @copyright Copyright (c) 2018 Flurrybox, Ltd. (https://flurrybox.com/)
 * @license   GNU General Public License ("GPL") v3.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
define([
    "jquery",
    "uiComponent",
    "ko",
    "mage/cookies"
], function ($, Component, ko) {
    "use strict";

    return Component.extend({
        showPopUp: ko.observable(false),

        defaults: {
            template: "Opengento_Gdpr/message"
        },

        /**
         * Initialize component
         */
        initialize() {
            var self = this,
                isBot = navigator.userAgent.toLowerCase().match( /.+?(?:bot|lighthouse)/ );

            this._super();

            if (!window.localStorage.getItem(self.cookieName) && !isBot) {
                self.showPopUp(true);
            }
        },

        /**
         * Get Popup Text
         * @returns {string}
         */
        getPopupText() {
            var self = this;

            return self.notificationText;
        },

        /**
         * Get Popup Link
         * @returns {string}
         */
        getPopupLink() {
            var self = this;

            return self.learnMore;
        },

        /**
         * Accept All Cookies
         * @returns {void}
         */
        acceptAllCookies() {
            var self = this;

            window.localStorage.setItem(self.cookieName, true, {});
            self.showPopUp(false);
        }
    });
});

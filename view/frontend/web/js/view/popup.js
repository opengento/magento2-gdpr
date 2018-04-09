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
    'jquery',
    'uiComponent',
    'ko',
    'mage/cookies'
], function ($, Component, ko) {
    'use strict';

    return Component.extend({
        showPopUp: ko.observable(null),
        popupText: ko.observable(null),

        defaults: {
            template: 'Flurrybox_EnhancedPrivacy/message'
        },

        /**
         * Initialize component.
         */
        initialize: function () {
            this._super();

            this.showPopUp(!$.cookie(this.cookieName));
            this.popupText(this.notificationText);

            $(document).on('click', '#enhanced-privacy-popup-agree', function () {
                this.showPopUp(false);
                $.cookie(this.cookieName, 1);
            }.bind(this));
        },

        /**
         * Get URL to information page.
         *
         * @returns {*}
         */
        getLearnMoreLink: function () {
            return this.learnMore;
        }
    });
});

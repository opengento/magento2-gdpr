/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
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
            template: 'Opengento_Gdpr/message'
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

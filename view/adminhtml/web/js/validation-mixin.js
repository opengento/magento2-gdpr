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
    'jquery'
], function ($) {
    'use strict';

    return function () {
        $.validator.addMethod(
            'validate-greater-than-zero',
            function (v) {
                v = parseNumber(v);

                return !isNaN(v) && v > 0;
            },
            $.mage.__('Please enter a number greater than 0 in this field.')
        );
    }
});

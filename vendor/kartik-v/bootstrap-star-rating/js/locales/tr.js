/*!
 * @see http://github.com/kartik-v/bootstrap-star-rating
 * @author Oguz Külcü <grafikcoder@gmail.com>
 * Turkish Language
 */
(function (factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        define(['jquery', 'window', 'document'], factory);
    } else if (typeof module === 'object' && typeof module.exports === 'object') { 
        factory(require('jquery'), window, document);
    } else { 
        factory(window.jQuery, window, document);
    }
}(function ($, window, document, undefined) {
    "use strict";
    $.fn.ratingLocales['tr'] = {
        defaultCaption: '{rating} Yıldız',
        starCaptions: {
            0.5: 'Yarım Yıldız',
            1: 'Tek Yıldız',
            1.5: 'Bir Buçuk Yıldız',
            2: 'İki Yıldız',
            2.5: 'İki Buçuk Yıldız',
            3: 'Üç Yıldız',
            3.5: 'Üç Buçuk Yıldız',
            4: 'Dört Yıldız',
            4.5: 'Dört Buçuk Yıldız',
            5: 'Beş Yıldız'
        },
        clearButtonTitle: 'Temizle',
        clearCaption: 'Oylanmamış'
    };
}));

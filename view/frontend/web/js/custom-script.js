define(["jquery"], function ($) {
    "use strict";

    const originalTrigger = $.fn.trigger;

    $.fn.trigger = function (event, data) {
        if (typeof event === "string") {
            const nativeEvent = new CustomEvent(event, { detail: data });

            this.each(function () {
                this.dispatchEvent(nativeEvent);
            });
        }

        return originalTrigger.apply(this, arguments);
    };
});

define(["jquery"], function ($) {
    "use strict";

    const originalTrigger = $.fn.trigger;

    $.fn.trigger = function (event, detail) {
        if (typeof event === "string") {
            const nativeEvent = new CustomEvent(event, { detail });

            this.each(function () {
                this.dispatchEvent(nativeEvent);
            });
        }

        return originalTrigger.apply(this, arguments);
    };
});

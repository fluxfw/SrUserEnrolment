il.MultiLineNewInputGUI = {
    /**
     * @param {jQuery} el
     */
    add: function (el) {
        var cloned_el = el.clone();

        $("[name]", el).each(function (i2, el2) {
            el2.value = "";
            if ("checked" in el2) {
                el2.checked = false;
            }
        });

        $(".alert", el).remove();

        this.init(cloned_el);

        el.before(cloned_el);

        this.update(el.parent());
    },

    cached_options: [],

    /**
     * @param {jQuery} el
     * @param {string} type
     * @param {Object} options
     */
    cacheOptions(el, type, options) {
        this.cached_options.push({
            type: type,
            options: options
        });

        el.attr("data-cached_options_id", (this.cached_options.length - 1));
    },

    /**
     * @param {jQuery} el
     */
    down: function (el) {
        el.insertAfter(el.next());

        this.update(el.parent());
    },

    /**
     * @param {jQuery} el
     */
    init: function (el) {
        $("span[data-action]", el).each(function (i, action_el) {
            action_el = $(action_el);

            action_el.off();

            action_el.on("click", this[action_el.data("action")].bind(this, el))
        }.bind(this));

        $(".input-group.date:not([data-cached_options_id])", el).each(function (i2, el2) {
            el2 = $(el2);

            if (el2.data("DateTimePicker")) {
                this.cacheOptions(el2, "datetimepicker", el2.datetimepicker("options"));

                el2.datetimepicker("destroy");

                el2.id = "";
            }
        }.bind(this));

        $("[data-cached_options_id]", el).each(function (i2, el2) {
            el2 = $(el2);

            const options = this.cached_options[el2.attr("data-cached_options_id")];
            if (!options) {
                return;
            }
            switch (options.type) {
                case "datetimepicker":
                    el2.datetimepicker(options.options);
                    break;

                default:
                    break;
            }
        }.bind(this));
    },

    /**
     * @param {jQuery} el
     */
    remove: function (el) {
        var parent = el.parent();

        if (parent.children().length > 1) {
            el.remove();

            this.update(parent);
        }
    },

    /**
     * @param {jQuery} el
     */
    up: function (el) {
        el.insertBefore(el.prev());

        this.update(el.parent());
    },

    /**
     * @param {jQuery} el
     */
    update: function (el) {
        for (const key of ["aria-controls", "aria-labelledby", "href", "id", "name"]) {
            el.children().each(function (i, el) {
                $("[" + key + "]", el).each(function (i2, el2) {
                    for (const [char_open, char_close] of [["[", "]["], ["__", "__"]]) {
                        el2.attributes[key].value = el2.attributes[key].value.replace(new RegExp(char_open.replace(/./g, "\\$&") + "[0-9]+" + char_close.replace(/./g, "\\$&")), char_open + i + char_close);
                    }
                }.bind(this));
            }.bind(this));
        }
    }
};

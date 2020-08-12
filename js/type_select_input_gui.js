(() => {
    if (il.int_type_selectinput_observer) {
        return;
    }
    il.int_type_selectinput_observer = true;

    const observer = new MutationObserver((events) => {
        /**
         * @type {MutationRecord} event
         */
        for (const event of events) {
            //console.log(event);
            for (const node of event.addedNodes) {
                if (node instanceof Element) { // Some node types like text missing some methods, this can be ignored
                    for (const child_node of [node, ...node.querySelectorAll("*")]) { // Check item self and needs also to check all children because if only insert a new parent node with HTML, the needed node is missing in addedNodes
                        if (checkNode(child_node)) {
                            initNode(child_node);
                        }
                    }
                }
            }
        }
    });

    observer.observe(document.documentElement, {
        childList: true,
        subtree: true
    });

    /**
     * @param {Node} node
     *
     * @returns {boolean}
     */
    function checkNode(node) {
        return (node instanceof HTMLSelectElement && node.classList.contains("form-control") && node.name.includes("type"));
    }

    /**
     * @param {HTMLSelectElement} node
     */
    function initNode(node) {
        if (node._init_selectinput) {
            return;
        }
        node._init_selectinput = true;

        //console.log(node);

        let input;
        if (node.parentElement.nextElementSibling) {
            input = node.parentElement.nextElementSibling.children[1];
        } else {
            input = node.parentElement.parentElement.nextElementSibling.children[1].children[0];
        }

        input.addEventListener("focus", inputAutoComplete);

        /**
         *
         */
        function inputAutoComplete() {
            $(input).iladvancedautocomplete("instance").options.requestUrl.searchParams.set("type", node.value); // Set type change to autocomplete url

            input.dispatchEvent(new Event("keydown")); // Simulate keydown event to open popup on focus input
        }
    }
})();

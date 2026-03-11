(function () {
    "use strict";

    const init = () => {
        createIcons({
            icons,
            "stroke-width": 1.5,
            nameAttr: "data-lucide",
        });
    };

    init();

    // Re-initialize icons after Livewire updates the DOM
    document.addEventListener("livewire:load", init);
    document.addEventListener("livewire:navigated", init);

    if (window.Livewire) {
        Livewire.hook("morph.updated", () => {
            init();
        });
    } else {
        document.addEventListener("livewire:init", () => {
            Livewire.hook("morph.updated", () => {
                init();
            });
        });
    }
})();

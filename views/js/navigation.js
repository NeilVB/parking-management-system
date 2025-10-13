document.addEventListener("DOMContentLoaded", () => {
    // navigation.js
    const navLinks = document.querySelectorAll("[data-target]");
    const sections = document.querySelectorAll(".content-section");

    function showSection(id) {
        sections.forEach(s => s.classList.remove("active"));
        document.querySelectorAll(".nav-item").forEach(i => i.classList.remove("active"));

        const section = document.getElementById(id);
        if (section) section.classList.add("active");

        const navItem = document.querySelector(`.nav-item a[data-target="${id}"]`);
        if (navItem) navItem.parentElement.classList.add("active");
    }

    navLinks.forEach(link => {
        link.addEventListener("click", e => {
            e.preventDefault();
            const target = link.getAttribute("data-target");
            showSection(target);

            const url = new URL(window.location);
            url.searchParams.set("section", target);
            window.history.replaceState({}, '', url);
        });
    });

    // Only used for the "Back" button now
    window.hideAllSections = () => {
        sections.forEach(s => s.classList.remove("active"));
        document.querySelectorAll(".nav-item").forEach(i => i.classList.remove("active"));
        history.replaceState({}, '', location.pathname);
    };

    document.addEventListener('click', e => {
        // Only hide sections when clicking the "Back" button
        if (e.target.matches('.btn-back')) {
            e.preventDefault();
            hideAllSections();
            return;
        }

        // Ignore clicks inside the active section — keep showing it
        const activeSection = document.querySelector(".content-section.active");
        if (activeSection && activeSection.contains(e.target)) return;

        // Ignore clicks anywhere else — do nothing
    });

    document.addEventListener('keydown', e => e.key === "Escape" && hideAllSections());

    const params = new URLSearchParams(window.location.search);
    const section = params.get("section");
    if (section && document.getElementById(section)) {
        showSection(section);
    }
});

document.addEventListener("DOMContentLoaded", () => {
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

            // Update the URL without reloading the page
            const url = new URL(window.location);
            url.searchParams.set("section", target);
            window.history.replaceState({}, '', url);
        });
    });

    const params = new URLSearchParams(window.location.search);
    const section = params.get("section") || "dashboard";
    showSection(section);
});
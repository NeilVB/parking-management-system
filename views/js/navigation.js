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
            showSection(link.getAttribute("data-target"));
        });
    });

    showSection("dashboard");
});

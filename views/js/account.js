document.addEventListener("DOMContentLoaded", () => {
    const tabButtons = document.querySelectorAll(".vehicle-tab-btn");
    const slots = document.querySelectorAll(".vehicle-slot");

    tabButtons.forEach(button => {
        button.addEventListener("click", () => {
            const targetId = button.getAttribute("data-tab");

            tabButtons.forEach(btn => btn.classList.remove("active"));
            button.classList.add("active");

            slots.forEach(slot => slot.classList.remove("active"));
            document.getElementById(targetId).classList.add("active");
        });
    });
});

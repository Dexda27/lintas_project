document.addEventListener("DOMContentLoaded", function () {
    lucide.createIcons();

    const toggleButtons = document.querySelectorAll('[data-toggle="password"]');

    toggleButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const inputId = this.getAttribute("data-target");
            const passwordInput = document.getElementById(inputId);
            const icon = this.querySelector("i");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.setAttribute("data-lucide", "eye-off");
            } else {
                passwordInput.type = "password";
                icon.setAttribute("data-lucide", "eye");
            }

            lucide.createIcons(); // re-render Lucide icons
        });
    });
});

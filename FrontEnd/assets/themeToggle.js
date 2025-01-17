export function initializeSidebarAndTheme() {
    const sidebarToggle = document.querySelector("#sidebar-toggle");
    const themeToggle = document.querySelector(".theme-toggle");

    // Sidebar toggle functionality
    sidebarToggle?.addEventListener("click", function () {
        document.querySelector("#sidebar")?.classList.toggle("collapsed");
    });

    // Theme toggle functionality
    themeToggle?.addEventListener("click", () => {
        toggleLocalStorage();
        toggleRootClass();
    });

    // Toggle the root class (light/dark mode)
    function toggleRootClass() {
        const current = document.documentElement.getAttribute('data-bs-theme');
        const inverted = current === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-bs-theme', inverted);
    }

    // Toggle the theme in localStorage
    function toggleLocalStorage() {
        if (isLight()) {
            localStorage.removeItem("light");
        } else {
            localStorage.setItem("light", "set");
        }
    }

    // Check if light theme is set in localStorage
    function isLight() {
        return localStorage.getItem("light");
    }

    // Initialize the theme if it's already set in localStorage
    if (isLight()) {
        toggleRootClass();
    }
    
}

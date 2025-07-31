document.addEventListener("DOMContentLoaded", function () {
  const hamburger = document.getElementById("hamburger-menu");
  const nav = document.getElementById("main-nav");
  const userDropdownToggle = document.querySelector(".user-dropdown-toggle");
  const userDropdown = document.querySelector(".user-dropdown");

  hamburger.addEventListener("click", function () {
    hamburger.classList.toggle("active");
    nav.classList.toggle("active");
    if (userDropdownToggle.classList.contains("active")) {
      userDropdownToggle.classList.remove("active");
    }
  });

  document.addEventListener("click", function (e) {
    if (
      !userDropdown.contains(e.target) &&
      !userDropdownToggle.contains(e.target)
    ) {
      userDropdownToggle.classList.remove("active");
    }
    if (
      !nav.contains(e.target) &&
      !hamburger.contains(e.target) &&
      nav.classList.contains("active")
    ) {
      hamburger.classList.remove("active");
      nav.classList.remove("active");
    }
  });
});

const userDropdownToggle = document.querySelector(".user-dropdown-toggle");
const userDropdown = document.querySelector(".user-dropdown");

userDropdownToggle.addEventListener("click", function () {
  userDropdownToggle.classList.toggle("active");
});

// Close the dropdown if clicked outside
document.addEventListener("click", function (event) {
  if (
    !userDropdownToggle.contains(event.target) &&
    userDropdown.classList.contains("active")
  ) {
    userDropdownToggle.classList.remove("active");
  }
});

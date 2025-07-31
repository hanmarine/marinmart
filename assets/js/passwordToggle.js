const togglePassword = document.getElementById("togglePassword");
const passwordInput = document.getElementById("password");

togglePassword.addEventListener("click", function () {
  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    togglePassword.src =
      "https://img.icons8.com/pastel-glyph/64/26344b/surprise--v2.png";
  } else {
    passwordInput.type = "password";
    togglePassword.src = "https://img.icons8.com/ios/50/26344b/closed-eye.png";
  }
});

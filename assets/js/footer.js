const footerId = document.getElementById("footer");
const getYear = (footer) => {
  const today = new Date();
  const year = today.getFullYear();
  footer.textContent = `© 2024-${year} Marinmart, All rights reserved.`;
};
getYear(footerId);

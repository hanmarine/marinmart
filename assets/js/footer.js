const footerId = document.getElementById("footer");
const getYear = (footer) => {
  const today = new Date();
  const year = today.getFullYear();
  footer.textContent = `© ${year} Marinmart, All rights reserved.`;
};
getYear(footerId);

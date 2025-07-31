function loadTable(tableName, page = 1, searchQuery = "") {
  fetch(
    `../functions/fetch_user_table.php?table=${tableName}&page=${page}&search=${searchQuery}`
  )
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("table-container").innerHTML = data;
    });
}

function loadPage(page) {
  const searchQuery = document.getElementById("searchInput").value;
  loadTable(currentTable, page, searchQuery);
}

document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  currentTable = params.get("table") || "product";
  const page = params.get("page") || 1;
  loadTable(currentTable, page);
});

document.getElementById("searchInput").addEventListener("input", () => {
  loadTable(currentTable, 1, document.getElementById("searchInput").value);
});

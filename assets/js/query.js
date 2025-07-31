function loadQuery(queryType, page = 1) {
  fetch(`../functions/fetch_query.php?type=${queryType}&page=${page}`)
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("query-container").innerHTML = data;
    });
}

function loadPage(queryType, page) {
  loadQuery(queryType, page);
}

document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  const queryType = params.get("type") || "complete";
  const page = params.get("page") || 1;
  loadQuery(queryType, page);
});

function showProductForm() {
  document.getElementById("productForm").style.display = "block";
  document.getElementById("supplierForm").style.display = "none";
  document.getElementById("categoryForm").style.display = "none";
}

function showSupplierForm() {
  document.getElementById("productForm").style.display = "none";
  document.getElementById("supplierForm").style.display = "block";
  document.getElementById("categoryForm").style.display = "none";
}

function showCategoryForm() {
  document.getElementById("productForm").style.display = "none";
  document.getElementById("supplierForm").style.display = "none";
  document.getElementById("categoryForm").style.display = "block";
}

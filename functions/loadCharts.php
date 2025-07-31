<script type="text/javascript">
    google.charts.load('current', {'packages': ['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var categoryData = google.visualization.arrayToDataTable([
            ['Category', 'Number'],
            <?php while ($row = mysqli_fetch_assoc($categoriesResult)) { ?>
                ['<?php echo $row['CategoryName']; ?>', <?php echo $row['product_count']; ?>],
            <?php } ?>
            ['Others', <?php echo $otherCategoriesCount; ?>]
        ]);

        var supplierData = google.visualization.arrayToDataTable([
            ['Supplier', 'Number'],
            <?php while ($row = mysqli_fetch_assoc($suppliersResult)) { ?>
                ['<?php echo $row['SupplierName']; ?>', <?php echo $row['product_count']; ?>],
            <?php } ?>
            ['Others', <?php echo $otherSuppliersCount; ?>]
        ]);

        var o1 = {
            title: 'TOP CATEGORIES', 
            titleTextStyle: {
                color: '#246af3' 
            },
            is3D: true,
            backgroundColor: 'transparent',
        };

        var o2 = {
            title: 'TOP SUPPLIERS', 
            titleTextStyle: {
                color: '#246af3' 
            },
            is3D: true,
            backgroundColor: 'transparent',
        };

        var chart1 = new google.visualization.PieChart(document.getElementById('chart1'));
        chart1.draw(categoryData, o1);

        var chart2 = new google.visualization.PieChart(document.getElementById('chart2'));
        chart2.draw(supplierData, o2);
    }
</script>
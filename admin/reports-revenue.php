<?php
$conn = new mysqli("localhost", "root", "", "muscle_city_gym");

$sql = "SELECT DATE_FORMAT(payment_date, '%M %Y') AS month, SUM(amount) AS total FROM payments GROUP BY month ORDER BY payment_date";
$result = $conn->query($sql);
?>

<h3>Monthly Revenue</h3>
<div class="chart-container">
    <canvas id="revenueChart" width="800" height="300"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js "></script>
<script>
const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
new Chart(ctxRevenue, {
    type: 'line',
    data: {
        labels: [<?php while($row = $result->fetch_assoc()) { echo "'".$row['month']."',"; } ?>],
        datasets: [{
            label: 'Revenue (PHP)',
            data: [<?php $result->data_seek(0); while($row = $result->fetch_assoc()) { echo $row['total'].","; } ?>],
            borderColor: '#ffc107',
            backgroundColor: '#ffc107',
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Monthly Revenue Trends'
            }
        }
    }
});
</script>
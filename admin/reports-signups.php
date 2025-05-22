<?php
$conn = new mysqli("localhost", "root", "", "muscle_city_gym");

$sql = "SELECT DATE_FORMAT(created_at, '%M %Y') AS month, COUNT(*) AS count FROM users WHERE role = 'user' GROUP BY month";
$result = $conn->query($sql);
?>

<h3>Monthly Member Sign-ups</h3>
<div class="chart-container">
    <canvas id="signupChart" width="800" height="300"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js "></script>
<script>
const ctxSignup = document.getElementById('signupChart').getContext('2d');
new Chart(ctxSignup, {
    type: 'line',
    data: {
        labels: [<?php while($row = $result->fetch_assoc()) { echo "'".$row['month']."',"; } ?>],
        datasets: [{
            label: 'New Members',
            data: [<?php $result->data_seek(0); while($row = $result->fetch_assoc()) { echo $row['count'].","; } ?>],
            borderColor: '#007bff',
            fill: false,
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true },
            title: {
                display: true,
                text: 'Monthly Member Sign-ups'
            }
        }
    }
});
</script>
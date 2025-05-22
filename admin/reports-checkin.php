<?php
$conn = new mysqli("localhost", "root", "", "muscle_city_gym");

$sql = "SELECT DATE(checkin_time) AS date, COUNT(*) AS count FROM user_checkins GROUP BY date ORDER BY date DESC LIMIT 30";
$result = $conn->query($sql);
?>

<h3>Daily Check-ins (Last 30 Days)</h3>
<div class="chart-container">
    <canvas id="checkinChart" width="800" height="300"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js "></script>
<script>
const ctxCheckin = document.getElementById('checkinChart').getContext('2d');
new Chart(ctxCheckin, {
    type: 'bar',
    data: {
        labels: [<?php while($row = $result->fetch_assoc()) { echo "'".date('M d', strtotime($row['date']))."',"; } ?>],
        datasets: [{
            label: 'Check-ins',
            data: [<?php $result->data_seek(0); while($row = $result->fetch_assoc()) { echo $row['count'].","; } ?>],
            backgroundColor: '#28a745'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Daily Gym Check-ins'
            }
        }
    }
});
</script>
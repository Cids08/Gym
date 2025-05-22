<?php
$conn = new mysqli("localhost", "root", "", "muscle_city_gym");

$sql = "SELECT package, COUNT(*) AS count FROM users WHERE role = 'user' GROUP BY package";
$result = $conn->query($sql);

$packageLabels = [];
$packageData = [];

while($row = $result->fetch_assoc()) {
    $packageLabels[] = $row['package'];
    $packageData[] = $row['count'];
}

$sqlStatus = "SELECT status, COUNT(*) AS count FROM memberships GROUP BY status";
$statusResult = $conn->query($sqlStatus);

$statusLabels = [];
$statusData = [];

while($row = $statusResult->fetch_assoc()) {
    $statusLabels[] = $row['status'];
    $statusData[] = $row['count'];
}
?>

<h3>Membership Distribution</h3>
<div class="chart-container">
    <canvas id="membershipTypeChart" width="500" height="300"></canvas>
</div>

<h3>Status Breakdown</h3>
<div class="chart-container">
    <canvas id="membershipStatusChart" width="500" height="300"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js "></script>
<script>
const ctxType = document.getElementById('membershipTypeChart').getContext('2d');
new Chart(ctxType, {
    type: 'doughnut',
    data: {
        labels: [<?php foreach ($packageLabels as $label) echo "'$label',"; ?>],
        datasets: [{
            data: [<?php foreach ($packageData as $d) echo "$d,"; ?>],
            backgroundColor: ['#007bff', '#28a745', '#ffc107']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Membership Type Distribution'
            }
        }
    }
});

const ctxStatus = document.getElementById('membershipStatusChart').getContext('2d');
new Chart(ctxStatus, {
    type: 'pie',
    data: {
        labels: [<?php foreach ($statusLabels as $label) echo "'$label',"; ?>],
        datasets: [{
            data: [<?php foreach ($statusData as $d) echo "$d,"; ?>],
            backgroundColor: ['#28a745', '#dc3545', '#ffc107']
        }]
    },
    options: {
        plugins: {
            title: {
                display: true,
                text: 'Membership Status Breakdown'
            }
        }
    }
});
</script>
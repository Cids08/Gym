<?php
session_start();

if (!isset($_SESSION['session_id'])) {
    session_regenerate_id(true);
    $_SESSION['session_id'] = session_id();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program - Muscle City Gym</title>
    <link rel="stylesheet" href="../public/css/styles.css">
    <link rel="stylesheet" href="../public/css/program.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php include '../partials/header.php'; ?> 
<?php include '../partials/navbar.php'; ?>  

<section id="program" class="info-section">
    <div class="container">
        <h2>Our Program</h2>
        <p>"We offer a variety of fitness programs designed to target different areas of the body and improve overall strength."</p>

        <div class="program-categories">
           <?php 
    $categories = ['arms', 'legs', 'back', 'cardio', 'abs', 'chest'];
    foreach ($categories as $category) {
        echo '<div class="program-category ' . $category . '" 
                style="background-image: url(\'../public/images/categories/' . $category . '.jpg\');" 
                onclick="showSection(\'' . $category . '\')">
                <h3>' . ucfirst($category) . '</h3>
              </div>';
    }
?>

        </div>

        <section id="video-detail">
            <button onclick="goBack()" class="back-btn">← Back to List</button>
            <div class="view-toggle">
                <button onclick="setView('grid')" id="gridViewBtn" class="active">Grid View</button>
                <button onclick="setView('list')" id="listViewBtn">List View</button>
            </div>
            <div id="video-content"></div>
        </section>
    </div>
</section>

<?php include '../partials/footer.php'; ?>

<script>
    function showSection(sectionId) {
        document.querySelector('.program-categories').style.display = 'none';
        document.getElementById('video-detail').style.display = 'block';
        const videoContent = document.getElementById('video-content');
        videoContent.innerHTML = '';
        videoContent.classList.add('grid-view');
        videoContent.classList.remove('list-view');

        const videos = {
            arms: [
                createVideo('Bicep Curls', '../public/videos/Arms/Bicep Curls.mp4'),
        createVideo('Overhead Tricep Extension', '../public/videos/Arms/Overhead Tricep Extension.mp4'),
        createVideo('Dumbbell Rear', '../public/videos/Arms/Dumbbell Rear.mp4'),
        createVideo('Dumbbell Shoulder', '../public/videos/Arms/Dumbbell Shoulder.mp4'),
        createVideo('Dumbbell Lateral', '../public/videos/Arms/Dumbbell Lateral.mp4'),
        createVideo('Straight Bar Push Down', '../public/videos/Arms/Straight Bar Push Down.mp4'),
        createVideo('Tricep Cable Push Down', 'videos/Arms/Tricep Cable Push Down.mp4')
            ],
            legs: [ createVideo('Leg Press', '../public/videos/Legs/Leg Press.mp4'),
             createVideo('Leg Curls', '../public/videos/Legs/Leg Curls.mp4'),
        createVideo('Leg Extension', '../public/videos/Legs/Legs Extension.mp4'),
        createVideo('Leg Press Calves', '../public/videos/Legs/Legs Press Calves.mp4') ],
            back: [
                createVideo('Lat Pulldown', '../public/videos/Back/Lat pull down.mp4.mp4'),
                createVideo('Seated Row', '../public/videos/Back/Seated Row.mp4.mp4'),
                createVideo('Single Arm Lat Machine', '../public/videos/Back/SINGLE ARM LAT MACHINE.mp4'),
                createVideo('T-Bar Row Lats', '../public/videos/Back/T-Bar Row Lats.mp4'),
                createVideo('T-Bar Row Upper Back', '../public/videos/Back/T-Bar Row Upperback.mp4')
            ],
            cardio: [ createVideo('Bike Cardio', '../public/videos/Cardio/Bike.mp4') ],
            abs: [ createVideo('Abs Routine', '../public/videos/Abs/abs.mp4') ],
            chest: [
                createVideo('Incline Barbell Bench', '../public/videos/Chest/Incline Barbell Bench.mp4'),
                createVideo('Chest Dips', '../public/videos/Chest/Chest Dips.mp4'),
                createVideo('Barbell Bench Press', '../public/videos/Chest/Barbell Bench Press.mp4'),
                createVideo('High to Low Cable', '../public/videos/Chest/High to Low Cable.mp4'),
                createVideo('Flat Dumbbell Press', '../public/videos/Chest/Flat Dumbbell Press.mp4'),
                createVideo('Incline Barbell Press', '../public/videos/Chest/Incline Barbell Bench Press.mp4'),
                createVideo('Incline Dumbbell Press', '../public/videos/Chest/Incline Dumbbell Press.mp4'),
                createVideo('Chest Peck Deck', '../public/videos/Chest/Chest Peck Deck.mp4')
            ]
        };

        if (videos[sectionId]) {
            videos[sectionId].forEach(videoHTML => {
                videoContent.innerHTML += videoHTML;
            });
        }
    }

    function createVideo(title, src) {
        return `
            <div class="video-wrapper">
                <h3 style="color: #FFD700;">${title}</h3>
                <video class="video-item" controls>
                    <source src="${src}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>`;
    }

    function goBack() {
        document.querySelector('.program-categories').style.display = 'grid';
        document.getElementById('video-detail').style.display = 'none';
        document.getElementById('video-content').innerHTML = '';
    }

    function setView(viewType) {
        const videoContent = document.getElementById('video-content');
        videoContent.classList.toggle('grid-view', viewType === 'grid');
        videoContent.classList.toggle('list-view', viewType === 'list');
        document.getElementById('gridViewBtn').classList.toggle('active', viewType === 'grid');
        document.getElementById('listViewBtn').classList.toggle('active', viewType === 'list');
    }
</script>
<script src="../public/js/index.js"></script>
<script src="../public/js/search.js"></script>
</body>
</html>

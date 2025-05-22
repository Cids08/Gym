<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>About Us - Muscle City Gym</title>
  <link rel="stylesheet" href="../public/css/styles.css" />
  <link rel="stylesheet" href="../public/css/about-us.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
</head>
<body>

<?php include '../partials/header.php'; ?> 
<?php include '../partials/navbar.php'; ?>  

  <!-- About Section --> 
  <section id="aboutUs" class="info-section">   
    <div class="container about-box">     
      <div class="photo-box">       
        <img id="gymImage" src="../public/images/Out.jpeg" alt="Muscle City Gym" />     
      </div>     
      <div class="text-box">       
        <h2>Welcome to Muscle City Fitness Gym</h2>       
        <p>"At Muscle City Fitness Gym, we don't just build bodies—we build confidence, discipline, and strength. Whether you're just starting or a seasoned athlete, we are here to push your limits and break barriers. Your fitness journey starts here. Are you ready?"</p>     
      </div>   
    </div> 
  </section>  

  <!-- Fullscreen Image --> 
  <div id="fullscreenOverlay" class="fullscreen-overlay">   
    <img id="fullscreenImage" src="" alt="Enlarged Image"/> 
  </div>  

  <!-- Gym Facilities --> 
  <section id="gymFacilities" class="info-section">   
    <div class="container">     
      <h2>Gym Facilities</h2>     
      <p class="facility-intro">Train in a gym designed for results. Equipped with high-quality machines, spacious workout zones, and everything you need to crush your goals.</p>     
      <div class="facility-grid">       
        <?php for ($i = 1; $i <= 8; $i++): ?>         
          <div class="facility-item" data-index="<?= $i - 1 ?>">           
            <img src="../public/images/<?= $i ?>.jpg" alt="Facility <?= $i ?>" />         
          </div>       
        <?php endfor; ?>     
      </div>   
    </div> 
  </section>  

  <!-- Facility Modal -->
  <div id="facilityModal" class="facility-modal">
    <span class="close-btn">&times;</span>
    <span class="modal-nav modal-prev">&#10094;</span>
    <img id="modalImage" src="" alt="Facility Image">
    <span class="modal-nav modal-next">&#10095;</span>
  </div>

  <!-- Contact Us --> 
  <section id="contactUs" class="info-section">   
    <h2>Contact Us</h2>   
    <p class="contact-subheading">Got Questions? We're Here to Help! Contact Us Today and Let's Start Your Fitness Journey</p>    
    <div class="container contact-content">     
      <div class="photo-box">       
        <img src="../public/images/contact.png" alt="Contact Photo" />     
      </div>     
      <div class="info-box">       
        <h3>Get in Touch</h3>       
        <ul>         
          <li><strong>📞 Phone:</strong> 0942-526-4181</li>         
          <li><strong>📧 Email:</strong> <a href="mailto:motournibeshy@gmail.com">motournibeshy@gmail.com</a></li>         
          <li><strong>📍 Address:</strong> 159 Tandang Sora Avenue, Quezon City</li>         
          <li><strong>📘 Facebook:</strong> <a href="https://www.facebook.com/profile.php?id=61564986633379" target="_blank">Muscle City</a></li>       
        </ul>     
      </div>   
    </div> 
  </section>  

  <?php include '../partials/footer.php'; ?>  

  <!-- JavaScript --> 
  <script>
    // Existing fullscreen image functionality
    document.getElementById('gymImage').addEventListener('click', function () {
      const overlay = document.getElementById('fullscreenOverlay');
      const fullscreenImg = document.getElementById('fullscreenImage');
      fullscreenImg.src = this.src;
      overlay.style.display = 'flex';
    });

    document.getElementById('fullscreenOverlay').addEventListener('click', function () {
      this.style.display = 'none';
    });

    // New Facility Modal Functionality
    const facilityItems = document.querySelectorAll('.facility-item');
    const facilityModal = document.getElementById('facilityModal');
    const modalImage = document.getElementById('modalImage');
    const closeBtn = document.querySelector('.close-btn');
    const prevBtn = document.querySelector('.modal-prev');
    const nextBtn = document.querySelector('.modal-next');
    
    let currentIndex = 0;
    const totalImages = facilityItems.length;
    
    // Open modal when clicking on a facility image
    facilityItems.forEach(item => {
      item.addEventListener('click', function() {
        const index = parseInt(this.getAttribute('data-index'));
        currentIndex = index;
        openModal(index);
      });
    });
    
    // Close modal when clicking the close button
    closeBtn.addEventListener('click', function() {
      facilityModal.style.display = 'none';
    });
    
    // Close modal when clicking outside the image
    facilityModal.addEventListener('click', function(e) {
      if (e.target === facilityModal) {
        facilityModal.style.display = 'none';
      }
    });
    
    // Navigation buttons
    prevBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      currentIndex = (currentIndex - 1 + totalImages) % totalImages;
      openModal(currentIndex);
    });
    
    nextBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      currentIndex = (currentIndex + 1) % totalImages;
      openModal(currentIndex);
    });
    
    // Function to open modal with specific image
    function openModal(index) {
      const imgSrc = document.querySelector(`.facility-item[data-index="${index}"] img`).src;
      modalImage.src = imgSrc;
      facilityModal.style.display = 'flex';
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
      if (facilityModal.style.display === 'flex') {
        if (e.key === 'Escape') {
          facilityModal.style.display = 'none';
        } else if (e.key === 'ArrowLeft') {
          currentIndex = (currentIndex - 1 + totalImages) % totalImages;
          openModal(currentIndex);
        } else if (e.key === 'ArrowRight') {
          currentIndex = (currentIndex + 1) % totalImages;
          openModal(currentIndex);
        }
      }
    });
  </script>

  <script src="../public/js/index.js"></script> 
  <script src="../public/js/search.js"></script>  
</body> 
</html>
// Enhanced Modal JavaScript with Proper Centering
document.addEventListener("DOMContentLoaded", function () {
    // Mobile menu toggle
    const menuToggle = document.createElement('div');
    menuToggle.className = 'menu-toggle';
    menuToggle.innerHTML = '<span></span><span></span><span></span>';
    document.querySelector('.main-navbar').prepend(menuToggle);

    // Search toggle for mobile
    const searchToggle = document.createElement('button');
    searchToggle.className = 'search-toggle';
    searchToggle.innerHTML = '&#128269;';
    document.querySelector('.main-navbar').appendChild(searchToggle);

    // Toggle mobile menu
    menuToggle.addEventListener('click', function() {
        this.classList.toggle('active');
        document.querySelector('.nav-links').classList.toggle('active');
        
        // Close search if open
        if (document.querySelector('.search-container').classList.contains('active')) {
            document.querySelector('.search-container').classList.remove('active');
        }
    });

    // Toggle search on mobile
    searchToggle.addEventListener('click', function() {
        document.querySelector('.search-container').classList.toggle('active');
        
        // Close menu if open
        if (document.querySelector('.nav-links').classList.contains('active')) {
            document.querySelector('.nav-links').classList.remove('active');
            menuToggle.classList.remove('active');
        }
        
        // Focus input when opened
        if (document.querySelector('.search-container').classList.contains('active')) {
            document.querySelector('.search-input').focus();
        }
    });

    // Expand search on desktop when focused
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('focus', function() {
            if (window.innerWidth > 768) {
                document.querySelector('.search-container').classList.add('expanded');
            }
        });
        
        searchInput.addEventListener('blur', function() {
            if (window.innerWidth > 768) {
                document.querySelector('.search-container').classList.remove('expanded');
            }
        });
    }

    // IMPROVED IMAGE MODAL FUNCTIONALITY
    const imageModal = document.getElementById("imageModal");
    const modalImg = document.getElementById("fullImage");
    const captionText = document.getElementById("caption");
    const closeImageModal = document.getElementById("closeModal");

    // Get all announcement images
    const announcementImages = document.querySelectorAll(".announcement-box img");

    /**
     * Properly center the modal when it opens
     * @param {string} src - The image source URL
     * @param {string} alt - The image alt text for caption
     */
    function openModal(src, alt) {
        if (!imageModal) return;
        
        // Force flex display
        imageModal.style.display = "flex";
        
        if (modalImg) {
            modalImg.src = src;
        }
        
        if (captionText) {
            captionText.innerHTML = alt || '';
        }
        
        // Force reflow to ensure proper centering
        void imageModal.offsetWidth;
        
        // Add show class
        imageModal.classList.add('show');
        
        // Prevent body scrolling
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        if (!imageModal) return;
        
        imageModal.style.display = "none";
        imageModal.classList.remove('show');
        document.body.style.overflow = '';
    }

    // Add click handlers to all announcement images
    announcementImages.forEach(function(img) {
        img.onclick = function(event) {
            event.stopPropagation();
            openModal(this.src, this.alt);
        };
        
        // Add cursor pointer to indicate clickable
        img.style.cursor = "pointer";
    });

    if (closeImageModal) {
        closeImageModal.onclick = function() {
            closeModal();
        };
    }

    // Close image modal when clicking outside
    window.onclick = function(event) {
        if (event.target === imageModal) {
            closeModal();
        }
    };
    
    // STANDARD LOGIN/SIGNUP MODAL FUNCTIONALITY
    const loginButton = document.querySelector('.nav-links a[href="#login"]');
    const signupButton = document.querySelector('.nav-links a[href="#signup"]');
    const loginModal = document.getElementById('loginModal');
    const signupModal = document.getElementById('signupModal'); 
    
    /**
     * Helper function to open any modal with proper centering
     * @param {HTMLElement} modal - The modal element to open
     */
    function openGenericModal(modal) {
        if (!modal) return;
        
        // Force flex display for proper centering
        modal.style.display = 'flex';
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        // Force re-render to ensure proper centering
        void modal.offsetWidth;
    }
    
    /**
     * Helper function to close any modal
     * @param {HTMLElement} modal - The modal element to close
     */
    function closeGenericModal(modal) {
        if (!modal) return;
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    // Setup event listeners for login/signup buttons
    if (loginButton && loginModal) {
        loginButton.addEventListener('click', function(e) {
            e.preventDefault();
            openGenericModal(loginModal);
        });
        
        const closeLoginBtn = loginModal.querySelector('.close');
        if (closeLoginBtn) {
            closeLoginBtn.addEventListener('click', function() {
                closeGenericModal(loginModal);
            });
        }
    }
    
    if (signupButton && signupModal) {
        signupButton.addEventListener('click', function(e) {
            e.preventDefault();
            openGenericModal(signupModal);
        });
        
        const closeSignupBtn = signupModal.querySelector('.close');
        if (closeSignupBtn) {
            closeSignupBtn.addEventListener('click', function() {
                closeGenericModal(signupModal);
            });
        }
    }
    
    // Close any visible modal when clicking outside
    window.addEventListener('click', function(event) {
        if (loginModal && event.target === loginModal) {
            closeGenericModal(loginModal);
        }
        if (signupModal && event.target === signupModal) {
            closeGenericModal(signupModal);
        }
    });
    
    // Create or update the announcement modal if it doesn't exist
    function setupAnnouncementModal() {
        // Check if we already have an announcement modal
        let announcementModal = document.getElementById('announcementModal');
        
        // If not, create one
        if (!announcementModal) {
            announcementModal = document.createElement('div');
            announcementModal.id = 'announcementModal';
            announcementModal.className = 'modal';
            
            const modalContent = document.createElement('div');
            modalContent.className = 'modal-content';
            
            const closeBtn = document.createElement('span');
            closeBtn.className = 'close';
            closeBtn.innerHTML = '&times;';
            closeBtn.onclick = function() {
                closeGenericModal(announcementModal);
            };
            
            const modalImg = document.createElement('img');
            modalImg.id = 'announcementImage';
            
            modalContent.appendChild(closeBtn);
            modalContent.appendChild(modalImg);
            announcementModal.appendChild(modalContent);
            document.body.appendChild(announcementModal);
            
            // Close when clicking outside the content
            announcementModal.onclick = function(event) {
                if (event.target === announcementModal) {
                    closeGenericModal(announcementModal);
                }
            };
        }
    }
    
    // Call the setup function
    setupAnnouncementModal();
});
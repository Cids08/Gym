// Search functionality for Muscle City Gym website - Uses existing navbar search bar
class GymSearch {
    constructor() {
        this.searchData = {
            trainers: [
                {
                    name: "Joseph Alcantara",
                    title: "Senior Trainer",
                    services: ["Weight Loss", "Fat Loss", "Muscle Building", "Strength and Conditioning"],
                    programs: ["8 Weeks", "12 Weeks", "16 Weeks", "18 Weeks"],
                    page: "trainer.php",
                    type: "trainer"
                }
            ],
            packages: [
                {
                    name: "Basic Membership",
                    price: "₱850/month",
                    features: ["Gym access (Weekdays, 6 AM – 12 PM)", "Access to basic video tutorials", "Free gym towel"],
                    page: "package.php",
                    type: "package"
                },
                {
                    name: "Standard Membership",
                    price: "₱2400/3 months",
                    features: ["Gym access (Mon–Sat, full hours)", "Access to full video tutorial library", "Free gym shirt"],
                    page: "package.php",
                    type: "package"
                },
                {
                    name: "Premium Membership",
                    price: "₱6500/6 months",
                    features: ["Unlimited gym access (7 days a week)", "Access to all exclusive video content", "Free gym kit"],
                    page: "package.php",
                    type: "package"
                }
            ],
            programs: [
                {
                    name: "Arms Workout",
                    exercises: ["Bicep Curls", "Overhead Tricep Extension", "Dumbbell Rear", "Dumbbell Shoulder", "Dumbbell Lateral", "Straight Bar Push Down", "Tricep Cable Push Down"],
                    page: "program.php",
                    type: "program",
                    category: "arms"
                },
                {
                    name: "Legs Workout",
                    exercises: ["Leg Press", "Leg Curls", "Leg Extension", "Leg Press Calves"],
                    page: "program.php",
                    type: "program",
                    category: "legs"
                },
                {
                    name: "Back Workout",
                    exercises: ["Lat Pulldown", "Seated Row", "Single Arm Lat Machine", "T-Bar Row Lats", "T-Bar Row Upper Back"],
                    page: "program.php",
                    type: "program",
                    category: "back"
                },
                {
                    name: "Cardio Workout",
                    exercises: ["Bike Cardio"],
                    page: "program.php",
                    type: "program",
                    category: "cardio"
                },
                {
                    name: "Abs Workout",
                    exercises: ["Abs Routine"],
                    page: "program.php",
                    type: "program",
                    category: "abs"
                },
                {
                    name: "Chest Workout",
                    exercises: ["Incline Barbell Bench", "Chest Dips", "Barbell Bench Press", "High to Low Cable", "Flat Dumbbell Press", "Incline Barbell Press", "Incline Dumbbell Press", "Chest Peck Deck"],
                    page: "program.php",
                    type: "program",
                    category: "chest"
                }
            ],
            facilities: [
                {
                    name: "Gym Facilities",
                    description: "High-quality machines, spacious workout zones, and everything you need to crush your goals",
                    page: "about-us.php",
                    type: "facility"
                }
            ],
            contact: [
                {
                    name: "Contact Information",
                    phone: "0942-526-4181",
                    email: "motournibeshy@gmail.com",
                    address: "159 Tandang Sora Avenue, Quezon City",
                    facebook: "Muscle City",
                    page: "about-us.php",
                    type: "contact"
                }
            ]
        };
        
        this.init();
    }

    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupExistingSearchBar());
        } else {
            this.setupExistingSearchBar();
        }
    }

    setupExistingSearchBar() {
        const searchInput = document.querySelector('.search-input, input[name="search"]');
        const searchContainer = document.querySelector('.search-container');
        
        if (!searchInput || !searchContainer) {
            console.log('Search elements not found, retrying...');
            setTimeout(() => this.setupExistingSearchBar(), 100);
            return;
        }

        // Create results container
        this.createResultsContainer(searchContainer);
        
        // Add styles for the results dropdown
        this.addSearchStyles();
        
        // Bind events to existing search input
        this.bindEvents(searchInput);
        
        console.log('Search functionality initialized');
    }

    createResultsContainer(searchContainer) {
        // Remove existing results container if present
        const existingResults = document.getElementById('searchResults');
        if (existingResults) {
            existingResults.remove();
        }

        // Create new results container
        const resultsContainer = document.createElement('div');
        resultsContainer.id = 'searchResults';
        resultsContainer.className = 'search-results';
        resultsContainer.style.display = 'none';
        
        // Insert after search container
        searchContainer.parentNode.insertBefore(resultsContainer, searchContainer.nextSibling);
    }

    addSearchStyles() {
        // Check if styles already exist
        if (document.getElementById('gymSearchStyles')) return;

        const styles = document.createElement('style');
        styles.id = 'gymSearchStyles';
        styles.textContent = `
            .search-container {
                position: relative;
            }

            .search-results {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background-color: rgba(0, 0, 0, 0.95);
                border: 1px solid #FFD700;
                border-radius: 10px;
                max-height: 400px;
                overflow-y: auto;
                margin-top: 5px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
                z-index: 1000;
            }

            .search-result-item {
                padding: 15px;
                border-bottom: 1px solid #333;
                cursor: pointer;
                transition: background-color 0.3s ease;
                color: white;
            }

            .search-result-item:hover,
            .search-result-item.active {
                background-color: rgba(255, 215, 0, 0.1);
            }

            .search-result-item:last-child {
                border-bottom: none;
            }

            .result-title {
                font-weight: bold;
                color: #FFD700;
                margin-bottom: 5px;
                font-size: 16px;
            }

            .result-type {
                background-color: #FFD700;
                color: black;
                padding: 2px 8px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: bold;
                display: inline-block;
                margin-bottom: 5px;
                text-transform: uppercase;
            }

            .result-description {
                color: #ccc;
                font-size: 14px;
                line-height: 1.4;
            }

            .result-features {
                margin-top: 5px;
                font-size: 13px;
                color: #aaa;
            }

            .no-results {
                padding: 20px;
                text-align: center;
                color: #ccc;
                font-style: italic;
            }

            /* Clear button for search input */
            .search-input-wrapper {
                position: relative;
                display: inline-block;
                width: 100%;
            }

            .clear-search {
                position: absolute;
                right: 40px;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                color: #FFD700;
                font-size: 16px;
                cursor: pointer;
                padding: 5px;
                border-radius: 50%;
                transition: all 0.3s ease;
                display: none;
            }

            .clear-search:hover {
                background-color: rgba(255, 215, 0, 0.2);
            }

            @media (max-width: 768px) {
                .search-results {
                    left: -10px;
                    right: -10px;
                    max-height: 300px;
                }
                
                .search-result-item {
                    padding: 12px;
                }
                
                .result-title {
                    font-size: 14px;
                }
                
                .result-description {
                    font-size: 13px;
                }
            }
        `;
        
        document.head.appendChild(styles);
    }

    bindEvents(searchInput) {
        const resultsContainer = document.getElementById('searchResults');
        const searchButton = document.querySelector('.search-button');
        
        let searchTimeout;

        // Add clear button functionality
        this.addClearButton(searchInput);

        // Handle input events
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            if (query.length === 0) {
                this.hideResults();
                this.toggleClearButton(false);
                return;
            }

            this.toggleClearButton(true);
            
            // Debounce search
            searchTimeout = setTimeout(() => {
                this.performSearch(query);
            }, 300);
        });

        // Handle search button click
        searchButton.addEventListener('click', (e) => {
            e.preventDefault();
            const query = searchInput.value.trim();
            if (query.length > 0) {
                this.performSearch(query);
            }
        });

        // Handle Enter key
        searchInput.addEventListener('keydown', (e) => {
            const results = resultsContainer.querySelectorAll('.search-result-item');
            const activeResult = resultsContainer.querySelector('.search-result-item.active');
            let activeIndex = Array.from(results).indexOf(activeResult);

            switch(e.key) {
                case 'Enter':
                    e.preventDefault();
                    if (activeResult) {
                        activeResult.click();
                    } else if (results.length > 0) {
                        results[0].click();
                    } else {
                        // If no dropdown results, perform search
                        const query = searchInput.value.trim();
                        if (query.length > 0) {
                            this.performSearch(query);
                        }
                    }
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    if (results.length > 0) {
                        if (activeIndex < results.length - 1) {
                            if (activeResult) activeResult.classList.remove('active');
                            results[activeIndex + 1].classList.add('active');
                        }
                    }
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    if (results.length > 0) {
                        if (activeIndex > 0) {
                            if (activeResult) activeResult.classList.remove('active');
                            results[activeIndex - 1].classList.add('active');
                        }
                    }
                    break;
                case 'Escape':
                    this.hideResults();
                    searchInput.blur();
                    break;
            }
        });

        // Hide results when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-container') && !e.target.closest('.search-results')) {
                this.hideResults();
            }
        });

        // Show results when focusing on search input (if there's a query)
        searchInput.addEventListener('focus', () => {
            const query = searchInput.value.trim();
            if (query.length > 0) {
                this.performSearch(query);
            }
        });
    }

    addClearButton(searchInput) {
        // Wrap search input if not already wrapped
        if (!searchInput.parentElement.classList.contains('search-input-wrapper')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'search-input-wrapper';
            searchInput.parentNode.insertBefore(wrapper, searchInput);
            wrapper.appendChild(searchInput);
        }

        // Add clear button
        const clearButton = document.createElement('button');
        clearButton.type = 'button';
        clearButton.className = 'clear-search';
        clearButton.innerHTML = '&times;';
        clearButton.title = 'Clear search';
        
        clearButton.addEventListener('click', () => {
            searchInput.value = '';
            this.hideResults();
            this.toggleClearButton(false);
            searchInput.focus();
        });

        searchInput.parentElement.appendChild(clearButton);
    }

    toggleClearButton(show) {
        const clearButton = document.querySelector('.clear-search');
        if (clearButton) {
            clearButton.style.display = show ? 'block' : 'none';
        }
    }

    performSearch(query) {
        const results = [];
        const queryLower = query.toLowerCase();

        // Search through all data categories
        Object.keys(this.searchData).forEach(category => {
            this.searchData[category].forEach(item => {
                const score = this.calculateRelevanceScore(item, queryLower);
                if (score > 0) {
                    results.push({ ...item, score, category });
                }
            });
        });

        // Sort by relevance score
        results.sort((a, b) => b.score - a.score);

        this.displayResults(results, query);
    }

    calculateRelevanceScore(item, query) {
        let score = 0;

        // Check name/title (highest priority)
        if (item.name && item.name.toLowerCase().includes(query)) {
            score += 10;
        }

        // Check title/type
        if (item.title && item.title.toLowerCase().includes(query)) {
            score += 8;
        }

        // Check services/features/exercises
        const searchableArrays = ['services', 'features', 'exercises'];
        searchableArrays.forEach(arrayKey => {
            if (item[arrayKey]) {
                item[arrayKey].forEach(arrayItem => {
                    if (arrayItem.toLowerCase().includes(query)) {
                        score += 5;
                    }
                });
            }
        });

        // Check description/category
        if (item.description && item.description.toLowerCase().includes(query)) {
            score += 3;
        }

        if (item.category && item.category.toLowerCase().includes(query)) {
            score += 6;
        }

        // Check price
        if (item.price && item.price.toLowerCase().includes(query)) {
            score += 4;
        }

        // Check contact info
        if (item.phone && item.phone.includes(query)) {
            score += 7;
        }

        if (item.email && item.email.toLowerCase().includes(query)) {
            score += 7;
        }

        if (item.address && item.address.toLowerCase().includes(query)) {
            score += 6;
        }

        return score;
    }

    displayResults(results, query) {
        const resultsContainer = document.getElementById('searchResults');
        
        if (results.length === 0) {
            resultsContainer.innerHTML = `
                <div class="no-results">
                    No results found for "${query}". Try searching for trainers, programs, packages, or facilities.
                </div>
            `;
        } else {
            resultsContainer.innerHTML = results.map(result => this.createResultHTML(result)).join('');
        }

        resultsContainer.style.display = 'block';
    }

    createResultHTML(result) {
        let description = '';
        let features = '';

        switch(result.type) {
            case 'trainer':
                description = `${result.title} - Specializes in ${result.services.slice(0, 2).join(', ')}`;
                if (result.services.length > 2) description += ` and ${result.services.length - 2} more services`;
                features = `Programs: ${result.programs.join(', ')}`;
                break;
            case 'package':
                description = `${result.price} - ${result.features.length} features included`;
                features = result.features.slice(0, 2).join(' • ');
                if (result.features.length > 2) features += ` • +${result.features.length - 2} more`;
                break;
            case 'program':
                description = `${result.exercises.length} exercises available`;
                features = `Category: ${result.category} • Exercises: ${result.exercises.slice(0, 3).join(', ')}`;
                if (result.exercises.length > 3) features += ` +${result.exercises.length - 3} more`;
                break;
            case 'facility':
                description = result.description;
                break;
            case 'contact':
                description = `${result.phone} • ${result.email}`;
                features = result.address;
                break;
        }

        return `
            <div class="search-result-item" onclick="gymSearch.navigateToResult('${result.page}', '${result.type}', '${result.category || ''}')">
                <div class="result-type">${result.type}</div>
                <div class="result-title">${result.name}</div>
                <div class="result-description">${description}</div>
                ${features ? `<div class="result-features">${features}</div>` : ''}
            </div>
        `;
    }

    navigateToResult(page, type, category = '') {
        // Hide search results
        this.hideResults();
        
        // Store search context for navigation
        sessionStorage.setItem('searchContext', JSON.stringify({ type, category }));
        
        // Navigate based on type and current page
        const currentPage = window.location.pathname.split('/').pop();
        
        if (currentPage !== page) {
            // Navigate to different page
            window.location.href = page;
        } else {
            // Already on the correct page, scroll to relevant section
            this.scrollToSection(type, category);
        }
    }

    scrollToSection(type, category) {
        let targetElement = null;

        switch(type) {
            case 'trainer':
                targetElement = document.getElementById('trainer') || document.querySelector('.trainer-card');
                break;
            case 'package':
                targetElement = document.getElementById('package') || document.querySelector('.package-container');
                break;
            case 'program':
                if (category) {
                    // If we're on the program page and have a specific category
                    targetElement = document.querySelector(`.program-category.${category}`);
                    if (targetElement) {
                        // Trigger the category click
                        setTimeout(() => targetElement.click(), 100);
                        return;
                    }
                }
                targetElement = document.getElementById('program');
                break;
            case 'facility':
                targetElement = document.getElementById('gymFacilities') || document.getElementById('aboutUs');
                break;
            case 'contact':
                targetElement = document.getElementById('contactUs');
                break;
        }

        if (targetElement) {
            setTimeout(() => {
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    }

    hideResults() {
        const resultsContainer = document.getElementById('searchResults');
        if (resultsContainer) {
            resultsContainer.style.display = 'none';
        }
    }

    // Handle search context when page loads (for navigation from search)
    handleSearchContext() {
        const searchContext = sessionStorage.getItem('searchContext');
        if (searchContext) {
            const { type, category } = JSON.parse(searchContext);
            sessionStorage.removeItem('searchContext');
            
            setTimeout(() => {
                this.scrollToSection(type, category);
            }, 500);
        }
    }
}

// Initialize search functionality
document.addEventListener('DOMContentLoaded', function() {
    window.gymSearch = new GymSearch();
    // Handle search context for navigation
    setTimeout(() => {
        if (window.gymSearch) {
            window.gymSearch.handleSearchContext();
        }
    }, 100);
});

// Fallback initialization
if (document.readyState !== 'loading') {
    window.gymSearch = new GymSearch();
    setTimeout(() => {
        if (window.gymSearch) {
            window.gymSearch.handleSearchContext();
        }
    }, 100);
}
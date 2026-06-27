// script.js - Main JavaScript file

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu functionality
    const mobileMenuBtn = document.querySelector('.mobile-menu');
    const navLinks = document.querySelector('.nav-links');
    const navAuth = document.querySelector('.nav-auth');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            if (navLinks.style.display === 'flex') {
                navLinks.style.display = 'none';
                if (navAuth) navAuth.style.display = 'none';
            } else {
                navLinks.style.display = 'flex';
                navLinks.style.flexDirection = 'column';
                navLinks.style.position = 'absolute';
                navLinks.style.top = '60px';
                navLinks.style.left = '0';
                navLinks.style.right = '0';
                navLinks.style.backgroundColor = 'white';
                navLinks.style.padding = '1rem';
                navLinks.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';
                navLinks.style.zIndex = '999';
                
                if (navAuth) {
                    navAuth.style.display = 'flex';
                    navAuth.style.position = 'absolute';
                    navAuth.style.top = '200px';
                    navAuth.style.left = '0';
                    navAuth.style.right = '0';
                    navAuth.style.backgroundColor = 'white';
                    navAuth.style.padding = '1rem';
                    navAuth.style.justifyContent = 'center';
                    navAuth.style.zIndex = '999';
                }
            }
        });
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe all sections for fade-in animation
    document.querySelectorAll('.property-card, .step-card, .user-type, .feature').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
    
    // Search form validation
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const rooms = document.getElementById('rooms')?.value;
            const location = document.getElementById('location_search')?.value;
            const maxPrice = document.getElementById('max_price')?.value;
            
            // Optional: Add custom validation logic here
            console.log('Searching for:', { rooms, location, maxPrice });
        });
    }
    
    // Property card hover effect enhancement
    const propertyCards = document.querySelectorAll('.property-card');
    propertyCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Sign In and Sign Up button handlers
    const signInBtn = document.getElementById('signInBtn');
    const signUpBtn = document.getElementById('signUpBtn');
    
    if (signInBtn) {
        signInBtn.addEventListener('click', function() {
            window.location.href = '../login.php';
        });
    }
    
    if (signUpBtn) {
        signUpBtn.addEventListener('click', function() {
            window.location.href = '../signup.php';
        });
    }
    
    // Add loading state for images
    const images = document.querySelectorAll('.property-image img');
    images.forEach(img => {
        img.addEventListener('load', function() {
            this.style.opacity = '1';
        });
        
        if (img.complete) {
            img.style.opacity = '1';
        } else {
            img.style.opacity = '0';
            img.style.transition = 'opacity 0.3s ease';
        }
    });
    
    // Price formatting helper (if needed for dynamic content)
    function formatPrice(price) {
        return new Intl.NumberFormat('en-NP', {
            style: 'currency',
            currency: 'NPR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(price);
    }
    
    // Optional: Add search suggestions
    const locationSearch = document.getElementById('location_search');
    if (locationSearch) {
        locationSearch.addEventListener('change', function() {
            // Could implement dynamic filtering here
            console.log('Location changed to:', this.value);
        });
    }
    
    // Handle window resize for mobile menu
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            if (navLinks) navLinks.style.display = '';
            if (navAuth) navAuth.style.display = '';
            if (navLinks) navLinks.style.flexDirection = '';
            if (navLinks) navLinks.style.position = '';
            if (navLinks) navLinks.style.backgroundColor = '';
            if (navLinks) navLinks.style.padding = '';
            if (navLinks) navLinks.style.boxShadow = '';
            if (navLinks) navLinks.style.zIndex = '';
        } else {
            if (navLinks && navLinks.style.display === 'flex') {
                // Keep mobile menu state if open
            }
        }
    });
});
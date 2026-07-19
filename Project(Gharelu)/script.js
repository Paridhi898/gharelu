// script.js - Complete JavaScript file

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
                navLinks.style.gap = '1rem';
                navLinks.style.alignItems = 'center';
                
                if (navAuth) {
                    navAuth.style.display = 'flex';
                    navAuth.style.flexDirection = 'column';
                    navAuth.style.position = 'absolute';
                    navAuth.style.top = '220px';
                    navAuth.style.left = '0';
                    navAuth.style.right = '0';
                    navAuth.style.backgroundColor = 'white';
                    navAuth.style.padding = '1rem';
                    navAuth.style.alignItems = 'center';
                    navAuth.style.zIndex = '999';
                    navAuth.style.gap = '0.5rem';
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
    
    // Search form validation with pagination reset
    const searchForms = document.querySelectorAll('.search-form');
    searchForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Remove page parameter when searching
            const pageInput = document.createElement('input');
            pageInput.type = 'hidden';
            pageInput.name = 'page';
            pageInput.value = '1';
            this.appendChild(pageInput);
            
            const rooms = this.querySelector('#rooms')?.value;
            const province = this.querySelector('#province_search')?.value;
            const maxPrice = this.querySelector('#max_price')?.value;
            
            console.log('Searching for:', { rooms, province, maxPrice });
        });
    });
    
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
    
    // Price formatting helper
    function formatPrice(price) {
        return new Intl.NumberFormat('en-NP', {
            style: 'currency',
            currency: 'NPR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(price);
    }
    
    // Handle window resize for mobile menu
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            if (navLinks) {
                navLinks.style.display = '';
                navLinks.style.flexDirection = '';
                navLinks.style.position = '';
                navLinks.style.backgroundColor = '';
                navLinks.style.padding = '';
                navLinks.style.boxShadow = '';
                navLinks.style.zIndex = '';
                navLinks.style.gap = '';
                navLinks.style.alignItems = '';
            }
            if (navAuth) {
                navAuth.style.display = '';
                navAuth.style.flexDirection = '';
                navAuth.style.position = '';
                navAuth.style.backgroundColor = '';
                navAuth.style.padding = '';
                navAuth.style.alignItems = '';
                navAuth.style.zIndex = '';
                navAuth.style.gap = '';
            }
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('propertyModal');
            if (modal) {
                const url = new URL(window.location.href);
                url.searchParams.delete('view');
                window.location.href = url.toString();
            }
        }
    });
    
    // Pagination - Preserve scroll position when changing pages
    document.querySelectorAll('.page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            if (href) {
                // Store current scroll position
                sessionStorage.setItem('scrollPosition', window.scrollY);
                window.location.href = href;
            }
        });
    });
    
    // Restore scroll position after page load
    const scrollPosition = sessionStorage.getItem('scrollPosition');
    if (scrollPosition) {
        window.scrollTo(0, parseInt(scrollPosition));
        sessionStorage.removeItem('scrollPosition');
    }
    
    // Sort dropdown - auto-submit on change
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const form = this.closest('form') || this.parentElement.querySelector('form');
            if (form) {
                form.submit();
            } else {
                // If no form, build URL and redirect
                const url = new URL(window.location.href);
                url.searchParams.set('sort', this.value);
                url.searchParams.set('page', '1');
                window.location.href = url.toString();
            }
        });
    }
});
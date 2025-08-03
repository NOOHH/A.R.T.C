/**
 * Enrollment Button Debug Script
 * 
 * This script adds event listeners to the enrollment buttons to help
 * debug any issues with navigation to enrollment pages.
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Enrollment debug script loaded');
    
    // Find all enrollment buttons
    const enrollButtons = document.querySelectorAll('.enrollment-btn, .enroll-btn');
    console.log('Found ' + enrollButtons.length + ' enrollment buttons');
    
    // Add click event listeners with debug info
    enrollButtons.forEach((button, index) => {
        console.log(`Button ${index}: `, button);
        
        // Add event listener that logs before navigation
        button.addEventListener('click', function(e) {
            // Don't prevent default to allow normal navigation
            console.log(`Button ${index} clicked. Navigating to:`, this.getAttribute('href'));
            
            // Log the exact time of click
            console.log('Click time: ', new Date().toISOString());
        });
    });
    
    // Log route information
    console.log('Current page URL:', window.location.href);
    
    // Check if Laravel routes are available
    if (typeof Laravel !== 'undefined' && Laravel.routes) {
        console.log('Laravel routes available:', Laravel.routes);
    } else {
        console.log('Laravel routes not available in JavaScript');
    }
});

/**
 * Modular Enrollment Button Direct Fix
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Modular button fix script loaded');
    
    // Find the modular enrollment button by ID
    const modularButton = document.getElementById('modular-enroll-btn');
    
    if (modularButton) {
        console.log('Found modular enrollment button:', modularButton);
        
        // Replace the onclick handler with a direct one
        modularButton.onclick = function(e) {
            e.preventDefault();
            console.log('Modular button clicked, navigating to /enrollment/modular');
            window.location.href = '/enrollment/modular';
        };
    } else {
        console.log('Modular enrollment button not found!');
    }
    
    // Add a global click handler for any enrollment buttons
    document.body.addEventListener('click', function(e) {
        if (e.target.classList.contains('enrollment-btn') || 
            e.target.classList.contains('enroll-btn') || 
            e.target.closest('.enrollment-btn') || 
            e.target.closest('.enroll-btn')) {
            
            console.log('Enrollment button clicked via global handler');
            
            // Get the closest button or link
            const button = e.target.closest('a, button');
            if (button) {
                // Check if it's the modular button
                if (button.textContent.includes('Modular') || 
                    button.innerHTML.includes('puzzle') ||
                    button.id === 'modular-enroll-btn') {
                    
                    console.log('Modular enrollment button detected');
                    e.preventDefault();
                    window.location.href = '/enrollment/modular';
                }
            }
        }
    });
});

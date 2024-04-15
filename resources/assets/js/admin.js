import { handleButtonClicks } from './eventHandlers.js';

const initializeUI = () => {
    initializeForms();
    initializeHeaderScroll();
}

const initializeForms = () => {
    // Listen for the submission of any form
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', event => {
            // Find all submit buttons within the form
            form.querySelectorAll('button[type="submit"]').forEach(button => {
                // Add the btn-loading class to each submit button
                button.classList.add('btn-loading');
                // Optional: Disable the button to prevent multiple submissions
                button.disabled = true;
            });
        });
    });
}

const initializeHeaderScroll = () => {
    // window.addEventListener('scroll', () => {
    //     const header = document.getElementById('page-header');

    //     if(!header) return;
    //     if (window.scrollY > 0) {
    //         header.classList.add('bg-dark');
    //     } else {
    //         header.classList.remove('bg-dark');
    //     }
    // });
}

const initializePageSpecificFeatures = () => {

}

const initializeTooltips = () => {
    // tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl));
}

const displayMessages = messages => {
    if (typeof messages !== 'undefined') {
        // Handle validation errors and flashed messages differently
        if (messages.errors && messages.errors.length > 0) {
            // Display validation errors
            showAlert('Error', messages.errors.join('\n'), 'error', { timer: 5000 });
        }

        // Handle flashed session messages
        if (messages.flash) {
            for (const [key, msg] of Object.entries(messages.flash)) {
                if (msg) { // Check if the message is not null
                    // Display each flashed message
                    // The key is the message type (success, error, warning, info)
                    showAlert(key, msg, key, { timer: 5000 });
                }
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initializeUI();
    initializePageSpecificFeatures();
    initializeTooltips();
    handleButtonClicks();
    displayMessages(window.messages);
});
<?php
header('Content-Type: application/javascript');
?>

// Shared SDK functions for code examples and tab management

function copyCode(elementId) {
    const codeElement = document.getElementById(elementId);
    const code = codeElement.textContent || codeElement.innerText;

    navigator.clipboard.writeText(code).then(function() {
        // Visual feedback
        const button = codeElement.parentElement.querySelector('.copy-btn');
        const originalText = button.textContent;
        button.textContent = 'Copied!';
        button.classList.add('copied');

        setTimeout(() => {
            button.textContent = originalText;
            button.classList.remove('copied');
        }, 2000);
    }).catch(function(err) {
        console.error('Failed to copy: ', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = code;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);

        // Visual feedback for fallback
        const button = codeElement.parentElement.querySelector('.copy-btn');
        const originalText = button.textContent;
        button.textContent = 'Copied!';
        button.classList.add('copied');

        setTimeout(() => {
            button.textContent = originalText;
            button.classList.remove('copied');
        }, 2000);
    });
}

function showTab(language) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => content.classList.remove('active'));

    // Remove active class from all buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => button.classList.remove('active'));

    // Show selected tab content
    const selectedContent = document.getElementById(language + '-content');
    if (selectedContent) {
        selectedContent.classList.add('active');
    }

    // Add active class to clicked button
    const selectedButton = document.querySelector(`[onclick="showTab('${language}')"]`);
    if (selectedButton) {
        selectedButton.classList.add('active');
    }
}

// Initialize SDK section - show PHP tab by default when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if there are SDK tabs present
    if (document.querySelector('.tab-button')) {
        showTab('php');
    }
});
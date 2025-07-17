<?php
header('Content-Type: text/css');
?>

/* SDK Section Styles - Shared across all pages with SDK documentation */

.developer-section {
    margin-top: 40px;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
}

.developer-section h2 {
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.code-section {
    margin: 30px 0;
}

.code-section h3 {
    color: #555;
    margin-bottom: 15px;
    font-size: 20px;
}

.tabs {
    border-bottom: 1px solid #ddd;
    margin-bottom: 20px;
}

.tab-button {
    background: #f8f9fa;
    border: 1px solid #ddd;
    border-bottom: none;
    padding: 8px 16px;
    cursor: pointer;
    margin-right: 4px;
    border-radius: 4px 4px 0 0;
    color: #555;
    font-size: 14px;
    display: inline-block;
}

.tab-button.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.code-block {
    position: relative;
    background: #2d3748;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 20px;
}

.code-block pre {
    margin: 0;
    padding: 20px;
    color: #e2e8f0;
    background: #2d3748;
    overflow-x: auto;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    line-height: 1.6;
}

.copy-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #007bff;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    z-index: 10;
    transition: background-color 0.2s;
}

.copy-btn:hover {
    background: #0056b3;
}

.copy-btn.copied {
    background: #28a745;
}

.sdk-info {
    background: #e3f2fd;
    padding: 15px;
    border-radius: 6px;
    margin: 20px 0;
    border-left: 4px solid #2196f3;
}

.sdk-info h4 {
    margin: 0 0 10px 0;
    color: #1976d2;
}

.sdk-info p {
    margin: 5px 0;
    color: #555;
}

.section-divider {
    border-top: 1px solid #ddd;
    margin: 40px 0;
}

/* New UI Components for Collapsible Sections */

.section-nav {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    padding: 15px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.nav-btn {
    padding: 10px 16px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    box-shadow: 0 2px 4px rgba(0,123,255,0.3);
}

.nav-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,123,255,0.4);
    background: linear-gradient(135deg, #0056b3, #003d82);
}

.collapsible-section {
    background: white;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.section-header {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    padding: 15px 20px;
    cursor: pointer;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
    user-select: none;
}

.section-header:hover {
    background: linear-gradient(135deg, #e9ecef, #dee2e6);
}

.section-header h3 {
    margin: 0;
    color: #495057;
    font-size: 18px;
    font-weight: 600;
}

.toggle-icon {
    font-size: 20px;
    font-weight: bold;
    color: #6c757d;
    transition: transform 0.3s ease;
    width: 20px;
    text-align: center;
}

.collapsible-section.collapsed .toggle-icon {
    transform: rotate(90deg);
}

.collapsible-section.collapsed .code-section {
    display: none;
}

.code-section {
    padding: 20px;
    transition: all 0.3s ease;
}

/* Smooth scrolling enhancement */
html {
    scroll-behavior: smooth;
}

/* Enhanced visual feedback */
.collapsible-section:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Responsive improvements */
@media (max-width: 768px) {
    .section-nav {
        flex-direction: column;
        gap: 8px;
    }
    
    .nav-btn {
        text-align: center;
    }
    
    .section-header {
        padding: 12px 15px;
    }
    
    .section-header h3 {
        font-size: 16px;
    }
}
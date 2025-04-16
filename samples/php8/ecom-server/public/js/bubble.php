<?php
header('Content-Type: application/javascript');
?>

document.addEventListener('DOMContentLoaded', function() {
    const docsMap = {
        // Terminal
        'terminals.php': 'https://api-docs.kody.com/docs/payments-api/terminal-payments/#1-list-of-terminals',
        'terminal_payment_form.php': 'https://api-docs.kody.com/docs/payments-api/terminal-payments/#2-initiate-terminal-payment',
        'terminal_submit_payment.php': 'https://api-docs.kody.com/docs/payments-api/terminal-payments/#3-cancel-terminal-payment',
        // Ecom
        'checkout.php': 'https://api-docs.kody.com/docs/payments-api/ecom-payments/#1-initiate-payment',
        'checkout_return.php': 'https://api-docs.kody.com/docs/payments-api/ecom-payments/#3-payment-details',
        'transactions.php': 'https://api-docs.kody.com/docs/payments-api/ecom-payments/#5-get-payments',
        'refund-form.php': 'https://api-docs.kody.com/docs/payments-api/ecom-payments/#6-refund-payments',
    };

    const bubble = document.createElement('div');
    bubble.className = 'docs-bubble';
    bubble.innerHTML = '<span>API documentation</span>';

    // Updated styles for a larger bubble with text
    bubble.style.position = 'fixed';
    bubble.style.width = 'auto';
    bubble.style.height = 'auto';
    bubble.style.minWidth = '160px';
    bubble.style.padding = '10px 15px';
    bubble.style.borderRadius = '25px';
    bubble.style.backgroundColor = '#28a745';
    bubble.style.color = 'white';
    bubble.style.bottom = '20px';
    bubble.style.right = '20px';
    bubble.style.display = 'flex';
    bubble.style.alignItems = 'center';
    bubble.style.justifyContent = 'center';
    bubble.style.fontSize = '14px';
    bubble.style.fontWeight = 'bold';
    bubble.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
    bubble.style.cursor = 'pointer';
    bubble.style.zIndex = '9999';
    bubble.style.transition = 'transform 0.3s ease';
    bubble.style.textAlign = 'center';

    bubble.addEventListener('mouseover', function() {
        this.style.transform = 'scale(1.05)';
    });

    bubble.addEventListener('mouseout', function() {
        this.style.transform = 'scale(1)';
    });

    const modal = document.createElement('div');
    modal.className = 'docs-modal';
    modal.style.display = 'none';
    modal.style.position = 'fixed';
    modal.style.zIndex = '10000';
    modal.style.left = '0';
    modal.style.top = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.overflow = 'hidden';
    modal.style.backgroundColor = 'rgba(0,0,0,0.5)';

    const modalContent = document.createElement('div');
    modalContent.className = 'docs-modal-content';
    modalContent.style.backgroundColor = '#fefefe';
    modalContent.style.margin = '2% auto';
    modalContent.style.padding = '20px';
    modalContent.style.border = '1px solid #888';
    modalContent.style.width = '90%';
    modalContent.style.maxWidth = '1200px';
    modalContent.style.borderRadius = '5px';
    modalContent.style.position = 'relative';
    modalContent.style.height = '90vh';
    modalContent.style.display = 'flex';
    modalContent.style.flexDirection = 'column';
    modalContent.style.overflow = 'hidden';

    const closeBtn = document.createElement('span');
    closeBtn.className = 'docs-close-button';
    closeBtn.innerHTML = '&times;';
    closeBtn.style.color = '#aaa';
    closeBtn.style.position = 'absolute';
    closeBtn.style.right = '20px';
    closeBtn.style.top = '10px';
    closeBtn.style.fontSize = '28px';
    closeBtn.style.fontWeight = 'bold';
    closeBtn.style.cursor = 'pointer';
    closeBtn.style.zIndex = '1';

    closeBtn.addEventListener('mouseover', function() {
        this.style.color = '#000';
    });

    closeBtn.addEventListener('mouseout', function() {
        this.style.color = '#aaa';
    });

    const currentPath = window.location.pathname;
    const filename = currentPath.split('/').pop().toLowerCase();
    let docsSrc = "https://api-docs.kody.com";

    if (filename in docsMap) {
        docsSrc = docsMap[filename];
    }

    const modalHeader = document.createElement('div');
    modalHeader.style.padding = '10px';
    modalHeader.style.borderBottom = '1px solid #ddd';
    modalHeader.style.marginBottom = '0';
    modalHeader.style.display = 'flex';
    modalHeader.style.justifyContent = 'space-between';
    modalHeader.style.alignItems = 'center';

    const modalTitle = document.createElement('h2');
    modalTitle.textContent = 'API Documentation';
    modalTitle.style.margin = '0';

    const externalLinkBtn = document.createElement('a');
    externalLinkBtn.innerHTML = 'Open in New Tab';
    externalLinkBtn.href = docsSrc;
    externalLinkBtn.target = '_blank';
    externalLinkBtn.style.marginLeft = '10px';
    externalLinkBtn.style.padding = '8px 12px';
    externalLinkBtn.style.backgroundColor = '#28a745';
    externalLinkBtn.style.color = 'white';
    externalLinkBtn.style.textDecoration = 'none';
    externalLinkBtn.style.borderRadius = '4px';
    externalLinkBtn.style.fontSize = '14px';

    modalHeader.appendChild(modalTitle);
    modalHeader.appendChild(externalLinkBtn);

    const browserContainer = document.createElement('div');
    browserContainer.style.flex = '1';
    browserContainer.style.position = 'relative';
    browserContainer.style.overflow = 'hidden';

    const loadingMessage = document.createElement('div');
    loadingMessage.textContent = 'Loading documentation...';
    loadingMessage.style.position = 'absolute';
    loadingMessage.style.top = '50%';
    loadingMessage.style.left = '50%';
    loadingMessage.style.transform = 'translate(-50%, -50%)';
    loadingMessage.style.padding = '15px 25px';
    loadingMessage.style.backgroundColor = '#f8f9fa';
    loadingMessage.style.borderRadius = '5px';
    loadingMessage.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
    loadingMessage.style.zIndex = '1';

    browserContainer.appendChild(loadingMessage);

    const iframe = document.createElement('iframe');
    iframe.style.width = '100%';
    iframe.style.height = '100%';
    iframe.style.border = 'none';
    iframe.style.opacity = '0';
    iframe.style.transition = 'opacity 0.5s';

    const htmlContent = `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body, html {
                margin: 0;
                padding: 0;
                height: 100%;
                overflow: hidden;
            }
            #content {
                width: 100%;
                height: 100%;
                border: none;
            }
        </style>
    </head>
    <body>
        <iframe id="content" src="${docsSrc}" allowfullscreen></iframe>
    </body>
    </html>
    `;

    iframe.srcdoc = htmlContent;

    setTimeout(function() {
        loadingMessage.style.display = 'none';
        iframe.style.opacity = '1';
    }, 1500);

    browserContainer.appendChild(iframe);

    modalContent.appendChild(closeBtn);
    modalContent.appendChild(modalHeader);
    modalContent.appendChild(browserContainer);
    modal.appendChild(modalContent);

    bubble.addEventListener('click', function() {
        modal.style.display = 'block';
        iframe.srcdoc = htmlContent;
    });

    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal.style.display === 'block') {
            modal.style.display = 'none';
        }
    });

    document.body.appendChild(bubble);
    document.body.appendChild(modal);
});

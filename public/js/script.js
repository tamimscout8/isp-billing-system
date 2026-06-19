/**
 * ISP Billing System - JavaScript
 */

// Alert functions
function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    alertDiv.style.marginBottom = '1rem';
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

// Confirm delete
function confirmDelete(id, type) {
    if (confirm(`Are you sure you want to delete this ${type}?`)) {
        // Send delete request
        window.location.href = `?page=${type}s&action=delete&id=${id}`;
    }
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Format date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Search functionality
function setupSearch() {
    const searchForm = document.getElementById('search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchTerm = document.getElementById('search-term').value;
            if (searchTerm.length >= 2) {
                window.location.href = `?page=${this.dataset.type}&action=search&q=${searchTerm}`;
            }
        });
    }
}

// Pagination
function setupPagination() {
    const pageLinks = document.querySelectorAll('.pagination a');
    pageLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Pagination works with standard links
        });
    });
}

// Setup active navigation
function setupNavigation() {
    const currentPage = new URLSearchParams(window.location.search).get('page') || 'dashboard';
    const navLinks = document.querySelectorAll('nav a');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href.includes(`page=${currentPage}`)) {
            link.classList.add('active');
        }
    });
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = 'red';
            isValid = false;
        } else {
            input.style.borderColor = '';
        }
    });

    return isValid;
}

// Submit form with validation
function submitForm(formId, callback) {
    if (validateForm(formId)) {
        const form = document.getElementById(formId);
        form.submit();
    } else {
        showAlert('Please fill in all required fields', 'danger');
    }
}

// Chart data processing
function processRevenueChart(data) {
    if (!data || data.length === 0) return null;

    const labels = [];
    const revenues = [];

    data.forEach(row => {
        labels.push(row.month);
        revenues.push(row.revenue);
    });

    return { labels, revenues };
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    setupNavigation();
    setupSearch();
    setupPagination();
});

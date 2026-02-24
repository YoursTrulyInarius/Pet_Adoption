/* assets/js/main.js */

document.addEventListener('DOMContentLoaded', function () {
    console.log('Pawsome Connections Loaded');

    // Simple Toast Notification system if needed
    window.toast = function (message, type = 'info') {
        const toastDiv = document.createElement('div');
        toastDiv.className = `toast toast-${type}`;
        toastDiv.innerText = message;
        document.body.appendChild(toastDiv);
        setTimeout(() => toastDiv.remove(), 3000);
    };

    // Mobile Navigation Toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', function () {
            navLinks.classList.toggle('active');
        });
    }

    // AJAX Form Submission Helper
    const ajaxForms = document.querySelectorAll('.ajax-form');
    ajaxForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = this.querySelector('[type="submit"]');
            const originalText = submitBtn.innerText;

            submitBtn.disabled = true;
            submitBtn.innerText = 'Processing...';

            fetch(this.getAttribute('action'), {
                method: 'POST',
                body: formData
            })
                .then(async response => {
                    const text = await response.text();
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Invalid JSON response:', text);
                        throw new Error('Invalid server response');
                    }
                })
                .then(data => {
                    if (data.success) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            toast(data.message, 'success');
                            if (data.reload) window.location.reload();
                        }
                    } else {
                        toast(data.message || 'An error occurred', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toast(error.message || 'Server error', 'danger');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                });
        });
    });

    // Password Toggle Logic
    const togglePasswords = document.querySelectorAll('.toggle-password');
    togglePasswords.forEach(toggle => {
        toggle.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                }
            }
        });
    });
});

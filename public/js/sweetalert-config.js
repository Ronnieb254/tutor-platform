/**
 * SweetAlert2 Configuration and Helpers
 */

const Swal = window.Swal;

// Configure default settings
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

// Success notification
window.showSuccess = function(title, message = '') {
    return Swal.fire({
        icon: 'success',
        title: title,
        text: message,
        confirmButtonColor: '#10B981',
        confirmButtonText: 'OK'
    });
};

// Error notification
window.showError = function(title, message = '') {
    return Swal.fire({
        icon: 'error',
        title: title,
        text: message,
        confirmButtonColor: '#EF4444',
        confirmButtonText: 'OK'
    });
};

// Confirmation dialog
window.showConfirm = function(title, message, confirmText = 'Yes', cancelText = 'No') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#6D28D9',
        cancelButtonColor: '#6B7280',
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        reverseButtons: true
    });
};

// Toast notification
window.showToast = function(message, icon = 'success') {
    return Toast.fire({
        icon: icon,
        title: message
    });
};

// Loading state
window.showLoading = function(message = 'Loading...') {
    return Swal.fire({
        title: message,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
};

// Hide loading
window.hideLoading = function() {
    Swal.close();
};

// Handle form submission with AJAX and SweetAlert
window.handleFormSubmit = function(form, url, method = 'POST') {
    const formData = new FormData(form);
    
    showLoading('Processing...');
    
    fetch(url, {
        method: method,
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            showSuccess(data.message || 'Success!').then(() => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            });
        } else {
            showError('Error', data.message || 'Something went wrong.');
        }
    })
    .catch(error => {
        hideLoading();
        showError('Error', 'An unexpected error occurred.');
        console.error('Error:', error);
    });
};

// Confirm action before proceeding
window.confirmAction = function(url, method = 'POST', title = 'Are you sure?', message = '') {
    return showConfirm(title, message).then((result) => {
        if (result.isConfirmed) {
            showLoading('Processing...');
            
            return fetch(url, {
                method: method,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                
                if (data.success) {
                    showToast(data.message || 'Success!', 'success');
                    
                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1000);
                    } else {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                } else {
                    showError('Error', data.message || 'Something went wrong.');
                }
                
                return data;
            })
            .catch(error => {
                hideLoading();
                showError('Error', 'An unexpected error occurred.');
                console.error('Error:', error);
            });
        }
    });
};

// Show session join confirmation
window.confirmJoinSession = function(sessionUrl) {
    return showConfirm(
        'Join Session',
        'Are you ready to join this session?',
        'Join Now',
        'Cancel'
    ).then((result) => {
        if (result.isConfirmed) {
            window.location.href = sessionUrl;
        }
    });
};

// Cancel booking with reason
window.cancelBooking = function(cancelUrl) {
    return Swal.fire({
        title: 'Cancel Booking',
        text: 'Please provide a reason for cancellation (optional):',
        input: 'textarea',
        inputPlaceholder: 'Cancellation reason...',
        showCancelButton: true,
        confirmButtonText: 'Cancel Booking',
        cancelButtonText: 'Go Back',
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        reverseButtons: true,
        inputValidator: (value) => {
            if (value && value.length > 500) {
                return 'Reason cannot exceed 500 characters';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading('Cancelling booking...');
            
            return fetch(cancelUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    reason: result.value || null
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                
                if (data.success) {
                    showSuccess('Booking Cancelled', data.message).then(() => {
                        window.location.reload();
                    });
                } else {
                    showError('Error', data.message || 'Failed to cancel booking.');
                }
            })
            .catch(error => {
                hideLoading();
                showError('Error', 'An unexpected error occurred.');
                console.error('Error:', error);
            });
        }
    });
};

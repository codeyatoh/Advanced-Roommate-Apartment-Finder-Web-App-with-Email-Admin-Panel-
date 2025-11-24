/**
 * Toast Notification Helper
 * Uses Toastify.js to show success/error messages
 */

function showToast(message, type = 'success') {
    let backgroundColor;
    
    if (type === 'success') {
        // Green gradient for success
        backgroundColor = "linear-gradient(to right, #00b09b, #96c93d)";
    } else if (type === 'error') {
        // Red gradient for error
        backgroundColor = "linear-gradient(to right, #ff5f6d, #ffc371)";
    } else {
        // Blue default
        backgroundColor = "linear-gradient(to right, #2193b0, #6dd5ed)";
    }

    Toastify({
        text: message,
        duration: 3000,
        close: true,
        gravity: "top", // `top` or `bottom`
        position: "right", // `left`, `center` or `right`
        stopOnFocus: true, // Prevents dismissing of toast on hover
        style: {
            background: backgroundColor,
            borderRadius: "10px",
            fontFamily: "'Inter', sans-serif",
            fontSize: "14px",
            boxShadow: "0 4px 6px rgba(0, 0, 0, 0.1)"
        },
        onClick: function(){} // Callback after click
    }).showToast();
}

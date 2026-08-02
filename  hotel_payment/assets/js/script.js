 
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this?');
}

 
function showToast(message, type) {
    type = type || 'success';
    var toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    toast.innerHTML = message;
    document.body.appendChild(toast);
    setTimeout(function() {
        toast.remove();
    }, 3000);
}

 
function formatDate(date) {
    var d = new Date(date);
    return d.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

 
function formatCurrency(amount) {
    return '$' + Number(amount).toFixed(2);
}

 
document.addEventListener('DOMContentLoaded', function() {
    var currentPage = window.location.pathname.split('/').pop();
    var links = document.querySelectorAll('.sidebar-nav .nav-link');
    
    for (var i = 0; i < links.length; i++) {
        var link = links[i];
        var href = link.getAttribute('href');
        if (href === currentPage) {
            link.classList.add('active');
        }
    }
});
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function logout() {
    fetch('/logout', { method: 'POST' })
    .then(response => {
        if (response.ok) {
            window.location.href = '/login';
        }
    })
    .catch(error => console.error('Logout failed:', error));
}
document.addEventListener('DOMContentLoaded', function () {
    // Add fade-in animation to main container
    const container = document.querySelector('.container-fluid');
    container.classList.add('fade-in');

    // Add ripple effect to buttonscument.addEventListener('DOMContentLoaded', function () {
    // Add animation class to cards
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.classList.add('status-card');
    });

    // Smooth scroll to top when page loads
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });

    // Add hover effect to buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-2px)';
        });
        button.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
        });
    });

    // Add fade-in animation to alert
    const alert = document.querySelector('.alert');
    if (alert) {
        alert.style.opacity = '0';
        alert.style.transition = 'opacity 0.5s ease-in';
        setTimeout(() => {
            alert.style.opacity = '1';
        }, 100);
    }
});

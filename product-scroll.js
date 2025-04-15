function scrollProducts(direction) {
    const scrollContainer = document.querySelector('.products-scroll');
    const scrollAmount = 300; // Количество пикселей для прокрутки

    if (direction === 'left') {
        scrollContainer.scrollBy({
            left: -scrollAmount,
            behavior: 'smooth'
        });
    } else if (direction === 'right') {
        scrollContainer.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
        });
    }
}
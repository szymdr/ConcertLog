function initializeCarousel(carousel) {
    const track = carousel.querySelector(".carousel-track");
    const prevButton = carousel.querySelector(".carousel-button.prev");
    const nextButton = carousel.querySelector(".carousel-button.next");
    const images = Array.from(track.children);

    if (images.length === 0) return; // Skip if no images

    const imageWidth = images[0].getBoundingClientRect().width;

    let currentIndex = 0;

    // Function to update carousel position
    const updateCarousel = () => {
        track.style.transform = `translateX(-${currentIndex * imageWidth}px)`;
        updateButtons();
    };

    // Function to update button visibility
    const updateButtons = () => {
        if (currentIndex === 0) {
            if (prevButton) {
                prevButton.classList.add('hidden');
            }
        } else {
            if (prevButton) {
                prevButton.classList.remove('hidden');
            }
        }

        if (currentIndex === images.length - 1) {
            if (nextButton) {
                nextButton.classList.add('hidden');
            }
        } else {
            if (nextButton) {
                nextButton.classList.remove('hidden');
            }
        }
    };

    // Initialize button visibility
    updateButtons();

    // Event listeners for buttons
    if (nextButton) {
        nextButton.addEventListener("click", () => {
            if (currentIndex < images.length - 1) {
                currentIndex++;
                updateCarousel();
            }
        });
    }

    if (prevButton) {
        prevButton.addEventListener("click", () => {
            if (currentIndex > 0) {
                currentIndex--;
                updateCarousel();
            }
        });
    }
}

// Initialize existing carousels on page load
document.addEventListener("DOMContentLoaded", () => {
    const carousels = document.querySelectorAll(".carousel");
    carousels.forEach(carousel => initializeCarousel(carousel));
});
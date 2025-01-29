document.addEventListener("DOMContentLoaded", () => {
    const carousels = document.querySelectorAll(".carousel");

    carousels.forEach(carousel => {
        const track = carousel.querySelector(".carousel-track");
        const nextButton = carousel.querySelector(".carousel-button.next");
        const prevButton = carousel.querySelector(".carousel-button.prev");
        const images = Array.from(track.children);
        const imageWidth = images[0].getBoundingClientRect().width;

        console.log("Next button:", nextButton);
        console.log("Prev button:", prevButton);

        let currentIndex = 0;

        const updateCarousel = () => {
            console.log(`Updating carousel to index ${currentIndex}`);
            track.style.transform = `translateX(-${currentIndex * imageWidth}px)`;
        };

        if (nextButton) {
            nextButton.addEventListener("click", () => {
                console.log("Next button clicked");
                if (currentIndex < images.length - 1) {
                    currentIndex++;
                    updateCarousel();
                }
            });
        }

        if (prevButton) {
            prevButton.addEventListener("click", () => {
                console.log("Prev button clicked");
                if (currentIndex > 0) {
                    currentIndex--;
                    updateCarousel();
                }
            });
        }
    });
});
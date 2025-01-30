const searchInput = document.querySelector('input[placeholder="Search"]');
const feedContainer = document.querySelector(".feed");

searchInput.addEventListener("keyup", function (event) {
    if (event.key === "Enter") {
        event.preventDefault();

        const data = { search: this.value };

        fetch("/search", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        }).then(function (response) {
            return response.json();
        }).then(function (concerts) {
            feedContainer.innerHTML = "";
            loadConcerts(concerts);
        }).catch(function (error) {
            console.error('Error:', error);
        });
    }
});

function loadConcerts(concerts) {
    concerts.forEach(concert => {
        createConcert(concert);
    });
}

function createConcert(concert) {
    const template = document.querySelector("#post-template");
    if (!template) {
        console.error("Post template not found.");
        return;
    }
    const clone = template.content.cloneNode(true);

    // Fill in images
    const carousel = clone.querySelector(".carousel");
    if (!carousel) {
        console.error("Carousel element not found in the cloned template.");
        return;
    }
    const carouselTrack = carousel.querySelector(".carousel-track");
    carouselTrack.innerHTML = ""; // Clear placeholder image

    concert.images.forEach(img => {
        const newImg = document.createElement("img");
        newImg.src = `public/uploads/concertPhotos/${img}`;
        newImg.alt = "Concert image";
        carouselTrack.appendChild(newImg);
    });

    // If there’s more than one image, add buttons
    if (concert.images.length > 1) {
        const prevButton = document.createElement("button");
        prevButton.classList.add("carousel-button", "prev");
        prevButton.innerText = "<";
        carousel.appendChild(prevButton); // Append to carousel

        const nextButton = document.createElement("button");
        nextButton.classList.add("carousel-button", "next");
        nextButton.innerText = ">";
        carousel.appendChild(nextButton);
    }

    // Fill in textual details
    const titleElement = clone.querySelector("h2");
    const artistElement = clone.querySelector(".artist");
    const dateElement = clone.querySelector(".date");
    const venueElement = clone.querySelector(".venue");
    const locationElement = clone.querySelector(".location");
    const addedByElement = clone.querySelector(".added-by");

    if (titleElement) titleElement.innerText = concert.title;
    if (artistElement) artistElement.innerHTML = `<strong>Artist:</strong> ${concert.artist}`;
    if (dateElement) dateElement.innerHTML = `<strong>Date:</strong> ${concert.date}`;
    if (venueElement) venueElement.innerHTML = `<strong>Venue:</strong> ${concert.venue}`;
    if (locationElement) locationElement.innerHTML = `<strong>Location:</strong> ${concert.location}`;
    if (addedByElement) addedByElement.innerHTML = `<strong>Added by:</strong> ${concert.addedBy}`;

    feedContainer.appendChild(clone);

    initializeCarousel(carousel);
}
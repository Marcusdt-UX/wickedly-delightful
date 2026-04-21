(function () {
  "use strict";

  const rotatorStage = document.getElementById("products-rotator-stage");
  const rotatorPrev = document.getElementById("products-rotator-prev");
  const rotatorNext = document.getElementById("products-rotator-next");
  const rotationImages = [
    "assets/homepage-rotation/wickedly-rotation-01.jpg",
    "assets/homepage-rotation/wickedly-rotation-02.jpg",
    "assets/homepage-rotation/wickedly-rotation-03.jpg",
    "assets/homepage-rotation/wickedly-rotation-04.jpg",
    "assets/homepage-rotation/wickedly-rotation-05.jpg",
    "assets/homepage-rotation/wickedly-rotation-06.jpg",
    "assets/homepage-rotation/wickedly-rotation-07.jpg",
    "assets/homepage-rotation/wickedly-rotation-08.jpg",
    "assets/homepage-rotation/wickedly-rotation-09.jpg",
    "assets/homepage-rotation/wickedly-rotation-10.jpg",
    "assets/homepage-rotation/wickedly-rotation-11.jpg",
    "assets/homepage-rotation/wickedly-rotation-12.jpg",
    "assets/homepage-rotation/wickedly-rotation-13.jpg",
    "assets/homepage-rotation/wickedly-rotation-14.jpg",
    "assets/homepage-rotation/wickedly-rotation-15.jpg",
    "assets/homepage-rotation/wickedly-rotation-16.jpg",
    "assets/homepage-rotation/wickedly-rotation-17.jpg"
  ];

  if (!rotatorStage || !rotationImages.length) {
    return;
  }

  let currentIndex = 0;
  let rotationTimer = null;

  function getVisibleCount() {
    if (window.innerWidth >= 1200) return 4;
    if (window.innerWidth >= 768) return 3;
    return 1;
  }

  function renderRotator() {
    const visibleCount = Math.min(getVisibleCount(), rotationImages.length);
    const cards = [];

    for (let offset = 0; offset < visibleCount; offset += 1) {
      const imageIndex = (currentIndex + offset) % rotationImages.length;
      const imagePath = rotationImages[imageIndex];
      const cardNumber = imageIndex + 1;

      cards.push(
        '<article class="products-rotator-card">'
          + '<div class="products-rotator-frame">'
            + '<img src="' + imagePath + '" alt="Wickedly Delightful wax melt product photo ' + cardNumber + '" loading="lazy" />'
            + '<div class="products-rotator-overlay"></div>'
          + '</div>'
          + '<div class="products-rotator-caption">'
            + '<span class="products-rotator-count">Photo ' + String(cardNumber).padStart(2, "0") + '</span>'
            + '<span class="products-rotator-label">Wickedly Delightful product spotlight</span>'
          + '</div>'
        + '</article>'
      );
    }

    rotatorStage.innerHTML = cards.join("");
  }

  function stepRotator(direction) {
    currentIndex = (currentIndex + direction + rotationImages.length) % rotationImages.length;
    renderRotator();
  }

  function stopRotator() {
    if (rotationTimer) {
      window.clearInterval(rotationTimer);
      rotationTimer = null;
    }
  }

  function startRotator() {
    stopRotator();
    rotationTimer = window.setInterval(function () {
      stepRotator(1);
    }, 4200);
  }

  if (rotatorPrev) {
    rotatorPrev.addEventListener("click", function () {
      stepRotator(-1);
      startRotator();
    });
  }

  if (rotatorNext) {
    rotatorNext.addEventListener("click", function () {
      stepRotator(1);
      startRotator();
    });
  }

  rotatorStage.addEventListener("mouseenter", stopRotator);
  rotatorStage.addEventListener("mouseleave", startRotator);
  window.addEventListener("resize", renderRotator);

  renderRotator();
  startRotator();
})();

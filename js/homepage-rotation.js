(function () {
  "use strict";

  const productsGrid = document.getElementById("products-grid");
  const products = [
    {
      name: "Vanilla Bean Buttercream",
      description: "Rich, creamy vanilla with warm buttercream sweetness.",
      image: "assets/homepage-rotation/wickedly-rotation-01.jpg"
    },
    {
      name: "Vanilla Bean Buttercream",
      description: "A cozy bakery classic with smooth vanilla frosting notes.",
      image: "assets/homepage-rotation/wickedly-rotation-02.jpg"
    },
    {
      name: "Black Raspberry Vanilla",
      description: "Juicy dark berries swirled with soft vanilla cream.",
      image: "assets/homepage-rotation/wickedly-rotation-03.jpg"
    },
    {
      name: "Bibbity Bobbity Boo",
      description: "Sweet and playful with a magical candy-like finish.",
      image: "assets/homepage-rotation/wickedly-rotation-04.jpg"
    },
    {
      name: "Fresh Cotton",
      description: "Clean laundry freshness with airy, comforting softness.",
      image: "assets/homepage-rotation/wickedly-rotation-05.jpg"
    },
    {
      name: "Clean Linen O.E.",
      description: "Crisp linen and fresh-air notes for a just-cleaned vibe.",
      image: "assets/homepage-rotation/wickedly-rotation-06.jpg"
    },
    {
      name: "Banana Pudding Parfait",
      description: "Creamy banana dessert layered with warm vanilla sweetness.",
      image: "assets/homepage-rotation/wickedly-rotation-07.jpg"
    },
    {
      name: "Lemon Pound Cake",
      description: "Bright lemon zest baked into buttery cake goodness.",
      image: "assets/homepage-rotation/wickedly-rotation-08.jpg"
    },
    {
      name: "Eye Candy",
      description: "Sugary, colorful sweetness with a fun fruity twist.",
      image: "assets/homepage-rotation/wickedly-rotation-09.jpg"
    },
    {
      name: "Stress Relief",
      description: "A soothing spa-style blend designed to calm the mood.",
      image: "assets/homepage-rotation/wickedly-rotation-10.jpg"
    },
    {
      name: "Pumpkin Pecan Waffles",
      description: "Warm waffles, toasted pecans, and cozy pumpkin spice.",
      image: "assets/homepage-rotation/wickedly-rotation-11.jpg"
    },
    {
      name: "Apple Spice",
      description: "Crisp orchard apples dusted with cinnamon and clove.",
      image: "assets/homepage-rotation/wickedly-rotation-12.jpg"
    },
    {
      name: "Watermelon",
      description: "Juicy summer melon with bright, refreshing sweetness.",
      image: "assets/homepage-rotation/wickedly-rotation-13.jpg"
    },
    {
      name: "Cereal Milk Swirl",
      description: "Sweet milk and cereal notes that feel nostalgic and fun.",
      image: "assets/homepage-rotation/wickedly-rotation-14.jpg"
    },
    {
      name: "Jamaica Me Crazy",
      description: "A tropical fruit blend with beachy, vacation energy.",
      image: "assets/homepage-rotation/wickedly-rotation-15.jpg"
    },
    {
      name: "Lavender",
      description: "Calming lavender blossoms for a restful atmosphere.",
      image: "assets/homepage-rotation/wickedly-rotation-16.jpg"
    },
    {
      name: "Over the Rainbow",
      description: "A cheerful fruit-and-sugar medley with bright personality.",
      image: "assets/homepage-rotation/wickedly-rotation-17.jpg"
    }
  ];

  if (!productsGrid || products.length < 4) {
    return;
  }

  function shuffledProducts(list) {
    const copy = list.slice();

    for (let i = copy.length - 1; i > 0; i -= 1) {
      const j = Math.floor(Math.random() * (i + 1));
      const temp = copy[i];
      copy[i] = copy[j];
      copy[j] = temp;
    }

    return copy;
  }

  function renderRandomCards() {
    const selected = shuffledProducts(products).slice(0, 4);
    const cards = selected.map(function (product, index) {
      const delayClass = index === 0 ? "" : " reveal-delay-" + index;
      return (
        '<div class="product-card ornate-card ornate-corners reveal' + delayClass + '">' 
          + '<span class="corner-bl"></span><span class="corner-br"></span>'
          + '<div class="product-image">'
            + '<img src="' + product.image + '" alt="' + product.name + ' soy wax melt by Wickedly Delightful Scents" loading="lazy" />'
            + '<div class="product-image-overlay"></div>'
          + '</div>'
          + '<div class="product-info">'
            + '<div class="about-accent-line"></div>'
            + '<h3>' + product.name + '</h3>'
            + '<p>' + product.description + '</p>'
          + '</div>'
        + '</div>'
      );
    });

    productsGrid.innerHTML = cards.join("");

    if ("IntersectionObserver" in window) {
      const cardsToReveal = productsGrid.querySelectorAll(".reveal");
      const observer = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              entry.target.classList.add("visible");
              observer.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.12, rootMargin: "0px 0px -40px 0px" }
      );

      cardsToReveal.forEach(function (card) {
        observer.observe(card);
      });
    } else {
      productsGrid.querySelectorAll(".reveal").forEach(function (card) {
        card.classList.add("visible");
      });
    }
  }

  renderRandomCards();
})();

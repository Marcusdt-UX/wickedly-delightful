/**
 * Wickedly Delightful Scents — Main JS
 * Navigation, scroll reveal, form handling
 */

(function () {
  "use strict";

  // ---- Mobile Navigation Toggle ----
  const navToggle = document.getElementById("nav-toggle");
  const navMobile = document.getElementById("nav-mobile");

  if (navToggle && navMobile) {
    navToggle.addEventListener("click", () => {
      const isOpen = navMobile.classList.toggle("open");
      navToggle.setAttribute("aria-expanded", isOpen);

      // Swap hamburger ↔ X icon
      navToggle.innerHTML = isOpen
        ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>'
        : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>';
    });

    // Close mobile menu on link click
    navMobile.querySelectorAll(".nav-link").forEach((link) => {
      link.addEventListener("click", () => {
        navMobile.classList.remove("open");
        navToggle.innerHTML =
          '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>';
        navToggle.setAttribute("aria-expanded", "false");
      });
    });
  }

  // ---- Sticky Nav Shadow on Scroll ----
  const nav = document.getElementById("nav");

  function updateNavShadow() {
    if (!nav) return;
    if (window.scrollY > 20) {
      nav.classList.add("scrolled");
    } else {
      nav.classList.remove("scrolled");
    }
  }

  window.addEventListener("scroll", updateNavShadow, { passive: true });
  updateNavShadow();

  // ---- Active Nav Link Highlighting ----
  const sections = document.querySelectorAll("section[id]");
  const navLinks = document.querySelectorAll(".nav-links .nav-link, .nav-mobile .nav-link");

  function updateActiveLink() {
    const scrollPos = window.scrollY + 120;

    sections.forEach((section) => {
      const top = section.offsetTop;
      const height = section.offsetHeight;
      const id = section.getAttribute("id");

      if (scrollPos >= top && scrollPos < top + height) {
        navLinks.forEach((link) => {
          link.classList.remove("active");
          if (link.getAttribute("href") === "#" + id) {
            link.classList.add("active");
          }
        });
      }
    });
  }

  window.addEventListener("scroll", updateActiveLink, { passive: true });
  updateActiveLink();

  // ---- Scroll Reveal (Intersection Observer) ----
  const revealEls = document.querySelectorAll(".reveal");

  if ("IntersectionObserver" in window) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("visible");
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: "0px 0px -40px 0px" }
    );

    revealEls.forEach((el) => observer.observe(el));
  } else {
    // Fallback: show everything
    revealEls.forEach((el) => el.classList.add("visible"));
  }

  // ---- Contact Form Handling ----
  const form = document.getElementById("contact-form");

  if (form) {
    form.addEventListener("submit", (e) => {
      e.preventDefault();

      const name = form.elements.name.value.trim();
      const email = form.elements.email.value.trim();
      const message = form.elements.message.value.trim();

      if (!name || !email || !message) return;

      // Simple UI feedback (no backend)
      const btn = form.querySelector(".form-submit");
      const originalText = btn.innerHTML;

      btn.innerHTML = "Thank you! We'll be in touch.";
      btn.style.background = "var(--primary)";
      btn.style.color = "var(--foreground)";
      btn.disabled = true;

      form.reset();

      setTimeout(() => {
        btn.innerHTML = originalText;
        btn.style.background = "";
        btn.style.color = "";
        btn.disabled = false;
      }, 3000);
    });
  }

  // ---- Smooth scroll offset for fixed nav ----
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", (e) => {
      const targetId = anchor.getAttribute("href");
      if (!targetId || targetId === "#") return;

      const target = document.querySelector(targetId);
      if (!target) return;

      e.preventDefault();
      const navHeight = nav ? nav.offsetHeight : 0;
      const top = target.offsetTop - navHeight;

      window.scrollTo({ top, behavior: "smooth" });
    });
  });
})();

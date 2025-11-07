// Smooth scroll for internal links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    document.querySelector(this.getAttribute("href")).scrollIntoView({
      behavior: "smooth"
    });
  });
});

// Fade-in animation on scroll
const revealElements = document.querySelectorAll('.reveal');
const revealOnScroll = () => {
  const triggerBottom = window.innerHeight * 0.85;
  revealElements.forEach(el => {
    const boxTop = el.getBoundingClientRect().top;
    if (boxTop < triggerBottom) el.classList.add('active');
    else el.classList.remove('active');
  });
};
window.addEventListener('scroll', revealOnScroll);
revealOnScroll();

// Mobile menu toggle
const menuBtn = document.querySelector('#menu-btn');
const navMenu = document.querySelector('#nav-menu');
if (menuBtn) {
  menuBtn.addEventListener('click', () => {
    navMenu.classList.toggle('hidden');
    navMenu.classList.toggle('flex');
  });
}

// Resume button click handler
const resumeBtn = document.querySelector('#resume-btn');
if (resumeBtn) {
  resumeBtn.addEventListener('click', () => {
    window.open('/assets/Ritik_Kumar_Resume.pdf', '_blank');
  });
}

// Typing animation (optional)
const typedText = document.querySelector('.typed-text');
if (typedText) {
  const words = ["an IT Engineer.", "a Web Developer.", "the Founder of CreVate Technologies."];
  let i = 0, j = 0, isDeleting = false;

  function type() {
    const currentWord = words[i];
    if (!isDeleting) {
      typedText.textContent = currentWord.substring(0, j + 1);
      j++;
      if (j === currentWord.length) {
        isDeleting = true;
        setTimeout(type, 1500);
        return;
      }
    } else {
      typedText.textContent = currentWord.substring(0, j - 1);
      j--;
      if (j === 0) {
        isDeleting = false;
        i = (i + 1) % words.length;
      }
    }
    setTimeout(type, isDeleting ? 60 : 120);
  }
  type();
}
// Contact Form Submission
document.addEventListener("DOMContentLoaded", () => {
  const contactForm = document.getElementById("contactForm");
  const responseMsg = document.getElementById("formResponse");

  contactForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    responseMsg.textContent = "Sending...";
    responseMsg.style.color = "#ccc";

    const formData = new FormData(contactForm);

    try {
      const res = await fetch("contact.php", {
        method: "POST",
        body: formData
      });
      const data = await res.json();

      if (data.status === "success") {
        responseMsg.textContent = data.message;
        responseMsg.style.color = "limegreen";
        contactForm.reset();
      } else {
        responseMsg.textContent = data.message;
        responseMsg.style.color = "red";
      }
    } catch (err) {
      responseMsg.textContent = "Network error. Try again later.";
      responseMsg.style.color = "red";
    }
  });
});
function scrollToSection() {
  const nextSection = document.querySelector('#Projects'); // Replace with your real section ID
  if (nextSection) {
    nextSection.scrollIntoView({ behavior: 'smooth' });
  }
}
window.addEventListener('scroll', () => {
  const scrollIndicator = document.querySelector('.scroll-indicator');
  if (window.scrollY > 100) {
    scrollIndicator.style.opacity = '0';
  } else {
    scrollIndicator.style.opacity = '1';
  }
});
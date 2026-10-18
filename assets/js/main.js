document.addEventListener('DOMContentLoaded', () => {
  // Initialize Lucide Icons
  if (typeof lucide !== 'undefined') {
    lucide.createIcons();
  }

  // --- Navbar Scroll State ---
  const navbar = document.querySelector('.navbar');
  if (navbar) {
    const onScroll = () => {
      if (window.scrollY > 60) {
        navbar.classList.add('is-scrolled');
      } else {
        navbar.classList.remove('is-scrolled');
      }
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll(); // initial check
  }

  // --- Mobile Menu Toggle ---
  const mobileToggle = document.querySelector('.mobile-toggle');
  const mobileMenu = document.querySelector('.mobile-menu');
  const mobileLinks = document.querySelectorAll('.mobile-menu .nav-link');
  let isMenuOpen = false;

  const toggleMenu = () => {
    isMenuOpen = !isMenuOpen;
    mobileToggle.setAttribute('aria-expanded', isMenuOpen);
    mobileMenu.classList.toggle('is-open');
    document.body.style.overflow = isMenuOpen ? 'hidden' : '';
  };

  if (mobileToggle && mobileMenu) {
    mobileToggle.addEventListener('click', toggleMenu);

    mobileLinks.forEach(link => {
      link.addEventListener('click', () => {
        if (isMenuOpen) toggleMenu();
      });
    });

    // Close on escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && isMenuOpen) {
        toggleMenu();
        mobileToggle.focus();
      }
    });
  }

  // --- Active Nav Indicator ---
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.desktop-nav .nav-link');
  const indicator = document.querySelector('.nav-indicator');

  if (sections.length > 0 && indicator && navLinks.length > 0) {
    const observerOptions = {
      root: null,
      rootMargin: '-45% 0px -50% 0px',
      threshold: 0
    };

    const sectionObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const id = entry.target.getAttribute('id');
          // Find matching link
          navLinks.forEach(link => {
            if (link.getAttribute('href') === `#${id}`) {
              link.classList.add('active');
              // Update indicator position
              const linkRect = link.getBoundingClientRect();
              const navRect = link.closest('.desktop-nav').getBoundingClientRect();
              
              indicator.style.width = `${linkRect.width}px`;
              indicator.style.transform = `translateX(${linkRect.left - navRect.left}px)`;
            } else {
              link.classList.remove('active');
            }
          });
        }
      });
    }, observerOptions);

    sections.forEach(sec => sectionObserver.observe(sec));
  }

  // --- Project Filtering ---
  const filterPills = document.querySelectorAll('.filter-pill');
  const projectCards = document.querySelectorAll('.project-card');
  const noProjectsMsg = document.getElementById('no-projects-msg');

  if (filterPills.length > 0 && projectCards.length > 0) {
    // Expose reset globally for the "Show all" button
    window.resetFilter = () => {
      document.querySelector('.filter-pill[data-filter="all"]')?.click();
    };

    filterPills.forEach(pill => {
      pill.addEventListener('click', () => {
        // Update active state
        filterPills.forEach(p => p.classList.remove('active'));
        pill.classList.add('active');

        const filterValue = pill.getAttribute('data-filter');
        let visibleCount = 0;

        projectCards.forEach(card => {
          // Reset animations for re-trigger
          card.style.animation = 'none';
          card.offsetHeight; // trigger reflow

          if (filterValue === 'all' || card.getAttribute('data-category') === filterValue) {
            card.style.display = '';
            // Only add animation if it's not the featured card taking up full width
            // Or add a simple fade-in. We'll use anim-fade-in
            card.style.animation = `fadeIn 400ms var(--ease-out) forwards`;
            visibleCount++;
          } else {
            card.style.display = 'none';
          }
        });

        // Show empty state if needed
        if (visibleCount === 0 && noProjectsMsg) {
          noProjectsMsg.style.display = 'block';
        } else if (noProjectsMsg) {
          noProjectsMsg.style.display = 'none';
        }
      });
    });
  }

  // --- Contact Form AJAX ---
  const contactForm = document.getElementById('contact-form');
  if (contactForm) {
    contactForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const submitBtn = document.getElementById('submit-btn');
      const msgContainer = document.getElementById('form-message-container');
      const msgTitle = document.getElementById('form-message-title');
      const msgBody = document.getElementById('form-message-body');
      
      // Reset states
      msgContainer.style.display = 'none';
      document.querySelectorAll('.error-msg').forEach(el => el.style.display = 'none');
      document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
      
      // Loading state
      const originalBtnText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<span style="display:inline-block; animation: spin 1s linear infinite;"><i data-lucide="loader-2" style="width:18px;height:18px;"></i></span> Sending...';
      submitBtn.disabled = true;
      if (typeof lucide !== 'undefined') lucide.createIcons();

      try {
        const formData = new FormData(contactForm);
        const response = await fetch(contactForm.action, {
          method: 'POST',
          body: formData,
          headers: { 'Accept': 'application/json' }
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
          // Success UI
          contactForm.reset();
          msgContainer.style.display = 'block';
          msgContainer.style.backgroundColor = 'var(--support-bg)';
          msgContainer.style.border = '1px solid var(--support)';
          msgContainer.style.color = 'var(--support)';
          msgTitle.textContent = 'Message sent';
          msgBody.textContent = data.message;
        } else if (data.errors) {
          // Validation Errors
          Object.keys(data.errors).forEach(key => {
            const input = document.getElementById(key);
            if (input) {
              input.classList.add('is-invalid');
              const errorMsg = input.nextElementSibling;
              if (errorMsg && errorMsg.classList.contains('error-msg')) {
                errorMsg.textContent = data.errors[key];
                errorMsg.style.display = 'block';
              }
            }
          });
        } else {
          // General Error
          throw new Error(data.message || 'Something went wrong.');
        }
      } catch (err) {
        // Error UI
        msgContainer.style.display = 'block';
        msgContainer.style.backgroundColor = 'var(--danger-bg)';
        msgContainer.style.border = '1px solid var(--danger)';
        msgContainer.style.color = 'var(--danger)';
        msgTitle.textContent = 'Message failed';
        msgBody.textContent = err.message || 'Please try again or email me directly.';
      } finally {
        // Reset button
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
        if (typeof lucide !== 'undefined') lucide.createIcons();
      }
    });
  }
});


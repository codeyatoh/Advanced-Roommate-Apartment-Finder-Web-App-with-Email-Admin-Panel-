/**
 * Carousel Component
 * Handles auto-play, manual navigation, and infinite loop functionality
 */

class Carousel {
  constructor(element, options = {}) {
    this.carousel = element;
    this.slides = element.querySelectorAll('.carousel-slide');
    this.dots = element.querySelectorAll('.carousel-dot');
    this.prevBtn = element.querySelector('.carousel-arrow-prev');
    this.nextBtn = element.querySelector('.carousel-arrow-next');
    
    this.currentIndex = 0;
    this.autoPlayInterval = options.interval || 6000;
    this.autoPlayTimer = null;
    this.isPlaying = options.autoPlay !== false;
    
    this.init();
  }
  
  init() {
    // Show first slide
    this.showSlide(0);
    
    // Set up event listeners
    if (this.prevBtn) {
      this.prevBtn.addEventListener('click', () => this.prevSlide());
    }
    
    if (this.nextBtn) {
      this.nextBtn.addEventListener('click', () => this.nextSlide());
    }
    
    // Dot navigation
    this.dots.forEach((dot, index) => {
      dot.addEventListener('click', () => this.goToSlide(index));
    });
    
    // Pause on hover
    this.carousel.addEventListener('mouseenter', () => this.pause());
    this.carousel.addEventListener('mouseleave', () => this.resume());
    
    // Start auto-play
    if (this.isPlaying) {
      this.play();
    }
  }
  
  showSlide(index) {
    // Remove active class from all slides and dots
    this.slides.forEach(slide => slide.classList.remove('active'));
    this.dots.forEach(dot => dot.classList.remove('active'));
    
    // Add active class to current slide and dot
    this.slides[index].classList.add('active');
    if (this.dots[index]) {
      this.dots[index].classList.add('active');
    }
    
    this.currentIndex = index;
  }
  
  nextSlide() {
    let nextIndex = this.currentIndex + 1;
    if (nextIndex >= this.slides.length) {
      nextIndex = 0; // Loop back to first slide
    }
    this.showSlide(nextIndex);
  }
  
  prevSlide() {
    let prevIndex = this.currentIndex - 1;
    if (prevIndex < 0) {
      prevIndex = this.slides.length - 1; // Loop to last slide
    }
    this.showSlide(prevIndex);
  }
  
  goToSlide(index) {
    this.showSlide(index);
    this.resetAutoPlay();
  }
  
  play() {
    this.autoPlayTimer = setInterval(() => {
      this.nextSlide();
    }, this.autoPlayInterval);
  }
  
  pause() {
    if (this.autoPlayTimer) {
      clearInterval(this.autoPlayTimer);
      this.autoPlayTimer = null;
    }
  }
  
  resume() {
    if (this.isPlaying && !this.autoPlayTimer) {
      this.play();
    }
  }
  
  resetAutoPlay() {
    this.pause();
    this.resume();
  }
  
  destroy() {
    this.pause();
    // Remove event listeners if needed
  }
}

// Initialize all carousels on the page
function initCarousels() {
  const carousels = document.querySelectorAll('.carousel');
  carousels.forEach(carousel => {
    new Carousel(carousel, {
      autoPlay: true,
      interval: 6000
    });
  });
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initCarousels);
} else {
  initCarousels();
}

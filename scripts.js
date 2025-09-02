// JavaScript for BeTravel website functionality

// Variable and constant examples
let userName = '';
const welcomeMessage = 'Bienvenido a BeTravel!';
const carouselAutoplayInterval = 5000; // 5 seconds

// Initialize all functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeCarousels();
    initializeAnimations();
    initializeInteractivity();
});

// Initialize carousel functionality
function initializeCarousels() {
    // Services Carousel
    const servicesCarousel = document.getElementById('servicesCarousel');
    if (servicesCarousel) {
        const carousel = new bootstrap.Carousel(servicesCarousel, {
            interval: carouselAutoplayInterval,
            wrap: true,
            pause: 'hover'
        });
    }

    // Promo Destinations Carousel
    const promoCarousel = document.getElementById('promoCarousel');
    if (promoCarousel) {
        const carousel = new bootstrap.Carousel(promoCarousel, {
            interval: carouselAutoplayInterval + 1000,
            wrap: true,
            pause: 'hover'
        });
    }

    // Packages Carousel
    const packagesCarousel = document.getElementById('packagesCarousel');
    if (packagesCarousel) {
        const carousel = new bootstrap.Carousel(packagesCarousel, {
            interval: carouselAutoplayInterval + 2000,
            wrap: true,
            pause: 'hover'
        });
    }
}

// Initialize scroll animations
function initializeAnimations() {
    // Add fade-in animation to sections when they come into view
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
            }
        });
    }, observerOptions);

    // Observe all sections
    const sections = document.querySelectorAll('section');
    sections.forEach(section => {
        observer.observe(section);
    });
}

// Initialize interactive elements
function initializeInteractivity() {
    // Add click tracking for service cards
    const serviceCards = document.querySelectorAll('.service-slide, .promo-card, .destination-card, .package-card');
    serviceCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Add ripple effect
            createRippleEffect(e, this);
        });
    });

    // Add hover effects for buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Add smooth scrolling for internal links
    const internalLinks = document.querySelectorAll('a[href^="#"]');
    internalLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Create ripple effect on click
function createRippleEffect(event, element) {
    const ripple = document.createElement('span');
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    ripple.classList.add('ripple');
    
    // Add ripple styles
    ripple.style.position = 'absolute';
    ripple.style.borderRadius = '50%';
    ripple.style.background = 'rgba(76, 194, 188, 0.3)';
    ripple.style.transform = 'scale(0)';
    ripple.style.animation = 'ripple 0.6s linear';
    ripple.style.pointerEvents = 'none';
    
    element.style.position = 'relative';
    element.style.overflow = 'hidden';
    element.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

// Function to show welcome alert on button click (legacy function)
function showWelcomeAlert() {
    alert(welcomeMessage);
}

// Function to toggle the responsive menu
function toggleMenu() {
    const nav = document.getElementById('navbarNav');
    if (nav.classList.contains('show')) {
        nav.classList.remove('show');
    } else {
        nav.classList.add('show');
    }
}

// Enhanced user interaction functions
function askUserName() {
    userName = prompt('Por favor, ingresa tu nombre para personalizar tu experiencia:');
    if (userName && userName.trim() !== '') {
        const confirmed = confirm(`¿Es correcto tu nombre: ${userName}?`);
        if (confirmed) {
            alert(`¡Gracias, ${userName}! Disfruta explorando nuestros destinos.`);
            updateWelcomeMessage(userName);
            localStorage.setItem('betravelUserName', userName);
        } else {
            askUserName(); // Ask again if not confirmed
        }
    } else if (userName !== null) { // User clicked OK but entered empty string
        alert('Por favor, ingresa un nombre válido.');
        askUserName();
    }
    // If user clicked Cancel (userName === null), do nothing
}

// DOM manipulation: update welcome message dynamically
function updateWelcomeMessage(name) {
    const header = document.querySelector('header p');
    if (header) {
        header.textContent = `Tu agencia de viajes de confianza, ${name}`;
    }
    
    // Also update any other personalized elements
    const personalizedElements = document.querySelectorAll('[data-personalize]');
    personalizedElements.forEach(element => {
        const originalText = element.getAttribute('data-original-text') || element.textContent;
        element.setAttribute('data-original-text', originalText);
        element.textContent = originalText.replace('{{name}}', name);
    });
}

// Check for returning user - MEJORADO: Solo pregunta una vez y mantiene el nombre
function checkReturningUser() {
    const savedName = localStorage.getItem('betravelUserName');
    if (savedName) {
        // Si ya hay un nombre guardado, usarlo directamente sin preguntar
        userName = savedName;
        updateWelcomeMessage(userName);
    } else {
        // Solo preguntar el nombre si no hay uno guardado
        setTimeout(askUserName, 2000); // Delay to let page load
    }
}

// Package booking simulation
function bookPackage(packageName, price) {
    if (confirm(`¿Deseas reservar el ${packageName} por ${price}?`)) {
        alert(`¡Excelente elección! Te contactaremos pronto para confirmar tu reserva del ${packageName}.`);
        // Here you could integrate with a real booking system
        trackBookingAttempt(packageName, price);
    }
}

// Track user interactions (for analytics)
function trackBookingAttempt(packageName, price) {
    console.log(`Booking attempt: ${packageName} - ${price}`);
    // Here you could send data to analytics service
}

// Destination interest tracking
function trackDestinationInterest(destination) {
    console.log(`User showed interest in: ${destination}`);
    // Store user preferences
    let interests = JSON.parse(localStorage.getItem('destinationInterests') || '[]');
    if (!interests.includes(destination)) {
        interests.push(destination);
        localStorage.setItem('destinationInterests', JSON.stringify(interests));
    }
}

// Add CSS for ripple animation
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Initialize everything when page loads - MEJORADO: Pregunta una vez y mantiene el nombre
window.addEventListener('load', function() {
    // Siempre verificar si hay un nombre guardado y aplicarlo
    const savedName = localStorage.getItem('betravelUserName');
    if (savedName) {
        // Si ya hay un nombre guardado, usarlo en todas las páginas
        userName = savedName;
        updateWelcomeMessage(userName);
    } else {
        // Solo preguntar el nombre si no hay uno guardado y estamos en la página principal
        if ((window.location.pathname.includes('index.html') || window.location.pathname.endsWith('/')) && !sessionStorage.getItem('userPromptShown')) {
            checkReturningUser();
            sessionStorage.setItem('userPromptShown', 'true');
        }
    }
});

// Add event listeners for package booking buttons
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-success')) {
        const card = e.target.closest('.package-card');
        if (card) {
            const packageName = card.querySelector('h5').textContent;
            const price = card.querySelector('.price').textContent;
            bookPackage(packageName, price);
        }
    }
    
    // Track destination interest
    if (e.target.classList.contains('btn-outline-primary')) {
        const card = e.target.closest('.destination-card');
        if (card) {
            const destination = card.querySelector('h5').textContent;
            trackDestinationInterest(destination);
        }
    }
});

// Funciones para destinos favoritos
function addToFavorites(destino) {
    fetch('favoritos.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `accion=agregar&destino=${encodeURIComponent(destino)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadFavoriteDestinations();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al agregar a favoritos');
    });
}

function removeFromFavorites(destino) {
    fetch('favoritos.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `accion=eliminar&destino=${encodeURIComponent(destino)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadFavoriteDestinations();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar de favoritos');
    });
}

function loadFavoriteDestinations() {
    fetch('favoritos.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'accion=listar'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayFavoriteDestinations(data.data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function displayFavoriteDestinations(favoritos) {
    const container = document.getElementById('favoriteDestinations');
    if (!container) return;
    
    if (favoritos.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center">
                <p class="text-muted">Aún no has agregado destinos a tus favoritos. ¡Haz clic en el corazón de cualquier destino para agregarlo!</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    favoritos.forEach(favorito => {
        html += `
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5><i class="fas fa-heart text-danger"></i> ${favorito.destino}</h5>
                        <p class="small text-muted">Agregado: ${favorito.fecha_formateada}</p>
                        <button class="btn btn-outline-danger btn-sm" onclick="removeFromFavorites('${favorito.destino}')">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function viewDestinationDetails(destino) {
    alert(`Mostrando detalles de ${destino}. Esta funcionalidad se puede expandir para mostrar información detallada del destino.`);
}

// Funciones para filtros de destinos
function filterDestinations() {
    const tipo = document.getElementById('destinationType')?.value;
    const presupuesto = document.getElementById('budget')?.value;
    
    console.log('Filtrando por:', { tipo, presupuesto });
    // Aquí se puede implementar la lógica de filtrado
}

function searchDestinations() {
    const tipo = document.getElementById('destinationType')?.value;
    const presupuesto = document.getElementById('budget')?.value;
    
    let mensaje = 'Buscando destinos';
    if (tipo) mensaje += ` de tipo: ${tipo}`;
    if (presupuesto) mensaje += ` con presupuesto: ${presupuesto}`;
    
    alert(mensaje + '. Esta funcionalidad se puede expandir para mostrar resultados filtrados.');
}

// Validación del formulario de contacto
function validateContactForm() {
    const nombre = document.getElementById('nombre').value.trim();
    const email = document.getElementById('email').value.trim();
    const asunto = document.getElementById('asunto').value;
    const mensaje = document.getElementById('mensaje').value.trim();
    
    if (!nombre) {
        alert('Por favor, ingresa tu nombre');
        return false;
    }
    
    if (!email) {
        alert('Por favor, ingresa tu email');
        return false;
    }
    
    if (!validateEmail(email)) {
        alert('Por favor, ingresa un email válido');
        return false;
    }
    
    if (!asunto) {
        alert('Por favor, selecciona un asunto');
        return false;
    }
    
    if (!mensaje) {
        alert('Por favor, ingresa tu mensaje');
        return false;
    }
    
    return true;
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Procesar formulario de contacto con AJAX
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validateContactForm()) {
                return;
            }
            
            const formData = new FormData(this);
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
            button.disabled = true;
            
            fetch('procesar_contacto.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    this.reset();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al enviar el mensaje. Por favor, inténtalo de nuevo.');
            })
            .finally(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            });
        });
    }
    
    // Cargar favoritos si estamos en la página de destinos
    if (document.getElementById('favoriteDestinations')) {
        loadFavoriteDestinations();
    }
});

// Smooth scroll to top function
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Add scroll to top button functionality
window.addEventListener('scroll', function() {
    const scrollButton = document.getElementById('scrollToTop');
    if (scrollButton) {
        if (window.pageYOffset > 300) {
            scrollButton.style.display = 'block';
        } else {
            scrollButton.style.display = 'none';
        }
    }
});

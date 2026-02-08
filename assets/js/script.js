window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        nav.classList.add('active');

    } else {
        nav.classList.remove('active');
    }
});

// Hamburger 
const hambergerOpen = document.querySelector('.hamburger-open');
const hambergerClose = document.querySelector('.hamburger-close');
const navbar = document.querySelector('.nav-inner');
const nav = document.querySelector('.nav');
hambergerOpen.addEventListener('click', () => {
    nav.style.height = "350px";
    nav.style.background = "white";
    navbar.style.height = "350px";

    hambergerOpen.style.display = "none";
    hambergerClose.style.display = "block";
})
hambergerClose.addEventListener('click', () => {
    nav.style.height = "70px";
    navbar.style.height = "70px";
    hambergerClose.style.display = "none";
    hambergerOpen.style.display = "block";
})

// Reveal 
const elements = document.querySelectorAll(".reveal");
const elementsAnti = document.querySelectorAll(".reveal-anti");

const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add("active");
        }
    });
}, {
    threshold: 0.15
});

if (elements) {
    elements.forEach(el => observer.observe(el));
    elementsAnti.forEach(el => observer.observe(el));
}

// Services 
const services = document.getElementById('services');
const serviceCard = document.querySelector('.sec-services-cards');

if (services && serviceCard) {
    services.addEventListener('click', (e) => {
        e.stopPropagation(); // prevent document click
        serviceCard.classList.toggle('active');
    });

    // close when clicking outside
    document.addEventListener('click', () => {
        serviceCard.classList.remove('active');
    });

    // prevent closing when clicking inside dropdown
    serviceCard.addEventListener('click', (e) => {
        e.stopPropagation();
    });
}

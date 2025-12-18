// Cambia el fondo del header al hacer scroll
window.addEventListener('scroll', function() {
    const header = document.querySelector('header');
    if (window.scrollY > 50) {
        header.style.backgroundColor = '#082d4a';
    } else {
        header.style.backgroundColor = '#0a3d62';
    }
});

// Botón "Volver arriba"
const scrollBtn = document.createElement('button');
scrollBtn.textContent = '↑';
scrollBtn.id = 'scrollTopBtn';
document.body.appendChild(scrollBtn);

// Estilos del botón
scrollBtn.style.position = 'fixed';
scrollBtn.style.bottom = '25px';
scrollBtn.style.right = '25px';
scrollBtn.style.display = 'none';
scrollBtn.style.background = '#ffbb33';
scrollBtn.style.border = 'none';
scrollBtn.style.padding = '10px 15px';
scrollBtn.style.borderRadius = '8px';
scrollBtn.style.cursor = 'pointer';
scrollBtn.style.fontSize = '18px';
scrollBtn.style.boxShadow = '0 3px 6px rgba(0,0,0,0.2)';

// Mostrar/ocultar el botón según el scroll
window.addEventListener('scroll', function() {
    scrollBtn.style.display = window.scrollY > 400 ? 'block' : 'none';
});

// Acción al hacer clic
scrollBtn.addEventListener('click', function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

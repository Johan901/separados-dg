document.addEventListener('DOMContentLoaded', function () {
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const dropdown = document.querySelector('.dropdown-menu');

    // Mostrar/ocultar menú al hacer clic en el ícono de la hamburguesa
    hamburgerMenu.addEventListener('click', function (event) {
        dropdown.classList.toggle('show-dropdown');
        event.stopPropagation(); // Evita cerrar inmediatamente el menú tras abrirlo
    });

    // Evento para cerrar el menú al hacer clic fuera de él
    document.addEventListener('click', function (event) {
        // Verificar si el clic ocurrió fuera del menú y fuera del ícono de la hamburguesa
        if (!dropdown.contains(event.target) && !hamburgerMenu.contains(event.target)) {
            dropdown.classList.remove('show-dropdown');
        }
    });

    // Si el menú está abierto y se presiona "Esc", también se cierra
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && dropdown.classList.contains('show-dropdown')) {
            dropdown.classList.remove('show-dropdown');
        }
    });
});
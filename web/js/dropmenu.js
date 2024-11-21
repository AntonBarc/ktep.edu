document.querySelector('.profile-icon').addEventListener('click', function (event) {
    event.preventDefault();
    const dropdownMenu = document.querySelector('.dropdown-menu');

    // Проверка переполнения
    const rect = dropdownMenu.getBoundingClientRect();
    if (rect.right > window.innerWidth) {
        dropdownMenu.classList.add('adjust-left'); // Меню смещается влево
    } else {
        dropdownMenu.classList.remove('adjust-left'); // Обычное положение
    }

    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
});

// Закрытие меню при клике вне его
document.addEventListener('click', function (event) {
    const profileMenu = document.querySelector('.profile-menu');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    if (!profileMenu.contains(event.target)) {
        dropdownMenu.style.display = 'none';
    }
});
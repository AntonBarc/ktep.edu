document.addEventListener('DOMContentLoaded', () => {
  const links = document.querySelectorAll('.icon-link');
  const currentUrl = window.location.pathname; // Текущий путь страницы

  links.forEach(link => {
      if (link.getAttribute('href') === currentUrl) {
          link.classList.add('active'); // Добавляем класс active к текущей ссылке
      } else {
          link.classList.remove('active'); // Убираем класс active у других
      }
  });
});
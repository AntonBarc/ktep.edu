
function uploadFile(input) {
    if (input.files.length > 0) {
        const form = document.getElementById('upload-form');
        const formData = new FormData(form);

        // Отправка файла через AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Файл успешно загружен');
                updateTable(); // Обновление таблицы
            } else {
                alert('Ошибка при загрузке файла');
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert('Ошибка при отправке запроса');
        });
    }
}

function updateTable() {
    fetch('your-url-to-fetch-table-data') // Укажите URL, который возвращает обновленные данные таблицы
        .then(response => response.text())
        .then(html => {
            document.querySelector('.content-table tbody').innerHTML = html;
        })
        .catch(error => console.error('Ошибка при обновлении таблицы:', error));
}

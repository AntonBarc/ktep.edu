<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Учебные материалы';
?>

<div class="container">
    <main class="content">
        <!-- Заголовок страницы -->
        <div class="page-header">
            <h1>Учебные материалы</h1>
            <div class="action-buttons">
                <button class="btn btn-primary" id="add-material-btn">Создать</button>
                <button class="btn btn-secondary" id="upload-material-btn">Загрузить</button>
            </div>
        </div>

        <!-- Таблица материалов -->
        <section class="materials-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Тип</th>
                        <th>Автор</th>
                        <th>Добавлено</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody id="materials-list">
                    <!-- Динамический контент -->
                </tbody>
            </table>
        </section>
    </main>
</div>

<!-- Модальные окна -->
<div id="add-material-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Создать новый материал</h2>
        <form id="add-material-form">
            <div class="form-group">
                <label for="material-name">Название</label>
                <input type="text" id="material-name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="material-type">Тип</label>
                <select id="material-type" class="form-control">
                    <option value="Курс">Курс</option>
                    <option value="Документ">Документ</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Сохранить</button>
        </form>
    </div>
</div>

<div id="upload-material-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Загрузить материал</h2>
        <form id="upload-material-form" enctype="multipart/form-data">
            <div class="form-group">
                <label for="material-file">Выберите файл</label>
                <input type="file" id="material-file" class="form-control" name="file" required>
            </div>
            <button type="submit" class="btn btn-success">Загрузить</button>
        </form>
    </div>
</div>

<!-- Скрипты -->
<script>
    // Открытие и закрытие модальных окон
    const addMaterialBtn = document.getElementById('add-material-btn');
    const uploadMaterialBtn = document.getElementById('upload-material-btn');
    const addMaterialModal = document.getElementById('add-material-modal');
    const uploadMaterialModal = document.getElementById('upload-material-modal');
    const closeButtons = document.querySelectorAll('.close');

    addMaterialBtn.addEventListener('click', () => addMaterialModal.style.display = 'block');
    uploadMaterialBtn.addEventListener('click', () => uploadMaterialModal.style.display = 'block');
    closeButtons.forEach(btn => btn.addEventListener('click', () => {
        addMaterialModal.style.display = 'none';
        uploadMaterialModal.style.display = 'none';
    }));

    // AJAX для отправки данных
    const addMaterialForm = document.getElementById('add-material-form');
    const uploadMaterialForm = document.getElementById('upload-material-form');

    addMaterialForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const data = {
            name: document.getElementById('material-name').value,
            type: document.getElementById('material-type').value,
        };

        fetch('<?= Url::to(['materials/create']) ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>' },
            body: JSON.stringify(data),
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  alert('Материал успешно создан!');
                  location.reload();
              } else {
                  alert('Ошибка при создании материала');
              }
          });
    });

    uploadMaterialForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(uploadMaterialForm);

        fetch('<?= Url::to(['materials/upload']) ?>', {
            method: 'POST',
            body: formData,
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  alert('Материал успешно загружен!');
                  location.reload();
              } else {
                  alert('Ошибка при загрузке материала');
              }
          });
    });
</script>

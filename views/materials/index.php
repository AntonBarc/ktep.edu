<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Учебные материалы';
?>
<div class="container">
<main class="content">
<h1><?= Html::encode($this->title) ?></h1>

<p><?= Html::a('Добавить материал', ['create'], ['class' => 'btn btn-success']) ?></p>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Название</th>
            <th>Тип</th>
            <th>Автор</th>
            <th>Дата создания</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($materials as $material): ?>
            <tr>
                <td><?= Html::encode($material->name) ?></td>
                <td><?= Html::encode($material->type) ?></td>
                <td><?= Html::encode($material->author_id) ?></td>
                <td><?= Html::encode($material->created_at) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</main>
</div>

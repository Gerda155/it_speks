<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="files/style.css">
    <link rel="shortcut icon" href="images/lvt.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="files/script.js" defer></script>
    <title>Document</title>
</head>
<body>
<div class="sidebar">
    <h2>Панель</h2>
    <div class="menu-item active">Новости</div>
    <div class="menu-item">Вакансии</div>
    <div class="menu-item">Модераторы</div>
  </div>
  <div class="main">
    <div class="top-bar">
      <h1>Добро пожаловать, Админ</h1>
      <div class="admin-info">
        <span>AdminName</span>
        <button>Выход</button>
      </div>
    </div>

    <div class="stats">
      <div class="card">
        <h3>Посетители</h3>
        <p>1,234</p>
      </div>
      <div class="card">
        <h3>Просмотры новостей</h3>
        <p>8,912</p>
      </div>
      <div class="card">
        <h3>Активные вакансии</h3>
        <p>57</p>
      </div>
    </div>

    <div class="data-table">
      <div class="sort">
        <label for="sort">Сортировать по:</label>
        <select id="sort">
          <option>Дате</option>
          <option>Популярности</option>
        </select>
      </div>
      <table width="100%">
        <thead>
          <tr>
            <th>Заголовок</th>
            <th>Категория</th>
            <th>Дата</th>
            <th>Просмотры</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Новая уязвимость в Linux</td>
            <td>Новости</td>
            <td>10.05.2025</td>
            <td>540</td>
          </tr>
          <tr>
            <td>Вакансия: Junior Backend Dev</td>
            <td>Вакансии</td>
            <td>09.05.2025</td>
            <td>210</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
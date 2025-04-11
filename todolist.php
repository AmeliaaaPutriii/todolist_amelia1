<?php
session_start();
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'db_todolist');

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task'])) {
    $id_pengguna = $_SESSION['id_pengguna'];
    $task = $conn->real_escape_string($_POST['task']);
    $deadline = $_POST['deadline'];
    $deskripsi_tugas = $conn->real_escape_string($_POST['deskripsi_tugas']);
    $prioritas = $_POST['prioritas'];

    $query = "INSERT INTO tasks (id_pengguna, task, deadline, status, deskripsi_tugas, prioritas) 
              VALUES ('$id_pengguna', '$task', '$deadline', 'pending', '$deskripsi_tugas', '$prioritas')";

    if ($conn->query($query)) {
        $task_id = $conn->insert_id;

        if (!empty($_POST['subtasks'])) {
            foreach ($_POST['subtasks'] as $index => $subtask) {
                $subtask = $conn->real_escape_string($subtask);
                $subtask_desc = isset($_POST['subtask_desc'][$index]) ? $conn->real_escape_string($_POST['subtask_desc'][$index]) : '';
                
                $conn->query("INSERT INTO subtasks (task_id, subtasks, status, deskripsi_subtugas) 
                              VALUES ('$task_id', '$subtask', 'pending', '$subtask_desc')");
            }
        }

        $_SESSION['message'] = "Tugas berhasil ditambahkan!";
        header("Location: daftar_tugas.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tugas</title>
    <link rel="stylesheet" href="todolist.css">
    <script>
        function tambahSubTugas() {
            let container = document.getElementById("subtask-container");
            let div = document.createElement("div");
            div.classList.add("subtask-entry");

            let inputSubtask = document.createElement("input");
            inputSubtask.type = "text";
            inputSubtask.name = "subtasks[]";
            inputSubtask.placeholder = "Judul Sub-Tugas";
            inputSubtask.required = true;

            let inputDesc = document.createElement("input");
            inputDesc.type = "text";
            inputDesc.name = "subtask_desc[]";
            inputDesc.placeholder = "Deskripsi Sub-Tugas";
            inputDesc.required = true;

            let buttonHapus = document.createElement("button");
            buttonHapus.type = "button";
            buttonHapus.innerText = "Hapus";
            buttonHapus.onclick = function() {
                container.removeChild(div);
            };

            div.appendChild(inputSubtask);
            div.appendChild(inputDesc);
            div.appendChild(buttonHapus);
            container.appendChild(div);
        }
    </script>
</head>
<body>
    <div class="container">
    <h1 style="text-align:center; margin-top: 60px;">Tambah Tugas</h1>
        <form method="POST">
        <h1 style="text-align:center;">Tambah Tugas</h1>
            <label>Judul Tugas</label>
            <input type="text" name="task" placeholder="Masukkan judul tugas" required>

            <label>Deadline</label>
            <input type="datetime-local" name="deadline" required>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    let inputDeadline = document.querySelector("input[name='deadline']");
                    let now = new Date();
                    now.setMinutes(now.getMinutes() - now.getTimezoneOffset()); 
                    inputDeadline.min = now.toISOString().slice(0, 16); 
                });
            </script>

            <label>Deskripsi Tugas</label>
            <input type="text" name="deskripsi_tugas" placeholder="Tambahkan deskripsi tugas" required>

            <label>Prioritas</label>
            <select name="prioritas">
                <option value="rendah">Rendah</option>
                <option value="sedang">Sedang</option>
                <option value="tinggi">Tinggi</option>
            </select>

            <h3>Sub-Tugas</h3>
            <div id="subtask-container">
                <div class="subtask-entry">
                    <label>Judul Sub-Tugas</label>
                    <input type="text" name="subtasks[]" placeholder="Masukkan judul sub-tugas" required>

                    <label>Deskripsi Sub-Tugas</label>
                    <input type="text" name="subtask_desc[]" placeholder="Tambahkan deskripsi sub-tugas" required>
                </div>
            </div>
            <button type="button" class="button" onclick="tambahSubTugas()">Tambah Sub-Tugas</button>
            <button type="submit" class="button">Tambah Tugas</button>
        </form>

        <a href="daftar_tugas.php" class="button">Lihat Daftar Tugas</a>
    </div>
</body>
</html>
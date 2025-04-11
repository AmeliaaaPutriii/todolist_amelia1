<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'db_todolist');

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "');</script>";
    unset($_SESSION['message']);
}

if (isset($_POST['delete_task'])) {
    $taskId = intval($_POST['delete_task']);
    $conn->query("DELETE FROM tasks WHERE id = $taskId");
    $conn->query("DELETE FROM subtasks WHERE task_id = $taskId");
    header("Location: daftar_tugas.php");
    exit();
}

if (isset($_POST['task_id'])) {
    $taskId = intval($_POST['task_id']);
    $conn->query("UPDATE tasks SET status = 'completed' WHERE id = $taskId");
    header("Location: daftar_tugas.php");
    exit();
}

if (isset($_POST['subtask_id'])) {
    $subtaskId = intval($_POST['subtask_id']);

    $conn->query("UPDATE subtasks SET status = 'completed' WHERE id = $subtaskId");

    $taskIdQuery = $conn->query("SELECT task_id FROM subtasks WHERE id = $subtaskId");
    $taskIdRow = $taskIdQuery->fetch_assoc();
    $taskId = $taskIdRow['task_id'];

    $pendingSubtasks = $conn->query("SELECT COUNT(*) as count FROM subtasks WHERE task_id = $taskId AND status = 'pending'");
    $pendingCount = $pendingSubtasks->fetch_assoc()['count'];

    if ($pendingCount == 0) {
        $conn->query("UPDATE tasks SET status = 'completed' WHERE id = $taskId");
    } else {
        $conn->query("UPDATE tasks SET status = 'pending' WHERE id = $taskId");
    }

    header("Location: daftar_tugas.php");
    exit();
}

if (isset($_POST['edit_task'])) {
    $taskId = intval($_POST['edit_task']);
    $task = $conn->real_escape_string($_POST['task']);
    $deadline = $_POST['deadline'];
    $deskripsi_tugas = $conn->real_escape_string($_POST['deskripsi_tugas']);
    $prioritas = $_POST['prioritas'];

    $conn->query("UPDATE tasks SET task='$task', deadline='$deadline', deskripsi_tugas='$deskripsi_tugas', prioritas='$prioritas' WHERE id=$taskId");
    header("Location: daftar_tugas.php");
    exit();
}

if (isset($_POST['edit_subtask'])) {
    $subtaskId = intval($_POST['edit_subtask']);
    $subtask = $conn->real_escape_string($_POST['subtask']);
    $deskripsi_subtugas = $conn->real_escape_string($_POST['deskripsi_subtugas']);

    $conn->query("UPDATE subtasks SET subtasks='$subtask', deskripsi_subtugas='$deskripsi_subtugas' WHERE id=$subtaskId");
    header("Location: daftar_tugas.php");
    exit();
}

if (isset($_POST['delete_subtask'])) {
    $subtaskId = intval($_POST['delete_subtask']);
    $conn->query("DELETE FROM subtasks WHERE id = $subtaskId");
    header("Location: daftar_tugas.php");
    exit();
}

$pengguna = $_SESSION['id_pengguna'];
$tasks = $conn->query("SELECT * FROM tasks WHERE id_pengguna = '$pengguna' ORDER BY id DESC");


$tasks = $conn->query("SELECT *, 
                        CASE 
                            WHEN status = 'completed' THEN 'completed'
                            WHEN deadline <= DATE_ADD(NOW(), INTERVAL 3 DAY) THEN 'urgent' 
                            ELSE 'normal' 
                        END AS urgency 
                        FROM tasks 
                        ORDER BY FIELD(urgency, 'urgent', 'normal', 'completed'), deadline ASC, id DESC");

$statusFilter = isset($_POST['status_filter']) ? $_POST['status_filter'] : 'all';
$prioritasFilter = isset($_POST['prioritas_filter']) ? $_POST['prioritas_filter'] : 'all';


if ($statusFilter === 'completed') {
    $tasks = $conn->query("SELECT *, 
                            CASE 
                                WHEN status = 'completed' THEN 'completed'
                                WHEN deadline <= DATE_ADD(NOW(), INTERVAL 3 DAY) THEN 'urgent' 
                                ELSE 'normal' 
                            END AS urgency 
                            FROM tasks 
                            WHERE id_pengguna = '$pengguna' AND status = 'completed'
                            ORDER BY FIELD(urgency, 'urgent', 'normal', 'completed'), deadline ASC, id DESC");
} else {
    $tasks = $conn->query("SELECT *, 
                            CASE 
                                WHEN status = 'completed' THEN 'completed'
                                WHEN deadline <= DATE_ADD(NOW(), INTERVAL 3 DAY) THEN 'urgent' 
                                ELSE 'normal' 
                            END AS urgency 
                            FROM tasks
                            WHERE id_pengguna = '$pengguna'
                            ORDER BY FIELD(urgency, 'urgent', 'normal', 'completed'), deadline ASC, id DESC");
}

$filterQuery = "SELECT *, 
                CASE 
                    WHEN status = 'completed' THEN 'completed'
                    WHEN deadline <= DATE_ADD(NOW(), INTERVAL 3 DAY) THEN 'urgent' 
                    ELSE 'normal' 
                END AS urgency 
                FROM tasks 
                WHERE id_pengguna = '$pengguna'";

if ($statusFilter === 'completed') {
    $filterQuery .= " AND status = 'completed'";
} elseif ($statusFilter === 'pending') {
    $filterQuery .= " AND status = 'pending'";
}


if ($prioritasFilter !== 'all') {
    $filterQuery .= " AND prioritas = '$prioritasFilter'";
}

$filterQuery .= " ORDER BY FIELD(urgency, 'urgent', 'normal', 'completed'), deadline ASC, id DESC";

$tasks = $conn->query($filterQuery);



$notifikasi = $conn->query("
    SELECT id, task, deadline 
    FROM tasks 
    WHERE status = 'pending' 
    AND deadline <= DATE_ADD(NOW(), INTERVAL 1 DAY) 
    AND notifikasi = 0 
    AND id_pengguna = '$pengguna'
");

while ($notif = $notifikasi->fetch_assoc()) {
    echo "<script>alert('Notifikasi: Task " . $notif['id'] . " (" . htmlspecialchars($notif['task']) . ") akan deadline');</script>";
    
    $conn->query("UPDATE tasks SET notifikasi = 1 WHERE id = " . $notif['id']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tugas</title>
    <link rel="stylesheet" href="daftar_tugas.css">
    <script>
        function toggleEdit(taskId) {
            let displayMode = document.getElementById("task-view-" + taskId);
            let editMode = document.getElementById("task-edit-" + taskId);

            if (displayMode.style.display === "none") {
                displayMode.style.display = "block";
                editMode.style.display = "none";
            } else {
                displayMode.style.display = "none";
                editMode.style.display = "block";
            }
        }

        function toggleEditSubtask(subtaskId) {
        let editMode = document.getElementById("subtask-edit-" + subtaskId);
        if (editMode.style.display === "none") {
            editMode.style.display = "block";
        } else {
            editMode.style.display = "none";
            }
        }

        function logout() {
        if (confirm("Apakah Anda yakin ingin logout?")) {
            window.location.href = 'login.php'; 
        }
    }

        function addTask() {
        window.location.href = 'todolist.php';
    }

    function confirmDelete() {
    return confirm("Apakah Anda yakin ingin menghapus tugas ini?");
    }

    </script>
</head>
<body>
    <div class="container">
        <h1>Daftar Tugas</h1>
        <button onclick="addTask()" class="button">Tambah Tugas</button>
        <button onclick="logout()" class="button">Logout</button>

        <form method="POST" style="margin: 20px 0; display: flex; gap: 10px; align-items: center;">
        <select name="status_filter" onchange="this.form.submit()">
    <option value="all" <?php echo ($statusFilter === 'all') ? 'selected' : ''; ?>>Semua Tugas</option>
    <option value="pending" <?php echo ($statusFilter === 'pending') ? 'selected' : ''; ?>>Tugas Belum Selesai</option>
    <option value="completed" <?php echo ($statusFilter === 'completed') ? 'selected' : ''; ?>>Tugas Selesai</option>
</select>


    <select name="prioritas_filter" onchange="this.form.submit()">
        <option value="all" <?php echo (!isset($_POST['prioritas_filter']) || $_POST['prioritas_filter'] === 'all') ? 'selected' : ''; ?>>Semua Prioritas</option>
        <option value="tinggi" <?php echo ($_POST['prioritas_filter'] === 'tinggi') ? 'selected' : ''; ?>>Tinggi</option>
        <option value="sedang" <?php echo ($_POST['prioritas_filter'] === 'sedang') ? 'selected' : ''; ?>>Sedang</option>
        <option value="rendah" <?php echo ($_POST['prioritas_filter'] === 'rendah') ? 'selected' : ''; ?>>Rendah</option>
    </select>
</form>


        <ul class="task-list">
            <?php while ($task = $tasks->fetch_assoc()): 
                $isOverdue = ($task['status'] === 'pending' && strtotime($task['deadline']) < time());
 ?>
                <li class="task-item <?php echo ($task['status'] === 'completed') ? 'completed' : ''; ?> <?php echo ($isOverdue) ? 'overdue' : ''; ?>">
            <div id="task-view-<?php echo $task['id']; ?>">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                        </form>
                        <strong><?php echo htmlspecialchars($task['task']); ?></strong>
                        <p><strong>Deskripsi:</strong> <?php echo htmlspecialchars($task['deskripsi_tugas']); ?></p>
                        <p><strong>Deadline:</strong> <?php echo htmlspecialchars($task['deadline']); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($task['status']); ?></p>
                        <p><strong>Prioritas:</strong> <?php echo htmlspecialchars($task['prioritas']); ?></p>
                        <p><strong>Dibuat Pada:</strong> <?php echo htmlspecialchars($task['dibuat_pada']); ?></p>
                        <?php if ($isOverdue): ?>

                <p class="overdue-message" style="color: red; font-weight: bold;">
                    Perhatian: Tugas ini sudah melewati deadline!
                </p>
            <?php endif; ?>

                        <?php if ($task['status'] !== 'completed'): ?>
                        <button type="button" onclick="toggleEdit(<?php echo $task['id']; ?>)">Edit</button>
                        <?php endif; ?>
                        <form method="POST" style="display:inline;" onsubmit="return confirmDelete();">
    <input type="hidden" name="delete_task" value="<?php echo $task['id']; ?>">
    <button type="submit">Hapus</button>
</form>
                    </div>

                    <div id="task-edit-<?php echo $task['id']; ?>" style="display: none;">
                        <form method="POST">
                            <input type="hidden" name="edit_task" value="<?php echo $task['id']; ?>">
                            <input type="text" name="task" value="<?php echo htmlspecialchars($task['task']); ?>" required>
                            <input type="datetime-local" name="deadline" value="<?php echo htmlspecialchars($task['deadline']); ?>" required>
                            <script>
            document.addEventListener("DOMContentLoaded", function () {
            let inputDeadline = document.querySelector("input[name='deadline']");
             let now = new Date();
             now.setMinutes(now.getMinutes() - now.getTimezoneOffset()); 
            inputDeadline.min = now.toISOString().slice(0, 16); 
             });
            </script>
                            <input type="text" name="deskripsi_tugas" value="<?php echo htmlspecialchars($task['deskripsi_tugas']); ?>" required>
                            <select name="prioritas">
                                <option value="rendah" <?php echo ($task['prioritas'] === 'low') ? 'selected' : ''; ?>>Low</option>
                                <option value="sedang" <?php echo ($task['prioritas'] === 'medium') ? 'selected' : ''; ?>>Medium</option>
                                <option value="tinggi" <?php echo ($task['prioritas'] === 'high') ? 'selected' : ''; ?>>High</option>
                            </select>
                            <button type="submit">Simpan</button>
                            <button type="button" onclick="toggleEdit(<?php echo $task['id']; ?>)">Batal</button>
                        </form>
                    </div>

                    <?php 
                    $taskId = $task['id'];
                    $subtasks = $conn->query("SELECT * FROM subtasks WHERE task_id = $taskId");
                    if ($subtasks && $subtasks->num_rows > 0): ?>
                        <ul class="subtask-list">
    <?php while ($subtask = $subtasks->fetch_assoc()): ?>
        <li class="subtask-item <?php echo ($subtask['status'] === 'completed') ? 'completed' : ''; ?>">
            <form method="POST" style="display: inline;">
                <input type="hidden" name="subtask_id" value="<?php echo $subtask['id']; ?>">
                <input type="checkbox" onchange="this.form.submit()" 
                    <?php echo ($subtask['status'] === 'completed') ? 'checked disabled' : ''; ?>>
            </form>
            <span><?php echo htmlspecialchars($subtask['subtasks']); ?> - <?php echo htmlspecialchars($subtask['deskripsi_subtugas']); ?></span>

            <?php if ($subtask['status'] !== 'completed'): ?>
            <button type="button" onclick="toggleEditSubtask(<?php echo $subtask['id']; ?>)">Edit</button>
            <?php endif; ?>

            <form method="POST" style="display:inline;" onsubmit="return confirmDelete();">
    <input type="hidden" name="delete_subtask" value="<?php echo $subtask['id']; ?>">
    <button type="submit">Hapus</button>
</form>

            <div id="subtask-edit-<?php echo $subtask['id']; ?>" style="display: none;">
                <form method="POST">
                    <input type="hidden" name="edit_subtask" value="<?php echo $subtask['id']; ?>">
                    <input type="text" name="subtask" value="<?php echo htmlspecialchars($subtask['subtasks']); ?>" required>
                    <input type="text" name="deskripsi_subtugas" value="<?php echo htmlspecialchars($subtask['deskripsi_subtugas']); ?>" required>
                    <button type="submit">Simpan</button>
                    <button type="button" onclick="toggleEditSubtask(<?php echo $subtask['id']; ?>)">Batal</button>
                </form>
            </div>
        </li>
    <?php endwhile; ?>
</ul>

                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>
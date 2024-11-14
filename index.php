<?php
    $errors = "";

    $db = mysqli_connect('localhost', 'root', '', 'todo_list');

    // Tambah task
    if (isset($_POST['submit'])) {
        $task = $_POST['task'];
        if (empty($task)) {
            $errors = "Wajib diisi!";
        } else {
            mysqli_query($db, "INSERT INTO tasks (task) VALUES ('$task')");
            header('location: index.php');
        }
    }

    // Hapus task
    if (isset($_GET['del_task'])) {
        $id = $_GET['del_task'];
        mysqli_query($db, "DELETE FROM tasks WHERE id=$id");
        header('location: index.php');
    }

    // Tandai task sebagai selesai
    if (isset($_GET['mark_done'])) {
        $id = $_GET['mark_done'];
        mysqli_query($db, "UPDATE tasks SET status = 1 WHERE id=$id");
        header('Location: index.php');
    }

    // Edit task
    if (isset($_POST['edit_task'])) {
        $id = $_POST['task_id'];
        $task = $_POST['new_task'];
        $task = mysqli_real_escape_string($db, $task);
        mysqli_query($db, "UPDATE tasks SET task='$task' WHERE id=$id");
        header('location: index.php');
    }

    $tasks = mysqli_query($db, "SELECT * FROM tasks");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Do List</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <script>
        function enableEdit(id, task) {
            const taskCell = document.getElementById('task-' + id);
            taskCell.innerHTML = `<form method='POST' action='index.php' style='display:inline;'>
                                    <input type='hidden' name='task_id' value='${id}'>
                                    <input type='text' name='new_task' value='${task.replace(/'/g, "&apos;")}'>
                                    <button type='submit' name='edit_task'>Save</button>
                                </form>`;
        }
    </script>
</head>
<body style="background-image: url('totoro.jpg');">

    <div class="heading">
        <h1>To Do List</h1>
    </div>

    <!-- Form untuk tambah task -->
    <form method="POST" action="index.php">
        <?php if (isset($errors) && !empty($errors)) { ?>
            <p><?php echo $errors; ?></p>
        <?php } ?>

        <input type="text" name="task" class="task_input" placeholder="Add a new task...">
        <button type="submit" class="add_button" name="submit">+</button>
    </form>

    <!-- Tabel daftar task -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Task</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $count = 1;
        while ($row = mysqli_fetch_array($tasks)) { ?>
            <tr>
                <td><?php echo $count; ?></td>
                <td id="task-<?php echo $row['id']; ?>" class="task" style="text-decoration: <?php echo ($row['status'] == 1) ? 'line-through' : 'none'; ?>;">
                    <?php echo htmlspecialchars($row['task']); ?>
                </td>  
                <td>
                    <?php if ($row['status'] == 0) { ?>
                        <a href="index.php?mark_done=<?php echo $row['id']; ?>">Done</a>
                    <?php } ?>
                    <a href="javascript:void(0);" onclick="enableEdit(<?php echo $row['id']; ?>, '<?php echo addslashes($row['task']); ?>')">Edit</a>
                    <a href="index.php?del_task=<?php echo $row['id']; ?>">Delete</a>
                </td>
            </tr>
        <?php 
            $count++;
        } ?>
        </tbody>
    </table>
</body>
</html>

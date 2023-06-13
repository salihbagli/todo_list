<?php 
$conn = mysqli_connect("localhost", "root", "", "todolist");
if (!$conn) {
    die("Bağlantı hatası: " . mysqli_connect_error());
}
$todosClass = new ToDos($conn);
$todos = $todosClass->listToDos();
if (isset($_POST['todo']) && !empty($_POST['todo'])) {
    $todo = $_POST['todo'];
    $result = $todosClass->addNewToDos($todo);
    if ($result) {
        header('location: index.php');
    }
}
$sql = "SELECT id, yapilacak_adi, durum FROM yapilacaklar ORDER BY durum ASC, id DESC";

if (isset($_GET['complete']) && !empty($_GET['complete'])) {
    $id = $_GET['complete'];
    $result = $todosClass->completeToDos($id);
    if ($result) {
       header('location: index.php');
    }
}
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = $_GET['delete'];
    $result = $todosClass->deleteToDos($id);
    if ($result) {
        header('location: index.php');
    }
}
class ToDos {
    private $conn;
    function __construct($conn)
    {
        $this->conn = $conn;
        $sql = "CREATE TABLE IF NOT EXISTS yapilacaklar (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        yapilacak_adi VARCHAR(255) NOT NULL,
        durum BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        mysqli_query($this->conn, $sql);
    }

    function listToDos() {
        $sql = "SELECT id, yapilacak_adi, durum FROM yapilacaklar ORDER BY durum ASC, id DESC";
        $results = mysqli_query($this->conn, $sql);
        $todos = mysqli_fetch_all($results, MYSQLI_ASSOC);
        return $todos;
    }
    function addNewToDos($todo) {
        $sql = "INSERT INTO yapilacaklar (yapilacak_adi) VALUES ('$todo')";
        mysqli_query($this->conn, $sql);
        return true;
    }
    function completeToDos($id) {
        $sql = "UPDATE yapilacaklar SET durum=1 WHERE id=$id";
        mysqli_query($this->conn, $sql);
        return true;
    }
    function deleteToDos($id) {
        $sql = "DELETE FROM yapilacaklar WHERE id=$id";
        mysqli_query($this->conn, $sql);
        return true;
    }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>ToDoList App</title>
    <link href="styles.css" rel="stylesheet">
  </head>
  <body>
    <div class="todo-container">
        <div class="header">
            <h1>ToDoList App</h1>
            <form method="post" action="">
                <input type="text" name="todo" placeholder="Yapacaklarınızı girin...">
            </form>
        </div>
        <ul class="list-todo">
            <?php foreach ($todos as $todo) {?>
                <li class="<?php echo ($todo['durum'] == 1 ? 'completed' : '')?>">
                    <div class="todo-content"><?php echo $todo['yapilacak_adi']?></div>
                    <?php if ($todo['durum'] == 0) {?>
                    <div class="todo-action">
                        <a href="index.php?complete=<?php echo $todo['id']?>" title="Tamamlandı!">Tamamlandı!</a>
                        <a href="index.php?delete=<?php echo $todo['id']?>" class="btn-delete-todo" title="Sil">&#215;</a>
                    </div>
                    <?php }?>
                </li>
            <?php }?>
        </ul>
      </div>
  </body>
</html>
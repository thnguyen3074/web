<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tìm Kiếm Sản Phẩm</title>
</head>
<body>
    <h2>Tìm Kiếm Sản Phẩm</h2>
    
    <form method="POST">
        Tên sản phẩm: <input type="text" name="search">
        <input type="submit" name="submit" value="Tìm kiếm">
    </form>
    
    <hr>

    <?php
    if (isset($_POST['submit'])) {
        $search = $_POST['search'];
        
        // Kết nối database
        $conn = new mysqli('localhost', 'root', '', 'test');
        $conn->set_charset("utf8");
        
        // Tìm kiếm
        $sql = "SELECT * FROM webtm_sanpham WHERE TenSP LIKE '%$search%'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Tên SP</th><th>Giá</th></tr>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['idSP'] . "</td>";
                echo "<td>" . $row['TenSP'] . "</td>";
                echo "<td>" . $row['Gia'] . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "Không tìm thấy!";
        }
        
        $conn->close();
    }
    ?>
</body>
</html>
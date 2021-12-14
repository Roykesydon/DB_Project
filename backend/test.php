<html>

<head>
    <title>HelloWorld</title>
</head>

<body>
    <?php
    $user = 'dbuser'; 
    $password = 'dbpasswd'; 
    try {
        $db = new
            PDO('mysql:host=172.21.0.2;dbname=HW3;charset=utf8', $user, $password);
            // 因為我不是用XAMPP，是 docker 的多個 conatiner，才改 172.21.0.2
        
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        
        $bottom_ID = 1;
        $top_ID = 5;
        //想用 prepared statement 才刻意多這個 
        $query = ("select salary from works where ID >= ? and ID <= ?");
        $stmt = $db->prepare($query);
        $error = $stmt->execute(array($bottom_ID, $top_ID));
        $result = $stmt->fetchAll();

        $sum = 0;
        for($i=0; $i<count($result); $i++){
            $sum += $result [$i]['salary'];
        }
        echo "sum: ".$sum;

    } catch (PDOException $e) { 
        print "ERROR!: " . $e->getMessage();
        die();
    }

    ?>
</body>

</html>
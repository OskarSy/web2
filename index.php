<?php
require 'config.php';
$eid = "";
$eod = "";



if (isset($_GET["en"])) {
  $cid = $_GET["cid"];
  $loggedOutTime = date('m/d/Y h:i:s a', time());
  $sql = "UPDATE user_data
SET time_when_logged_out = '$loggedOutTime'
WHERE User_id = '$cid'
ORDER BY id DESC
LIMIT 1";
  mysqli_query($con, $sql);
  header("Location: https://site221.webte.fei.stuba.sk/index.php");
}


if (isset($_SESSION["email"]) && $_SESSION['id']) {
  $current = $_SESSION['id'];
  $user = $_SESSION['email'];

}



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sent'])) {
  $str1 = $_POST["name"];
  $str2 = $_POST["surname"];
  $str3 = $_POST["placing"];
  $str4 = $_POST["disciple"];
  $str6 = $_GET["eid"];
  do {
    if (
      empty($str1) || empty($str2) || empty($str3) || empty($str4)
    ) {


      $ermessage = "Empty variable";
      break;
    }
    $sql = "SELECT name,surname FROM person
    where not id='$str6'";

    $res = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
      if (($str1 == $row['name']) || ($str2 == $row['surname'])) {
        $ermessage = "Athlete already exists";
        $flag = 1;
        break;
      }
    }
    if ($flag == 1) {
      break;
    }


    $str7 = $_GET["yr"];
    $sql = "UPDATE placement pl
  JOIN person p ON pl.person_id=p.id
    JOIN game g ON pl.game_id=g.id 
    SET pl.disciple = '$str4'
    WHERE pl.person_id = '$str6' AND g.year='$str7'";

    $res = mysqli_query($con, "SELECT pl.disciple From placement pl
      JOIN person p ON pl.person_id=p.id
        JOIN game g ON pl.game_id=g.id 
        WHERE pl.person_id = '$str6' AND g.year='$str7'");
    while ($row = mysqli_fetch_assoc($res)) {
      $oDisciple = $row["disciple"];
    }

    mysqli_query($con, $sql);
    $sql = "UPDATE placement pl
    JOIN person p ON pl.person_id=p.id
      JOIN game g ON pl.game_id=g.id 
      SET pl.placing = '$str3'
      WHERE pl.person_id = '$str6' AND g.year='$str7'";

    $res = mysqli_query($con, "SELECT pl.placing From placement pl
                  JOIN person p ON pl.person_id=p.id
                    JOIN game g ON pl.game_id=g.id 
                    WHERE pl.person_id = '$str6' AND g.year='$str7'");
    while ($row = mysqli_fetch_assoc($res)) {
      $oPlacing = $row["placing"];
    }

    mysqli_query($con, $sql);
    $sql = "UPDATE placement pl
      JOIN person p ON pl.person_id=p.id
        JOIN game g ON pl.game_id=g.id 
        SET p.surname = '$str2'
        WHERE pl.person_id = '$str6' AND g.year='$str7'";

    $res = mysqli_query($con, "SELECT surname FROM person WHERE id='$str6'");
    while ($row = mysqli_fetch_assoc($res)) {
      $oSname = $row["surname"];
    }

    mysqli_query($con, $sql);
    $sql = "UPDATE placement pl
        JOIN person p ON pl.person_id=p.id
          JOIN game g ON pl.game_id=g.id 
          SET p.name = '$str1'

          WHERE pl.person_id = '$str6' AND g.year='$str7'";
    $res = mysqli_query($con, "SELECT name FROM person WHERE id='$str6'");
    while ($row = mysqli_fetch_assoc($res)) {
      $oName = $row["name"];
    }

    mysqli_query($con, $sql);
    $res = mysqli_query($con, "SELECT * From user_data WHERE User_id = '$current'");
    while ($row = mysqli_fetch_assoc($res)) {
      $note = $row["action"];
    }
    $fullnote = "$note Modified person $oName, $oSname, $oPlacing, $oDisciple to $str1, $str2, $str3, $str4, ";

    $sql = "UPDATE user_data
          SET action = '$fullnote'
          WHERE User_id = '$current'
          ORDER BY id DESC
        LIMIT 1";
    mysqli_query($con, $sql);
    echo "<script>
alert('Modified succesfully');
window.location.href='../index.php';
</script>";

  






  } while (false);



}






if (!empty($_SESSION["id"]) && isset($_GET["eid"]) && (isset($_GET["eod"]))) {
  $eid = $_GET["eid"];
  $eod = $_GET["eod"];
  if ($eod == "d") {
    $dp = $_GET['dp'];
    $yr = $_GET['yr'];
    $sel = "SELECT id FROM game where year='$yr'";
    $r = mysqli_query($con, $sel);
    while ($row = mysqli_fetch_assoc($r)) {
      $gid = $row['id'];
    }
    $res = mysqli_query($con, "SELECT concat(name, ' ', surname) as meno FROM person WHERE id='$eid'");
    while ($row = mysqli_fetch_assoc($res)) {
      $who = $row["meno"];
    }
    $selDel = "DELETE FROM placement WHERE person_id='$eid' AND disciple='$dp' and game_id='$gid'";
    mysqli_query($con, $selDel);
    $last_record = mysqli_insert_id($con);
    $res = mysqli_query($con, "SELECT * From user_data WHERE User_id = '$current'");
    while ($row = mysqli_fetch_assoc($res)) {
      $note = $row["action"];
    }
    $fullnote = "$note Deleted record on $dp from person $who,";

    $sql = "UPDATE user_data
  SET action = '$fullnote'
  WHERE User_id = '$current'
  ORDER BY id DESC
LIMIT 1";
    mysqli_query($con, $sql);
    echo "<script>
    alert('Deleted record');
    </script>";
  } else if ($eod == "e") {
    $yr = $_GET['yr'];
    $dp = $_GET['dp'];

    $sql = "SELECT * FROM placement pl
  JOIN person p ON pl.person_id=p.id
  JOIN game g ON pl.game_id=g.id 
  WHERE pl.disciple ='$dp' AND g.year='$yr' AND pl.person_id='$eid'  ";
    $sqlDis = "SELECT disciple FROM placement group by disciple";
    $sqlYr = "SELECT year FROM game group by year order by year ASC ";
    $sqlTy = "SELECT type FROM game group by type";
    $sqlPla = "SELECT placing FROM placement group by placing";

    $result = mysqli_query($con, $sql);
    $resultDis = mysqli_query($con, $sqlDis);
    $resultYr = mysqli_query($con, $sqlYr);
    $resultTy = mysqli_query($con, $sqlTy);
    $resultPla = mysqli_query($con, $sqlPla);

  }
  if ($eod == "dp") {
    $selDel = "DELETE FROM placement WHERE person_id='$eid'";
    mysqli_query($con, $selDel);
    $res = mysqli_query($con, "SELECT concat(name, ' ', surname) as meno FROM person WHERE id='$eid'");
    while ($row = mysqli_fetch_assoc($res)) {
      $who = $row["meno"];
    }
    $selDel = "DELETE FROM person WHERE id='$eid'";
    mysqli_query($con, $selDel);
    $last_record = mysqli_insert_id($con);
    $res = mysqli_query($con, "SELECT * From user_data WHERE User_id = '$current'");
    while ($row = mysqli_fetch_assoc($res)) {
      $note = $row["action"];
    }
    $fullnote = "$note Deleted person $who,";

    $sql = "UPDATE user_data
  SET action = '$fullnote'
  WHERE User_id = '$current'
  ORDER BY id DESC
LIMIT 1";
    mysqli_query($con, $sql);
    echo "<script>
    alert('Deleted person');
    </script>";
  }


}

if ($eod != "e") {
  $limit = 10;
  $page = isset($_GET['page']) ? $_GET['page'] : 1;
  $start = ($page - 1) * $limit;
  $action = 'ASC';
  $id = 'id';
  if (isset($_GET["type"])) {
    $id = $_GET["type"];
    $action = $_GET["action"];

    if (!isset($_GET["paging"])) {
      if ($action == 'ASC') {
        $action = 'DESC';
      } else {
        $action = 'ASC';
      }
      switch ($_GET['type']) {
        case 'surname':
          $id = "surname";
          break;
        case 'year':
          $id = "year";
          break;
        case 'type':
          $id = "type";
          break;
        default:
          break;
      }
    }
    $sql = "SELECT p.id, concat(p.name, ' ', p.surname) as meno,g.type,g.year,g.city,pl.disciple FROM placement pl
   JOIN person p ON pl.person_id=p.id
   JOIN game g ON pl.game_id=g.id 
   ORDER BY  $id $action
   Limit $start,$limit "
    ;


  } else {
    $sql = "SELECT p.id, concat(p.name, ' ', p.surname) as meno,g.type,g.year,g.city,pl.disciple FROM placement pl
JOIN person p ON pl.person_id=p.id
JOIN game g ON pl.game_id=g.id 
Limit $start,$limit ";
  }

  $sqlcounter = "SELECT count(pl.id) as id FROM placement pl
JOIN person p ON pl.person_id=p.id
JOIN game g ON pl.game_id=g.id";
  $result1 = mysqli_query($con, $sqlcounter);

  $count = $result1->fetch_all(MYSQLI_ASSOC);
  $total = $count[0]['id'];
  $pages = ceil($total / $limit);

  $Previous = $page - 1;
  $Next = $page + 1;

  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $str = $_POST["search"];
    $sql = "SELECT p.id, concat(p.name, ' ', p.surname) as meno,g.type,g.year,g.city,pl.disciple FROM placement pl
  JOIN person p ON pl.person_id=p.id
  JOIN game g ON pl.game_id=g.id 
  AND( concat(p.name, ' ', p.surname) like CONCAT('%','$str','%')
      or g.city like CONCAT('%','$str','%')
      or pl.disciple like CONCAT('%','$str','%') 
      or g.type like CONCAT('%','$str','%')
      or p.id like CONCAT('%','$str','%') 
      or g.year like CONCAT('%','$str','%')) ";
  }
  


    }
    
    if($_GET['page']>$pages && !isset($_GET['eod'])){
      header("Location: https://site221.webte.fei.stuba.sk/index.php?page=$pages&paging=");
      }
      if($_GET['page']<1 && !isset($_GET['eod'])){
          header("Location: https://site221.webte.fei.stuba.sk/index.php?page=1&paging=");
        }

        $result = mysqli_query($con, $sql);



?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="bootstrap/bootstrap.min.css">
  <script src="bootstrap/bootstrap.min.js"></script>


  <title>Zadanie1</title>
</head>

<body>

  <header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample08"
        aria-controls="navbarsExample08" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <a class="navbar-brand disabled" href="#">Navbar</a>
      <div class="collapse navbar-collapse justify-content-md-center" id="navbarsExample08">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="index.php">Slovakia
              champions</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="php/tops.php">TOP 10</a>
          </li>
          <?php if (!empty($_SESSION["id"])) { ?>
            <li class="nav-item">
              <a class="nav-link" href="php/history.php">History</a>
            </li>

          <?php } ?>
        </ul>
      </div>
    </nav>
  </header>
  <?php if (empty($_SESSION["id"])) { ?>
    <button class="btn btn-primary float-right" id="btn">Register</button>
    <script>
      var btn = document.getElementById('btn');
      btn.addEventListener('click', function () {
        document.location.href = '<?php echo "https://site221.webte.fei.stuba.sk/php/register.php"; ?>';
      });
    </script>
    <button class="btn btn-primary float-right" id="btn2">Login</button>
    <script>
      var btn = document.getElementById('btn2');
      btn.addEventListener('click', function () {
        document.location.href = '<?php echo "https://site221.webte.fei.stuba.sk/php/login.php"; ?>';
      });
    </script>
  <?php } ?>
  <?php if (!empty($_SESSION["id"])) { ?>
    <button class="btn btn-primary float-right" id="btn3">Logout</button>
    <script>
      var btn = document.getElementById('btn3');
      btn.addEventListener('click', function () {
        document.location.href = '<?php echo "https://site221.webte.fei.stuba.sk/php/logout.php"; ?>';
      });
    </script>
    <span>Logged in as
      <?php echo $user ?>
    </span>
  <?php } ?>

  <div class="container">
    <div class="row mt-5">
      <div class="col">
        <div class="card-mt-5">
          <div class="card-header">
            <div class="table-responsive">
              <h1 class="display-6 text-centre">
                OH hry
              </h1>
              <?php if (!empty($_SESSION["id"]) && $eod != "e") { ?>
                <a class="btn btn-dark float-right"
                  href="<?php echo "https://site221.webte.fei.stuba.sk/php/add.php"; ?>">Add
                  athlete</a>
              <?php } ?>
              <?php if ($eod != "e") { ?>
                <form method="post">
                  <label>Search</label>
                  <input type="text" name="search">
                  <input class="btn btn-dark" type="submit" name="submit">
                  <button class="btn btn-dark" onClick="window.location.href='index.php'">reset</button>
                </form>


                <div class="card-body">

                  <table class="table table-bordered text-centre ">
                    <tr>
                      <td><a
                          href="index.php?type=<?php echo 'id'; ?>&action=<?php echo $action; ?>&page=<?php echo $page; ?>">ID</a>
                      </td>
                      <td><a
                          href="index.php?type=<?php echo 'surname'; ?>&action=<?php echo $action; ?>&page=<?php echo $page; ?>">Full
                          name</a></td>
                      <td><a
                          href="index.php?type=<?php echo 'year'; ?>&action=<?php echo $action; ?>&page=<?php echo $page; ?>">Year</a>
                      </td>
                      <td><a
                          href="index.php?type=<?php echo 'city'; ?>&action=<?php echo $action; ?>&page=<?php echo $page; ?>">City</a>
                      </td>
                      <td><a
                          href="index.php?type=<?php echo 'type'; ?>&action=<?php echo $action; ?>&page=<?php echo $page; ?>">Type</a>
                      </td>
                      <td><a
                          href="index.php?type=<?php echo 'disciple'; ?>&action=<?php echo $action; ?>&page=<?php echo $page; ?>">Disciple</a>
                      </td>
                      <?php if (!empty($_SESSION["id"])) { ?>
                        <td>Edit</td>
                        <td>Delete Record</td>
                        <td>Delete Person</td>
                      <?php } ?>
                    </tr>

                    <?php

                    while ($row = mysqli_fetch_assoc($result)) {
                      ?>
                      <tr>
                        <td>
                          <?php echo $row["id"] ?>
                        </td>
                        <td>
                          <a href="php/athlete.php?fullname=<?php echo $row["meno"]; ?>"><?= $row["meno"]; ?></a>
                        </td>
                        <td>
                          <?php echo $row["year"] ?>
                        </td>
                        <td>
                          <?php echo $row["city"] ?>
                        </td>
                        <td>
                          <?php echo $row["type"] ?>
                        </td>
                        <td>
                          <?php echo $row["disciple"] ?>
                        </td>
                        <?php if (!empty($_SESSION["id"])) { ?>

                          <td><a
                              href="index.php?eid=<?php echo $row['id']; ?>&yr=<?php echo $row['year']; ?>&dp=<?php echo $row['disciple']; ?>&page=<?php echo $page; ?>&eod=<?php echo "e"; ?>"
                              class="btn btn-dark">Edit</a></td>
                          <td><a
                              href="index.php?eid=<?php echo $row['id']; ?>&yr=<?php echo $row['year']; ?>&page=<?php echo $page; ?>&dp=<?php echo $row['disciple']; ?>&eod=<?php echo "d"; ?>"
                              class="btn btn-danger">Delete Record</a></td>
                          <td><a
                              href="index.php?eid=<?php echo $row['id']; ?>&yr=<?php echo $row['year']; ?>&page=<?php echo $page; ?>&dp=<?php echo $row['disciple']; ?>&eod=<?php echo "dp"; ?>"
                              class="btn btn-danger">Delete Person</a></td>
                        <?php } ?>
                      </tr>
                      <?php
                    }
                    ?>

                  </table>
                </div>

                <?php if(!isset($_POST['submit'])){?>
                <div class="center">
                  <nav aria-label="Page navigation">
                    <div class="pagination">
                      <?php if ($Previous > 1) { ?>
                        <a href="index.php?type=<?php echo $id; ?>&action=<?php echo $action; ?>&page=<?php echo $Previous; ?>&paging=<?php ?>"
                          aria-label="Previous">
                          <span aria-hidden="true">&laquo; Previous</span>
                        </a>
                      <?php } ?>
                      <?php if ($Previous == 1) { ?>
                        <a href="index.php?type=<?php echo $id; ?>&action=<?php echo $action; ?>&page=<?php echo 1; ?>&paging=<?php ?>"
                          aria-label="Previous">
                          <span aria-hidden="true">&laquo; Previous</span>
                        </a>
                      <?php } ?>
                      <?php $setActive = $_GET['page'];
                      for ($i = 1; $i <= $pages; $i++): ?>
                        <?php if ($setActive == $i) { ?>
                          <a class="active"
                            href="index.php?type=<?php echo $id; ?>&action=<?php echo $action; ?>&page=<?php echo $i; ?>&paging=<?php ?>"><?=
                                        $i; ?></a>

                        <?php } else { ?>
                          <a
                            href="index.php?type=<?php echo $id; ?>&action=<?php echo $action; ?>&page=<?php echo $i; ?>&paging=<?php ?>"><?=
                                        $i; ?></a>
                        <?php } ?>
                      <?php endfor; ?>

                      <?php if ($Next < $pages) { ?>
                        <a href="index.php?type=<?php echo $id; ?>&action=<?php echo $action; ?>&page=<?php echo $Next; ?>&paging=<?php ?>"
                          aria-label="Next">
                          <span aria-hidden="true">Next &raquo;</span>
                        </a>
                      <?php } ?>
                      <?php if ($Next == $pages) { ?>
                        <a href="index.php?type=<?php echo $id; ?>&action=<?php echo $action; ?>&page=<?php echo $pages; ?>&paging=<?php ?>"
                          aria-label="Next">
                          <span aria-hidden="true">Next &raquo;</span>
                        </a>
                      <?php } ?>
                    </div>
                  </nav>

                </div>
                <?php
              }
              ?>

              </div>
              <?php
              }
              ?>

            <?php if (!empty($_SESSION["id"]) && $eod == "e") { ?>
              <?php if (!empty($ermessage)) {
                echo "
<div class='alert alert-warning alert-dismissible fade show' role='alert'>
<strong>$ermessage</strong>
</div>
";
              } ?>
              <div class="card-body">

                <table class="table table-bordered text-centre">
                  <tr>
                    <td>ID
                    </td>
                    <td>Name
                    </td>
                    <td>Surname
                    </td>
                    <td>Year
                    </td>
                    <td>City
                    </td>
                    <td>Type
                    </td>
                    <td>Disciple
                    </td>
                    <td>Placing
                    </td>
                    <?php if (!empty($_SESSION["id"])) { ?>
                      <td>Save</td>
                      <td>Exit</td>
                    <?php } ?>
                  </tr>

                  <?php

                  while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <form action="" method="post">
                      <tr>
                        <td>
                          <?php echo $row["id"] ?>
                        </td>
                        <td>
                          <input type="text" class="form-control" placeholder="<?php echo $row["name"] ?>" name="name">
                        </td>
                        <td>
                          <input type="text" class="form-control" placeholder=" <?php echo $row["surname"] ?>"
                            name="surname">
                        </td>
                        <td>
                          <?php echo $row["year"] ?>
                        </td>
                        <td>
                          <?php echo $row["city"] ?>
                        </td>
                        <td>
                          <?php echo $row["type"] ?>
                        </td>
                        <td>
                          <select class="form-control" id="1" name="disciple">
                            <?php while ($rowDis = mysqli_fetch_assoc($resultDis)) { ?>
                              <option name="disciple" value="<?= $rowDis['disciple'] ?>"><?= $rowDis['disciple'] ?></option>
                            <?php } ?>
                          </select>
                        </td>
                        <td>
                          <input type="number" min="1" class="form-control" placeholder=" <?php echo $row["placing"] ?>"
                            name="placing">
                        </td>
                        <?php if (!empty($_SESSION["id"])) { ?>
                          <td> <input class="btn btn-primary" type="submit" name="sent"></td>
                          <td><a href="index.php" class="btn btn-danger">Stop editing</a></td>
                        <?php } ?>
                      </tr>
                    </form>
                    <?php
                  }
                  ?>

                </table>
              </div>


            </div>
            <?php
            }
            ?>
        </div>
      </div>
    </div>
  </div>
  </div>

</body>

</html>
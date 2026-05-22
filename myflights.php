<?php
session_start();
include "./config/db.php";
include "./includes/header.php";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=airline_db","root","");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}

$user_id = $_SESSION['user_id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ?");
$stmt->execute([$user_id]);

$flights = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
<title>My Flights</title>

<style>
body{
    font-family:'Segoe UI',sans-serif;
    background:#f6f3f5;
}

/* TITLE */
h1{
    text-align:center;
    margin:99px 0;
    color:#333;
}

/* CARD */
.flight-card{
    width:80%;
    margin:20px auto;
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    transition:0.3s;
}

.flight-card:hover{
    transform:translateY(-5px);
}

/* ROUTE */
.route{
    display:flex;
    align-items:center;
    gap:40px;
}

/* CITY */
.city h2{
    font-size:26px;
    color:#222;
}

.city p{
    font-size:12px;
    color:#777;
}

/* LINE */
.line{
    width:220px;
    height:2px;
    background:#ddd;
    position:relative;
}

.line::before{
    content:"✈";
    position:absolute;
    left:0;
    top:-12px;
    color:#28a745;
    font-size:18px;
}

.line::after{
    content:"●";
    position:absolute;
    right:0;
    top:-10px;
    color:#28a745;
    
}

/* CENTER INFO */
.info{
    text-align:center;
    padding:35px;
    border-radius:70px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

.info h3{
    color:#a63d63;
        padding: 40px 0;

}

.info p{
    color:#555;

}

/* ACTIONS */
.actions{
    display:flex;
    flex-direction:column;
    gap:10px;
}

/* BUTTONS */
.print{
    background:#28a745;
    color:white;
    border:none;
    padding:10px 18px;
    border-radius:20px;
    cursor:pointer;
}

.cancel{
    background:#dc3545;
    color:white;
    border:none;
    padding:10px 18px;
    border-radius:20px;
    cursor:pointer;
}

/* EMPTY */
.empty{
    text-align:center;
    margin-top:50px;
    color:#777;
    font-size:18px;
}
</style>
</head>

<body>

<h1>My Flights</h1>

<?php if(!empty($flights)): ?>

    <?php foreach($flights as $row): ?>

    <div class="flight-card">

        <!-- LEFT -->
        <div class="route">
            <div class="city">
                <h2><?= $row['from_city'] ?></h2>
                <p>Departed</p>
                <h3><?= $row['depart_time'] ?></h3>
                <small><?= $row['flight_date'] ?></small>
            </div>

            <div class="line"></div>

            <div class="city">
                <h2><?= $row['to_city'] ?></h2>
                <p>Arrived</p>
                <h3><?= $row['arrive_time'] ?></h3>
                <small><?= $row['flight_date'] ?></small>
            </div>
        </div>

        <!-- CENTER -->
        <div class="info">
            <h3><?= $row['airline'] ?></h3>
            <p><?= $row['flight_no'] ?></p>
        </div>

        <!-- RIGHT -->
         
       <a href="user/print_ticket.php?id=<?= $row['id'] ?>">
    <button class="print">Print</button>
</a>

<a href="user/cancel_tiket.php?id=<?= $row['id'] ?>"
  >
    <button class="cancel">Cancel</button>
</a>

    </div>

    <?php endforeach; ?>
    <?php if(isset($_GET['success'])): ?>
    <p style="color:green;text-align:center;">
        ✅ Payment Successful! Flight Booked.
    </p>
<?php endif; ?>
<?php else: ?>

    <p style="text-align:center;">No flights booked yet</p>

<?php endif; ?>

</body>
</html>
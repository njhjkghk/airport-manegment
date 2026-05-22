

<?php
session_start();
// ===== DATABASE CONNECTION =====
$host = 'localhost';
$db   = 'airline_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: ".$e->getMessage());
}
$stmt=$pdo->query("SELECT * FROM flightss");
$flights=$stmt->fetchAll();
// ===== FETCH DATA =====
$footer_items = $pdo->query("SELECT * FROM footer_info")->fetchAll();
$places = $pdo->query("SELECT * FROM places")->fetchAll();


// ===== HANDLE BOOKING =====
if(isset($_POST['book_flight'])){

    // Check login
    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Sanitize input
    $from = htmlspecialchars($_POST['from_city']);
    $to = htmlspecialchars($_POST['to_city']);
    $date = $_POST['flight_date'];
    $passengers = (int)$_POST['passengers'];

    // Validation
    if($from == $to){
        header("Location: index.php?error=samecity");
        exit();
    }

    if($passengers <= 0){
        header("Location: index.php?error=passengers");
        exit();
    }

    // Insert booking
    $stmt = $pdo->prepare("INSERT INTO flight_bookings (user_id, from_city, to_city, flight_date, passengers) VALUES (?,?,?,?,?)");
    $stmt->execute([$user_id, $from, $to, $date, $passengers]);

    // Redirect
    
    header("Location: index.php?success=1");
    
    exit();

}
?>
<?php if(isset($_SESSION['user_id'])): ?>

<div class="user-box">
    <span class="user-icon">👤</span>
    <span class="user-name"><?= $_SESSION['user_name']; ?></span>

    <a href=" auth/logout.php">
        <button class="logout-btn">Logout →</button>
    </a>
</div>

<?php else: ?>

<a href="login.php">
    <button class="auth-login-btn">Login →</button>
</a>

<?php endif; ?>
<style>
    .user-box{
    display:flex;
    align-items:center;
    gap:12px;
}

.user-icon{
    font-size:18px;
    color:white;
}

.user-name{
    font-weight:600;
    color:white;
    letter-spacing:1px;
}

/* LOGOUT BUTTON */
.logout-btn{
    background:#a63d63;
    border:none;
    padding:10px 20px;
    border-radius:25px;
    color:white;
    cursor:pointer;
    font-weight:600;
    transition:0.3s;
}

.logout-btn:hover{
    background:#8e3458;
}

/* LOGIN BUTTON */
.login-btn{
    background:#a63d63;
    border:none;
    padding:10px 20px;
    border-radius:25px;
    color:white;
    cursor:pointer;
}
</style>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<title> AirVista</title>
<style>
*{ margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',sans-serif; }
body{ display:flex; flex-direction:column; min-height:100vh; }
header{ position: fixed; top:0; left:0; width:100%; display:flex; justify-content:space-between; align-items:center; padding:22px 60px; background:rgba(255,255,255,0.08); backdrop-filter:blur(8px); z-index:1000; color:white; transition: all 0.3s ease; }
header .logo{ font-size:26px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#a63d63;  padding:8px 16px; border-radius:30px; }
header nav a{ margin:0 20px; text-decoration:none; color:#a63d63; font-weight:900; letter-spacing:1px;  padding:8px 16px; border-radius:30px; }
header .login{ background:#a63d63; border:none; padding:10px 22px; color:white; border-radius:30px; cursor:pointer; }

.hero{ height:110vh; background: url('images/airport-hero.jpg') center/cover no-repeat; display:flex; justify-content:center; align-items:flex-end; padding-bottom:0; position:relative; margin-top:80px; margin-bottom: 90px; }
.content{ width:90%; max-width:1100px; background:white; border-radius:22px; padding:35px; box-shadow:0 8px 25px rgba(0,0,0,0.08); margin-bottom:-90px; position:relative; }

.tabs{ display:flex; justify-content:center; margin-bottom:40px; }
.tabs button{ padding:14px 40px; border:none; border-radius:30px; margin:0 8px; background:#efefef; cursor:pointer; font-weight:600; }
.tabs .active{ background:#a63d63; color:white; }

.form{ display:grid; grid-template-columns:repeat(4,1fr); gap:20px;  }
.form input{ padding:14px; border:none; border-bottom:2px solid #ddd; outline:none; font-size:15px; }
.search-btn{ display:block; margin:30px auto 0; padding:14px 35px; border:none; border-radius:12px; background:#a63d63; color:white; font-size:16px; cursor:pointer; }
@media(max-width:768px){ .form{ grid-template-columns:1fr 1fr; } }
@media(max-width:500px){ .form{ grid-template-columns:1fr; } }

.places-section h1{ font-size:32px; color:#333; margin-bottom:60px; }
.places-section{ text-align:center; margin-top:50px;  margin-bottom: 70px;}
.places-container{ display:flex; justify-content:center; gap:30px; flex-wrap:wrap; margin-bottom: 10px;}
.place-card{ width:260px; background:white; border-radius:40px; overflow:hidden; box-shadow:0 6px 15px rgba(0,0,0,0.08); }
.place-card img{ width:100%; height:300px; object-fit:cover; display:block; }
.place-card p{ padding:15px; font-size:28px; color:#444; }

.footer{ background:#8e3458; color:white; padding:20px 40px; margin-top:auto; }
.footer-container{ display:flex; justify-content:space-between; flex-wrap:wrap; gap:30px; }
.footer-left video{ width:300px; height:180px; object-fit:cover; border:5px solid white; }
.footer-info h2, .footer-contact h2{ margin-bottom:15px; font-size:28px; }
.footer-info p, .footer-contact p{ margin-bottom:10px; font-size:18px; }
.footer-logo{ 
            
            
    display:flex;
    align-items:center;
    font-size:28px;
    font-weight:bold;
    color:wheat;
    position:relative;
    text-decoration: none;
}


  
hr{ margin:20px 0 15px; border:1px solid rgba(255,255,255,0.4); }
.footer-bottom{ display:flex; justify-content:space-between; flex-wrap:wrap; }
.footer-bottom p{ font-size:16px; }
.social-icons span{ margin-left:12px; font-size:20px; cursor:pointer; }
.message{ text-align:center; color:green; margin-top:20px; font-weight:700; font-size:18px; }
</style>
</head>

<body>
<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    background:#f7f5f6;
}



.logo{
    font-weight:bold;
    color:#a63d63;
    font-size:20px;
}

/* NAV */
nav a{
    margin:0 15px;
    text-decoration:none;
    color:#333;
    font-weight:600;
    position:relative;
    margin-bottom: 80px;
}

/* HOVER EFFECT */
nav a:hover{
    color:#a63d63;
}

nav a::after{
    content:"";
    position:absolute;
    left:0;
    bottom:-5px;
    width:0%;
    height:2px;
    background:#a63d63;
    transition:0.3s;
}

nav a:hover::after{
    width:100%;
}

.user{
    display:flex;
    align-items:center;
    gap:15px;
}

.logout{
    background:#a63d63;
    border:none;
    padding:8px 18px;
    border-radius:20px;
    color:white;
}

/* MAIN */
.container{
    width:85%;
    margin:40px auto;

}

/* TOP SECTION */
.top{
    display:flex;
    gap:30px;
    align-items:center;
    margin-bottom:40px;
}

.top img{
    width:50%;
    border-radius:15px;
}

.top .text{
    width:50%;
        margin-bottom: 80px;

}

.top h2{
    margin-bottom:10px;
        margin-bottom: 80px;

}

.top p{
    color:#555;
    line-height:1.6;

}

/* BOTTOM SECTION */
.bottom{
    display:flex;
    gap:30px;
    align-items:center;
}

.bottom .text{
    width:60%;
}

.bottom img{
    width:40%;
    border-radius:15px;
}

.bottom h2{
    margin-bottom:10px;
}

.bottom p{
    color:#555;
    line-height:1.6;
    text-align:center;
}
.text h2{
    font-size:28px;
        margin-bottom: 80px;
}
</style>
<header>
    <div class="logo">
        <span class="plane">✈</span>
        <span> AirVista </span> </div>
        <style>
            
            .logo{
    display:flex;
    align-items:center;
    font-size:28px;
    font-weight:bold;
    color:#a63d63;
    position:relative;
    text-decoration: none;
}

.plane{
    font-size:40px;
    transform:rotate(-150deg);
    margin-right:10px;
}

.logo::after{
    content:"";
    position:absolute;
    bottom:-10px;
    right:40px;
    width:150px;
    height:50px;
    border-bottom:3px solid #a63d63;
    border-radius:50%;
    text-decoration: solid #a63d63;
}
        </style>

    <nav>
        <a href="index.php">HOME</a>
        <a href="myflights.php">MY FLIGHTS</a>
        <a href="about.php">ABOUT</a>
        <a href="#">FEEDBACK</a>
    </nav>

    <!-- ✅ PUT IT HERE -->
    <?php if(isset($_SESSION['user_id'])): ?>

    <div class="header-right">
        <span style="color:white; color:#a63d63; font-weight:900; align-items:center; margin-left:50px; 
    gap:12px;"><i class="fa-solid fa-user"></i><?= $_SESSION['user_name']; ?></span>

        <a href="auth/logout.php">
            <span style="margin-left: 49px;"></span>
            <button class="login">Logout →</button>
    
        </a>
       
    </div>
    

    <?php else: ?>

    <a href="auth/login.php">
        <button class="login">Login →</button>
    </a>

    <?php endif; ?>

</header>
<section class="hero">
<div class="content-box">

    <!-- TABS -->
    <div class="tabs">
        <button class="tab active" onclick="showTab('book')">Book A Flight</button>
        <button class="tab " onclick="showTab('status')">Flight Status</button>

    </div>

    <!-- ================= BOOK ================= -->
    <div id="book" class="tab-content active">

        <form action="./user/video .php" method="POST" class="form-row">

            <div class="input-box">
                <label>From</label>
                <select name="from_city" required>
                    <option value="">Select City</option>
                    <?php foreach($places as $place): ?>
                        <option value="<?= $place['name'] ?>"><?= $place['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="input-box">
                <label>To</label>
                <select name="to_city" required>
                    <option value="">Select City</option>
                    <?php foreach($places as $place): ?>
                        <option value="<?= $place['name'] ?>"><?= $place['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="input-box">
                <label>Departure Date</label>
                <input type="date" name="flight_date" required>
            </div>

            <div class="input-box">
                <label>Passengers</label>
                <input type="number" name="passengers" min="1" required>
            </div>

            <button class="main-btn">Search Flights</button>
        </form>

    </div>

    <!-- ================= STATUS ================= -->
   <!-- ================= STATUS ================= -->
<div id="status" class="tab-content active" style="display:none;padding:40px;">

    <!-- ✅ PUT FORM HERE -->
    <form method="GET" action="./user/flight_status.php" class="form-row">

        <div class="input-box">
            <label>From</label>
            <select name="from_city" required>
                <option value="">Select City</option>
                <?php foreach($places as $place): ?>
                    <option value="<?= $place['name'] ?>"><?= $place['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="input-box">
            <label>To</label>
            <select name="to_city" required>
                <option value="">Select City</option>
                <?php foreach($places as $place): ?>
                    <option value="<?= $place['name'] ?>"><?= $place['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="input-box">
            <label>Departure Date</label>
            <input type="date" name="flight_date" required>
        </div>

        <button type="submit" class="main-btn">Check Status</button>

    </form>

</div>

</div>
</section>
<style>
    .hero{
    display:flex;
    justify-content:center;
    margin-top:50px;
}

.content-box{
    background:white;
    padding:40px;
    border-radius:20px;
    width:80%;
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
}

/* TABS */
.tabs{
    display:flex;
    justify-content:center;
    margin-bottom:25px;
}

.tab{
    border:none;
    padding:12px 40px;
    border-radius:30px;
    background:#eee;
    cursor:pointer;
    font-weight:600;
    margin:0 5px;
    transition:0.3s;
}

.tab.active{
    background:#a63d63;
    color:white;
}

/* FORM */
.form-row{
    display:flex;
    justify-content:center;
    gap:20px;
    flex-wrap:wrap;
}

.input-box{
    display:flex;
    flex-direction:column;
}

.input-box label{
    font-size:13px;
    color:#666;
    margin-bottom:5px;
}

.input-box input,
.input-box select{
    padding:10px;
    border-radius:8px;
    border:1px solid #ddd;
    min-width:180px;
}

/* BUTTON */
.main-btn{
    background:#a63d63;
    color:white;
    border:none;
    padding:12px 25px;
    border-radius:25px;
    cursor:pointer;
    margin-top:20px;
}

/* STATUS CARD */
.status-card{
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:#fafafa;
    padding:40px;
    border-radius:15px;
    margin-top:20px;
}

.left, .right{
    text-align:center;
}

.center{
    font-size:20px;
}

/* BADGES */
.badge{
    padding:6px 15px;
    border-radius:20px;
    color:white;
    font-size:12px;
}

.running{background:#28a745;}
.arrived{background:#007bff;}
.pending{background:#999;}
</style>
<script>
function showTab(tab){
    document.querySelectorAll('.tab-content').forEach(e=>e.style.display='none');
    document.getElementById(tab).style.display='block';

    document.querySelectorAll('.tab').forEach(e=>e.classList.remove('active'));
    event.target.classList.add('active');
}
</script>
>

<section class="section-two">
  <div class="container-two">

    <!-- LEFT IMAGES -->
    <div class="left-images">
      <div class="big-img"></div>
      <div class="small-img"></div>
    </div>

    <!-- RIGHT SIDE TEXT -->
    <div class="right-text">
      <h1>Start Planning Your<br>Next Trip</h1>

      <div class="grid-text">

        <div>
          <h3>Experience Tranquility</h3>
          <p>Serenity Haven offers a tranquil escape, featuring comfortable seating, calming ambiance, and attentive service.</p>
        </div>

        <div>
          <h3>Elevate Your Experience</h3>
          <p>Designed for discerning travelers, this exclusive lounge offers premium amenities, assistance, and private workspaces.</p>
        </div>

        <div>
          <h3>A Welcoming Space</h3>
          <p>Creating a family-friendly atmosphere, the Family Zone is perfect for parents and children.</p>
        </div>

        <div>
          <h3>A Culinary Delight</h3>
          <p>Immerse yourself in flavors, offering international cuisines, gourmet dishes, and beverages.</p>
        </div>

      </div>
    </div>

  </div>
</section>
<style>
    .section-two{
    background:#ffffff;
    padding:100px 60px; /* space u dhexeeya sections-ka */
    margin-bottom: 10px;
}

.container-two{
    max-width:1200px;
    margin:auto;
    display:grid;
    grid-template-columns:1fr 1.5fr; /* left images + right text */
    gap:60px;
    align-items:center;
}

/* LEFT IMAGES */
.left-images{
    position:relative;
    min-height:350px; /* hubi in height-ka uu ku filan yahay sawirka */
}

.big-img{
    width:280px;
    height:350px;
    background: url('images/flight-search.jpg') center/cover no-repeat;
    display:block;
    border-radius:50%;
}

.small-img{
    width:180px;
    height:180px;
    background: url('images/booking-bg.jpg') center/cover no-repeat;
    display:block;
    border-radius:50%;
    position:absolute;
    bottom:-30px;
    right:20px;
    border:8px solid white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* RIGHT TEXT */
.right-text h1{
    font-size:42px;
    margin-top:40px;
    margin-bottom:30px;
    color:#333;
}

.grid-text{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:30px;
}

.grid-text h3{
    font-size:18px;
    margin-bottom:10px;
}

.grid-text p{
    color:#666;
    line-height:1.6;
    font-size:20px;
}

/* RESPONSIVE */
@media(max-width:900px){
    .container-two{
        grid-template-columns:1fr; /* images + text stacked */
    }

    .grid-text{
        grid-template-columns:1fr;
    }

    .big-img{
        margin:auto;
    }

    .small-img{
        position:relative;
        bottom:0;
        right:0;
        margin:20px auto 0;
    }
}

</style>
<!-- PLACES -->
<section class="places-section">
    <h1>Explore New Places</h1>
    <div class="places-container">
        <?php foreach($places as $place): ?>
            <div class="place-card">
                <img src="<?= $place['image'] ?>" alt="<?= $place['name'] ?>">
                <p><?= $place['name'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-left">
            <video width="300" height="180" autoplay loop muted playsinline>
                <source src="images/welcome-plane.mp4" type="video/mp4">
            </video>
        </div>
        <div class="footer-info">
            <h2>INFORMATION</h2>
            <?php foreach($footer_items as $item){ if($item['type']=='info') echo "<p>{$item['content']}</p>"; } ?>
        </div>
        <div class="footer-contact">
            <h2>CONTACT</h2>
            
            <?php foreach($footer_items as $item){ 
                if($item['type']=='contact'){ 
                    if($item['title']=='Email') echo "<p>{$item['content']}</p>"; 
                    else echo "<div class='footer-logo'>{$item['content']}</div>"; 
                } 
            } ?>
        </div>
    </div>
    <hr>
    <div class="footer-bottom">
        <p>Copyright © 2026 Team GMADS. All rights reserved.</p>
           
        <div class="social-icons">
            <i class="fa-brands fa-facebook-f"></i>
            
            <i class="fa-brands fa-twitter"></i>
            <i class="fa-brands fa-instagram"></i>
            <i class="fa-brands fa-youtube"></i>
        </div>
    </div>
</footer>

<script>
// Header animation
const header = document.querySelector('header');
window.addEventListener('scroll', () => {
    if(window.scrollY > 50){
        header.style.padding = '10px 40px';
        header.style.background = 'rgba(255,255,255,0.9)';
        header.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
        header.style.color = '#333';
    } else {
        header.style.padding = '22px 60px';
        header.style.background = 'rgba(255,255,255,0.08)';
        header.style.boxShadow = 'none';
        header.style.color = 'white';
    }
});
</script>

</body>
</html>

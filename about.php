<?php
session_start();
include "./config/db.php";
include "./includes/header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>About Us</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
    font-weight: bold;
}

body{
    background:#f8f5f6;
    box-sizing: border-box;
    padding: 20px;
    color: #555;
}

/* CONTAINER */
.container{
    width:85%;
    margin:99px auto;
}

/* TOP TEXT */
.top-text{
    text-align:center;
    margin-bottom:60px;
    color:#a63d63;
    font-size:18px;
    line-height:1.6;
    
}

/* ROW */
.row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:40px;
    margin-bottom:60px;
    
}

/* IMAGE */
.row img{
    width: 50%;
    height: 240px;
    border-radius:20px;
    object-fit:cover;

}

/* IMAGE HOVER (ZOOM EFFECT) */
.row img:hover{
    transform:scale(1.05);
}

/* TEXT BOX */
.text-box{
    width:50%;
    padding:30px;
    border-radius:20px;
    background: rgba(43, 122, 232, 0.05);
    box-shadow:0 8px 25px rgba(0,0,0,0.05);
    transition:0.4s;
}



/* TITLE */
.text-box h2{
    margin-bottom:15px;
    font-size:24px;
}

/* PARAGRAPH */
.text-box p{
    color:#555;
    line-height:1.7;
    font-size:19px;
}

/* REVERSE */
.row.reverse{
    flex-direction:row-reverse;
}

/* RESPONSIVE */
@media(max-width:900px){
    .row{
        flex-direction:column;
    }

    .row.reverse{
        flex-direction:column;
    }

    .row img,
    .text-box{
        width:100%;
    }
}
</style>

</head>

<body>

<div class="container">

    <!-- TOP TEXT -->
    <div class="top-text">
        <p>
        <strong>Welcome to AirVista</strong>, your trusted partner in simplifying air travel.<br>
        We provide a seamless and efficient booking experience so your journey starts stress-free.
        </p>
    </div>

    <!-- OUR MISSION -->
    <div class="row">
        <div class="text-box">
            <h2>Our Mission</h2>
            <p>
            Our mission is to transform the way you book flights — making it faster,
            easier, and more reliable than ever before. We focus on providing a
            smooth user experience with powerful tools and clear information.
            </p>
        </div>

<img src="images/eio.jpg" alt="">
    </div>

    <!-- WHO WE ARE -->
    <div class="row reverse">
        <div class="text-box">
            <h2>Who We Are</h2>
            <p>
            AirVista is a modern flight booking platform designed for travelers
            of all types. Our team is passionate about delivering convenience,
            transparency, and excellence in every booking experience.
            </p>
        </div>
        <img src="images/planee.jpg" alt="">

    </div>
 <!-- OUR MISSION -->
    <div class="row">
        <div class="text-box">
            <h2>Our Commitment to You</h2>
            <p>
          At AirVista, we are committed to excellence in every aspect of our service.
                Whether you're planning a business trip, a family vacation, or a spontaneous getaway,
                we're here to make your travel dreams a reality.
            
            <br><br>

              Trust us to be your companion in the skies, and let us take you to your next destination
                with ease and comfort.
                </p>
        </div>

<img src="images/team.jpg" alt="">


    </div>

</div>
<?php include "./includes/footer.php"; ?>

</body>
</html>
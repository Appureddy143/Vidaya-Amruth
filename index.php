<?php
session_start();
include('db-config.php'); // Ensure you include your database configuration

// Fetch latest notifications from principal
$notificationStmt = $conn->prepare("SELECT message, created_at FROM notifications ORDER BY created_at DESC LIMIT 5");
$notificationStmt->execute();
$notificationResult = $notificationStmt->get_result();
$notifications = $notificationResult->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vidya Amruth PU College</title>

    <link rel="stylesheet" href="style.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
</head>
<body>
    <nav>
        <img src="img/logo.png" class="logo" alt="Logo">

        <div class="navigation">
            <ul>
                <i id="menu-close" class="fas fa-times"></i>
                <li><a href="index.html" class="active">Home</a></li>
                <li><a href="about.html">About Us</a></li>
                <li><a href="courses.html">Courses</a></li>
                <li><a href="contact.html">Admision</a></li>
                <li><a href="clg/index.php">Login</a></li>
            </ul>

            <img src="img/menu.png" id="menu-btn" alt="Menu" >
        </div>
    </nav>
    <!-- Navigation ends -->

    <!-- Hero section starts -->
    <div id="home">
        <video>
        </video>
        
        <div class="content">
            <h1>Enhance Your Future With Vidya Amruth PU College</h1>
           <center> <p>Quality education for a brighter tomorrow</p> </center>
        </div>
    </div>
    <!-- Hero section ends -->

<br>
<br>
    <!-- Features section ends -->

    <!-- Notifications section -->
    <div class="notifications">
        <h4>📢 Latest Notifications from Principal</h4>
        <ul>
            <?php foreach ($notifications as $note): ?>
                <li><strong><?= date("d-m-Y H:i", strtotime($note['created_at'])) ?>:</strong> <?= htmlspecialchars($note['message']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<br>
<br>
<br>
    <!-- Registration section starts -->
    <section id="registration">
        <div class="reminder">
            <p>Submit your details for enquiry</p>
            <h1>Enquiry Form</h1>

            <div class="time">

            </div>
        </div>

        <div class="form">
    <form action="submit_enquiry.php" method="post">
        <label>Full Name:</label><br>
        <input type="text" name="full_name" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Phone:</label><br>
        <input type="text" name="phone" required><br><br>

        <label>Course Interested In:</label><br>
        <input type="text" name="course" required><br><br>

        <label>Message:</label><br>
        <textarea name="message" rows="4" cols="40"></textarea><br><br>

        <input type="submit" value="Submit Enquiry">
    </form>
            </div>
        </div>
    </section>

    <!-- Footer section starts -->
    <footer>
        <div class="copyright">
            <p>
                Copyright &copy; 2022 All rights reserved | This template is made by <a href="https://atulcodex.com" target="_blank">atulcodex</a>
            </p>
        </div>
    </footer>
    <!-- Footer section ends -->

    <script>
        // Show menu links on burger click
        $('#menu-btn').click(function(){
            $('nav .navigation ul').addClass('active');
        });

        // Hide navbar on click function
        $('#menu-close').click(function(){
            $('nav .navigation ul').removeClass('active');
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('video-background');
            
            // Set volume to 50% when video can play
            video.addEventListener('canplay', function() {
                // Remove muted attribute to allow sound
                video.removeAttribute('muted');
                video.volume = 0.9;
            });
            
            // Ensure video plays even when tab is not active
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible') {
                    video.play().catch(e => console.log('Video play failed:', e));
                }
            });
        });
    </script>
</body>
</html>

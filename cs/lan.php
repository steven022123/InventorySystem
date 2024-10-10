<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Starbucks</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="shortcut icon" type="image/logo" href="">
    <style>
      /* Global Body Styles */
      body {
        background-color: #8fbc8f; /* Light mode background color */
        transition: background-color 0.3s ease; /* Smooth transition for theme change */
      }
      .dark-theme {
        background-color: #2c2c2c; /* Dark mode background color */
      }

      /* Footer Styles */
      .footer {
        background-color: inherit; /* Same background as the body */
        text-align: center;
        padding: 20px 0;
        color: #fff;
      }
      .dark-theme .footer {
        color: #ccc; /* Slightly lighter text for dark mode */
      }
      
      /* Social Icon Styling */
      .social a {
        margin: 0 15px;
        color: #009e60;
        text-decoration: none;
        font-size: 20px;
        transition: color 0.3s ease;
      }
      .dark-theme .social a {
        color: #ddd; /* Adjust color for dark mode */
      }

      .social a:hover {
        color: #4caf50; /* Hover effect */
      }
    </style>
  </head>
  <body>
    <header class="header">
      <i class="bx bx-menu" id="menu-icon"></i>
      <a href="#home" class="logo">
        <img src="10.png" alt="SB" class="logo">
        
      </a>
      <nav class="navbar">
        <a href="#home" class="active">Home</a>
        
        <a href="login.php">Login</a>
        <a href="register.php">Sign up</a>
      </nav>
      <div class="modes">
        <img src="daa.png" id="icon">
      </div>
    </header>

    <section class="home" id="home">
      <div class="home-content">
        <h1>Starbucks Philippines</h1>
        <h2>San Miguel Tarlac</h2>
        <h3 class="text-animation">World Class <span></span></h3>
        <p>Starbucks is a multinational coffee company known for its high-quality coffee beans and unique coffee drinks. It's a popular spot for people to relax, work, or socialize while enjoying their favorite beverages.</p>
        <div class="social-icons">
          <a href="https://www.youtube.com/?app">
            <i class='bx bxl-youtube'></i>
          </a>
          <a href="https://www.facebook.com/login/">
            <i class='bx bxl-facebook'></i>
          </a>
          <a href="https://www.instagram.com/">
            <i class='bx bxl-instagram'></i>
          </a>
          <a href="https://x.com/i/flow/login">
            <i class="fa-brands fa-twitter"></i>
          </a>
        </div>
      </div>
      <div class="home-img">
        <img src="cof.jpg" alt="1.jpg">
      </div>
    </section>

    <section class="about" id="about">
      <div class="about-img">
        <img src="bucks.jpg" alt="About Image">
      </div>
      <div class="about-content">
        <h2 class="heading">About <span>Starbucks</span></h2>
        <h3>World Class Coffee & Pastries</h3>
        <p>Starbucks, a global coffeehouse chain, was founded in 1971 in Seattle, Washington...</p>
      </div>
    </section>
   
    <footer class="footer">
      <div class="social">
        <a href="https://www.youtube.com/?app"><i class='bx bxl-youtube'></i></a>
        <a href="https://www.facebook.com/login/"><i class='bx bxl-facebook'></i></a>
        <a href="https://www.instagram.com/"><i class='bx bxl-instagram'></i></a>
        <a href="https://x.com/i/flow/login"><i class="fa-brands fa-twitter"></i></a>
      </div>
      <p class="copyright">Copyright &copy; Starbucks Philippines | All Rights Reserved</p>
    </footer>

    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <script src="script.js"></script>
    <script>
      var icon = document.getElementById("icon");
      let localData = localStorage.getItem("theme");

      if (localData == "light") {
        icon.src = "daa.png";
        document.body.classList.remove("dark-theme");
      } else if (localData == "dark") {
        icon.src = "sun.webp";
        document.body.classList.add("dark-theme");
      }

      icon.onclick = function() {
        document.body.classList.toggle("dark-theme");
        if (document.body.classList.contains("dark-theme")) {
          icon.src = "sun.webp";
          localStorage.setItem("theme", "dark");
        } else {
          icon.src = "daa.png";
          localStorage.setItem("theme", "light");
        }
      }
    </script>
  </body>
</html>
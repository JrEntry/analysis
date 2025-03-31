<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rolsa Technologies</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/index.css" />
  <!-- Custom CSS -->
</head>

<body>
  <?php include 'includes/header.php'; ?>

  <!-- Hero Section - Carousel -->
  <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <!-- Carousel Indicators -->
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true"
        aria-label="Slide 1"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>

    <!-- Carousel Items -->
    <div class="carousel-inner">
      <!-- Slide 1 -->
      <div class="carousel-item active" style="
            background-image: url('https://hips.hearstapps.com/hmg-prod/images/gettyimages-181062267-644abe15a0cc9.jpg');
          ">
        <div class="carousel-overlay"></div>
        <div class="carousel-caption text-start h-100 d-flex flex-column justify-content-center">
          <div class="container">
            <h1 class="hero-title mb-3">Powering a greener tomorrow</h1>
            <p class="lead mb-4 d-none d-sm-block">
              Discover our range of sustainable energy solutions that help
              reduce your carbon footprint while saving you money.
            </p>
            <a href="#" class="btn btn-lg btn-yellow-rolsa">Book a consultation now!</a>
          </div>
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="carousel-item" style="
            background-image: url('https://urpltd.co.uk/wp-content/uploads/2023/01/Solar-Panel-Install-In-Progress.jpg');
          ">
        <div class="carousel-overlay"></div>
        <div class="carousel-caption text-start h-100 d-flex flex-column justify-content-center">
          <div class="container">
            <h1 class="hero-title mb-3">Save money with solar</h1>
            <p class="lead mb-4 d-none d-sm-block">
              Reduce your energy bills by up to 70% with our high-efficiency
              solar panel installations.
            </p>
            <a href="#" class="btn btn-lg btn-yellow-rolsa">Book a consultation now!</a>
          </div>
        </div>
      </div>

      <!-- Slide 3 -->
      <div class="carousel-item" style="
            background-image: url('https://www.kaspersky.com/content/en-global/images/repository/isc/2020/how-to-keep-your-smart-home-safe.jpg');
          ">
        <div class="carousel-overlay"></div>
        <div class="carousel-caption text-start h-100 d-flex flex-column justify-content-center">
          <div class="container">
            <h1 class="hero-title mb-3">Smart homes, smarter living</h1>
            <p class="lead mb-4 d-none d-sm-block">
              Take control of your energy usage with our intelligent home
              automation systems.
            </p>
            <a href="#" class="btn btn-lg btn-yellow-rolsa">Book a consultation now!</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Carousel Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>

  <!-- Services Section -->
  <section class="py-5 bg-light">
    <div class="container py-4">
      <div class="row g-4">
        <!-- Service 1 -->
        <div class="col-lg-4 col-md-6">
          <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
            <img src="https://manorlakes.com.au/sites/default/files/solar-power.jpg" class="card-img-top"
              alt="Solar Energy Solutions" />
            <div class="card-body p-4">
              <h3 class="card-title text-green-rolsa fw-semibold fs-4 mb-3">
                <i class="fas fa-solar-panel"></i> Solar Energy Solutions
              </h3>
              <p class="card-text text-dark-rolsa mb-4">
                Harness the power of the sun with our high-efficiency solar
                panels. Reduce your electricity bills and minimize your carbon
                footprint.
              </p>
              <a href="#" class="text-green-rolsa fw-medium text-decoration-none d-inline-flex align-items-center">
                Learn more <i class="fas fa-arrow-right ms-2"></i>
              </a>
            </div>
          </div>
        </div>
        <!-- Service 2 -->
        <div class="col-lg-4 col-md-6">
          <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
            <img src="https://affordableevcharging.com/wp-content/uploads/2023/12/do-i-need-new.jpg"
              class="card-img-top" alt="EV Charging Stations" />
            <div class="card-body p-4">
              <h3 class="card-title text-green-rolsa fw-semibold fs-4 mb-3">
                <i class="fas fa-charging-station"></i> EV Charging Stations
              </h3>
              <p class="card-text text-dark-rolsa mb-4">
                Power your electric vehicle with clean energy. Our home
                charging solutions make sustainable transportation easier than
                ever.
              </p>
              <a href="#" class="text-green-rolsa fw-medium text-decoration-none d-inline-flex align-items-center">
                Learn more <i class="fas fa-arrow-right ms-2"></i>
              </a>
            </div>
          </div>
        </div>
        <!-- Service 3 -->
        <div class="col-lg-4 col-md-6">
          <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
            <img src="https://planradar-website.s3.amazonaws.com/production/uploads/2020/05/Pametni-dom.jpg"
              class="card-img-top" alt="Smart home systems" />
            <div class="card-body p-4">
              <h3 class="card-title text-green-rolsa fw-semibold fs-4 mb-3">
                <i class="fas fa-home-lg"></i> Smart home systems
              </h3>
              <p class="card-text text-dark-rolsa mb-4">
                Take control of your energy consumption with intelligent home
                automation. Monitor and optimize your usage in real-time.
              </p>
              <a href="#" class="text-green-rolsa fw-medium text-decoration-none d-inline-flex align-items-center">
                Learn more <i class="fas fa-arrow-right ms-2"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Impact Section -->
  <section class="bg-dark-rolsa text-white py-5 text-center">
    <div class="container py-4">
      <h2 class="display-5 mb-5">Our Impact On The Environment</h2>
      <div class="row g-4">
        <div class="col-md-3 col-6">
          <p class="display-4 text-yellow-rolsa fw-bold mb-2">
            9,999
          </p>
          <p class="fs-6"><i class="fas fa-solar-panel"></i> Solar Installations</p>
        </div>
        <div class="col-md-3 col-6">
          <p class="display-4 text-yellow-rolsa fw-bold mb-2">99M+</p>
          <p class="fs-6"><i class="fas fa-bolt"></i> Kwh Clean Energy Generated</p>
        </div>
        <div class="col-md-3 col-6">
          <p class="display-4 text-yellow-rolsa fw-bold mb-2">9,999+</p>
          <p class="fs-6"><i class="fas fa-tree"></i> Tons of CO2 reduced</p>
        </div>
        <div class="col-md-3 col-6">
          <p class="display-4 text-yellow-rolsa fw-bold mb-2">99%</p>
          <p class="fs-6"><i class="fas fa-smile"></i> Customer Satisfaction rate</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-dark-rolsa text-white py-5">
    <div class="container py-4">
      <div class="row g-4">
        <!-- Company Info -->
        <div class="col-lg-4 col-md-6">
          <img src="assets/images/footer.png" alt="Rolsa Technologies Logo" class="img-fluid mb-4"
            style="max-width: 150px" />
          <p class="mb-4">
            Rolsa Technologies is committed to creating a sustainable future
            through innovative green energy solutions for homes and
            businesses.
          </p>
          <div class="mb-4">
            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="col-lg-2 col-md-6">
          <h3 class="fs-5 mb-4">Quick Links</h3>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="#" class="footer-link"><i class="fas fa-home"></i> Home</a></li>
            <li class="mb-2"><a href="#" class="footer-link"><i class="fas fa-info-circle"></i> About Us</a></li>
            <li class="mb-2"><a href="#" class="footer-link"><i class="fas fa-leaf"></i> Why Go Green?</a></li>
            <li class="mb-2"><a href="#" class="footer-link"><i class="fas fa-cogs"></i> Our Services</a></li>
            <li class="mb-2"><a href="#" class="footer-link"><i class="fas fa-calendar-check"></i> Book a
                Consultation</a></li>
            <li class="mb-2"><a href="#" class="footer-link"><i class="fas fa-user"></i> My Account</a></li>
          </ul>
        </div>

        <!-- Services -->
        <div class="col-lg-3 col-md-6">
          <h3 class="fs-5 mb-4">Services</h3>
          <ul class="list-unstyled">
            <li class="mb-2"><a href="#" class="footer-link"><i class="fas fa-solar-panel"></i> Solar Panels</a></li>
            <li class="mb-2"><a href="#" class="footer-link"><i class="fas fa-charging-station"></i> EV Charging
                Stations</a></li>
            <li class="mb-2"><a href="#" class="footer-link"><i class="fas fa-home-lg"></i> Smart Home Systems</a></li>
            <li class="mb-2"><a href="#" class="footer-link"><i class="fas fa-tools"></i> Maintenance</a></li>
            <li class="mb-2"><a href="#" class="footer-link"><i class="fas fa-comments"></i> Energy Consultation</a>
            </li>
          </ul>
        </div>

        <!-- Contact Us -->
        <div class="col-lg-3 col-md-6">
          <h3 class="fs-5 mb-4">Contact Us</h3>
          <ul class="list-unstyled">
            <li class="mb-3 d-flex">
              <i class="fas fa-map-marker-alt text-yellow-rolsa me-3 mt-1"></i>
              <span>123 Green Street, Eco City, EC 12345</span>
            </li>
            <li class="mb-3 d-flex">
              <i class="fas fa-phone-alt text-yellow-rolsa me-3 mt-1"></i>
              <span>(555) 123-4567</span>
            </li>
            <li class="mb-3 d-flex">
              <i class="fas fa-envelope text-yellow-rolsa me-3 mt-1"></i>
              <span>info@rolsatech.com</span>
            </li>
            <li class="mb-3 d-flex">
              <i class="fas fa-clock text-yellow-rolsa me-3 mt-1"></i>
              <span>Mon-Fri: 9am-6pm, Sat: 10am-4pm</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Copyright -->
      <div class="border-top border-secondary mt-5 pt-4 text-center text-muted">
        <small>© 2025 Rolsa Technologies. All rights reserved.</small>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
include '../../config.php';
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Function to get a setting value
function getSettingValue($mysqli, $setting) {
    $query = $mysqli->prepare("SELECT value FROM tbl_appconfig WHERE setting = ?");
    $query->bind_param("s", $setting);
    $query->execute();
    $result = $query->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['value'];
    }
    return '';
}

// Fetch hotspot title and description from tbl_appconfig
$hotspotTitle = getSettingValue($mysqli, 'hotspot_title');
$description = getSettingValue($mysqli, 'description');

// Fetch settings
$settings = [];
$settings['frequently_asked_questions_headline1'] = getSettingValue($mysqli, 'frequently_asked_questions_headline1');
$settings['frequently_asked_questions_answer1'] = getSettingValue($mysqli, 'frequently_asked_questions_answer1');
$settings['frequently_asked_questions_headline2'] = getSettingValue($mysqli, 'frequently_asked_questions_headline2');
$settings['frequently_asked_questions_answer2'] = getSettingValue($mysqli, 'frequently_asked_questions_answer2');
$settings['frequently_asked_questions_headline3'] = getSettingValue($mysqli, 'frequently_asked_questions_headline3');
$settings['frequently_asked_questions_answer3'] = getSettingValue($mysqli, 'frequently_asked_questions_answer3');

// Fetch router name and router ID from tbl_appconfig
$routerName = getSettingValue($mysqli, 'router_name');
$routerId = getSettingValue($mysqli, 'router_id');

// Fetch available plans
$planQuery = "SELECT id, name_plan, price, validity, validity_unit FROM tbl_plans WHERE routers = ? AND type = 'Hotspot'";
$planStmt = $mysqli->prepare($planQuery);
$planStmt->bind_param("s", $routerName);
$planStmt->execute();
$planResult = $planStmt->get_result();

// Prepare HTML content
$htmlContent = "<!DOCTYPE html>\n";
$htmlContent .= "<html lang=\"en\">\n";
$htmlContent .= "<head>\n";
$htmlContent .= "    <meta charset=\"utf-8\">\n";
$htmlContent .= "    <meta content=\"width=device-width, initial-scale=1.0\" name=\"viewport\">\n";
$htmlContent .= "    <title>" . htmlspecialchars($hotspotTitle) . " Hotspot Template - Index</title>\n";
$htmlContent .= "    <meta content=\"\" name=\"description\">\n";
$htmlContent .= "    <meta content=\"\" name=\"keywords\">\n";
$htmlContent .= "    <link href=\"assets/img/favicon.png\" rel=\"icon\">\n";
$htmlContent .= "    <link href=\"assets/img/apple-touch-icon.png\" rel=\"apple-touch-icon\">\n";
$htmlContent .= "    <link href=\"https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i\" rel=\"stylesheet\">\n";
$htmlContent .= "    <link href=\"assets/vendor/aos/aos.css\" rel=\"stylesheet\">\n";
$htmlContent .= "    <link href=\"assets/vendor/bootstrap/css/bootstrap.min.css\" rel=\"stylesheet\">\n";
$htmlContent .= "    <link href=\"assets/vendor/bootstrap-icons/bootstrap-icons.css\" rel=\"stylesheet\">\n";
$htmlContent .= "    <link href=\"assets/vendor/boxicons/css/boxicons.min.css\" rel=\"stylesheet\">\n";
$htmlContent .= "    <link href=\"assets/vendor/glightbox/css/glightbox.min.css\" rel=\"stylesheet\">\n";
$htmlContent .= "    <link href=\"assets/vendor/remixicon/remixicon.css\" rel=\"stylesheet\">\n";
$htmlContent .= "    <link href=\"assets/vendor/swiper/swiper-bundle.min.css\" rel=\"stylesheet\">\n";
$htmlContent .= "    <link href=\"assets/css/style.css\" rel=\"stylesheet\">\n";
$htmlContent .= "</head>\n";
$htmlContent .= "<body>\n";
$htmlContent .= "    <header id=\"header\" class=\"fixed-top d-flex align-items-center header-transparent\">\n";
$htmlContent .= "        <div class=\"container d-flex align-items-center justify-content-between\">\n";
$htmlContent .= "            <div class=\"logo\">\n";
$htmlContent .= "                <h1><a href=\"index.html\"><span>" . htmlspecialchars($hotspotTitle) . "</span></a></h1>\n";
$htmlContent .= "                <!-- Uncomment below if you prefer to use an image logo -->\n";
$htmlContent .= "                <!-- <a href=\"index.html\"><img src=\"assets/img/logo.png\" alt=\"\" class=\"img-fluid\"></a> -->\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </header>\n";

$htmlContent .= "<!-- ======= Hero Section ======= -->\n";
$htmlContent .= "<section id=\"hero\">\n";
$htmlContent .= "    <div class=\"container\">\n";
$htmlContent .= "        <div class=\"row justify-content-between\">\n";
$htmlContent .= "            <div class=\"col-lg-7 pt-5 pt-lg-0 order-2 order-lg-1 d-flex align-items-center\">\n";
$htmlContent .= "                <div data-aos=\"zoom-out\">\n";
$htmlContent .= "                    <h1>" . htmlspecialchars($hotspotTitle) . " Hotspot Login Page</h1>\n";
$htmlContent .= "                    <h2>" . htmlspecialchars($description) . "</h2>\n";
$htmlContent .= "                    <div class=\"text-center text-lg-start\">\n";
$htmlContent .= "                        <a href=\"" . APP_URL . "/?nux-mac=\$(mac-esc)&nux-ip=\$(ip)&nux-router=" . htmlspecialchars($routerId) . "\" class=\"btn-get-started scrollto\">Already Have an Account? Login</a>\n";
$htmlContent .= "                    </div>\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </div>\n";
$htmlContent .= "    <svg class=\"hero-waves\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" viewBox=\"0 24 150 28 \" preserveAspectRatio=\"none\">\n";
$htmlContent .= "        <defs>\n";
$htmlContent .= "            <path id=\"wave-path\" d=\"M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z\">\n";
$htmlContent .= "        </defs>\n";
$htmlContent .= "        <g class=\"wave1\">\n";
$htmlContent .= "            <use xlink:href=\"#wave-path\" x=\"50\" y=\"3\" fill=\"rgba(255,255,255, .1)\">\n";
$htmlContent .= "        </g>\n";
$htmlContent .= "        <g class=\"wave2\">\n";
$htmlContent .= "            <use xlink:href=\"#wave-path\" x=\"50\" y=\"0\" fill=\"rgba(255,255,255, .2)\">\n";
$htmlContent .= "        </g>\n";
$htmlContent .= "        <g class=\"wave3\">\n";
$htmlContent .= "            <use xlink:href=\"#wave-path\" x=\"50\" y=\"9\" fill=\"#fff\">\n";
$htmlContent .= "        </g>\n";
$htmlContent .= "    </svg>\n";
$htmlContent .= "</section>\n";
$htmlContent .= "<!-- End Hero Section -->\n";

$htmlContent .= "<!-- ======= Pricing Section ======= -->\n";
$htmlContent .= "<section id=\"pricing\" class=\"pricing\">\n";
$htmlContent .= "    <div class=\"container\">\n";
$htmlContent .= "        <div class=\"section-title\" data-aos=\"fade-up\">\n";
$htmlContent .= "            <h2>Pricing</h2>\n";
$htmlContent .= "            <p>Check our Pricing</p>\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "   <div class=\"row\" data-aos=\"fade-left\">\n";

while ($plan = $planResult->fetch_assoc()) {
    $htmlContent .= "   <div class=\"col-lg-3 col-md-6 col-sm-6 col-6\">\n";
    $htmlContent .= "       <div class=\"box featured\" data-aos=\"zoom-in\" data-aos-delay=\"200\">\n";
    $htmlContent .= "           <h3>" . htmlspecialchars($plan['name_plan']) . "</h3>\n";
    $htmlContent .= "           <h4><sup>ksh</sup>" . htmlspecialchars($plan['price']) . "<span> </span></h4>\n";
    $htmlContent .= "           <ul>\n";
    $htmlContent .= "               <li>" . htmlspecialchars($plan['validity']) . " " . htmlspecialchars($plan['validity_unit']) . " Unlimited</li>\n";
    $htmlContent .= "           </ul>\n";
    $htmlContent .= "           <div class=\"btn-wrap\">\n";
    $htmlContent .= "               <a href=\"" . APP_URL . "/connect.php/?nux-mac=\$(mac-esc)&nux-ip=\$(ip)&nux-router=" . $routerId . "&plan_id=" . $plan['id'] . "\" class=\"btn-buy\">Click Here To Connect</a>\n";
    $htmlContent .= "           </div>\n";
    $htmlContent .= "       </div>\n";
    $htmlContent .= "   </div>\n";
}

$htmlContent .= "</div>\n";
$htmlContent .= "    </div>\n";
$htmlContent .= "</section>\n";

$htmlContent .= "<!-- ======= Counts Section ======= -->\n";
$htmlContent .=   "<section id=\"counts\" class=\"counts\">\n";
$htmlContent .= "   <div class=\"container\">\n";
$htmlContent .= "      <div class=\"row\" data-aos=\"fade-up\">\n";
$htmlContent .= "         <div class=\"col-lg-3 col-md-6\">\n";
$htmlContent .= "            <div class=\"count-box\">\n";
$htmlContent .= "               <i class=\"bi bi-emoji-smile\"></i>\n";
$htmlContent .= "               <span data-purecounter-start=\"0\" data-purecounter-end=\"1523\" data-purecounter-duration=\"1\" class=\"purecounter\"></span>\n";
$htmlContent .= "               <p>Happy Clients</p>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "         </div>\n";
$htmlContent .= "         <div class=\"col-lg-3 col-md-6 mt-5 mt-md-0\">\n";
$htmlContent .= "            <div class=\"count-box\">\n";
$htmlContent .= "               <i class=\"bi bi-journal-richtext\"></i>\n";
$htmlContent .= "               <span data-purecounter-start=\"0\" data-purecounter-end=\"521\" data-purecounter-duration=\"1\" class=\"purecounter\"></span>\n";
$htmlContent .= "               <p>Hotspots Deployed</p>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "         </div>\n";
$htmlContent .= "         <div class=\"col-lg-3 col-md-6 mt-5 mt-lg-0\">\n";
$htmlContent .= "            <div class=\"count-box\">\n";
$htmlContent .= "               <i class=\"bi bi-headset\"></i>\n";
$htmlContent .= "               <span data-purecounter-start=\"0\" data-purecounter-end=\"1463\" data-purecounter-duration=\"1\" class=\"purecounter\"></span>\n";
$htmlContent .= "               <p>Hours Of Support</p>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "         </div>\n";
$htmlContent .= "         <div class=\"col-lg-3 col-md-6 mt-5 mt-lg-0\">\n";
$htmlContent .= "            <div class=\"count-box\">\n";
$htmlContent .= "               <i class=\"bi bi-people\"></i>\n";
$htmlContent .= "               <span data-purecounter-start=\"0\" data-purecounter-end=\"15\" data-purecounter-duration=\"1\" class=\"purecounter\"></span>\n";
$htmlContent .= "               <p>Support staff</p>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "         </div>\n";
$htmlContent .= "      </div>\n";
$htmlContent .= "   </div>\n";
$htmlContent .= "</section>\n";
$htmlContent .= "<!-- ======= End Counts Section ======= -->\n";
$htmlContent .= '<!-- ======= Testimonials Section ===== -->
         <section id="testimonials" class="testimonials">
            <div class="container">
               <div class="testimonials-slider swiper" data-aos="fade-up" data-aos-delay="100">
                  <div class="swiper-wrapper">
                     <div class="swiper-slide">
                        <div class="testimonial-item">
                           <img src="assets/img/testimonials/testimonials-1.jpg" class="testimonial-img" alt="">
                           <h3>Peter Omondi</h3>
                           <h4>Ceo &amp; and Business Owner</h4>
                           <p>
                              <i class="bx bxs-quote-alt-left quote-icon-left"></i>
                              I\'m thoroughly impressed with the consistent high-speed internet I\'ve received. The customer service is top-notch; they\'re always prompt and helpful.Definitely recommend for anyone needing reliable and fast internet service
                              <i class="bx bxs-quote-alt-right quote-icon-right"></i>
                           </p>
                        </div>
                     </div>
                     <!-- End testimonial item -->
                     <div class="swiper-slide">
                        <div class="testimonial-item">
                           <img src="assets/img/testimonials/testimonials-2.jpg" class="testimonial-img" alt="">
                           <h3>Brian Musyoka</h3>
                           <h4>Graphic Designer</h4>
                           <p>
                              <i class="bx bxs-quote-alt-left quote-icon-left"></i>
                              I switched to this service based on a friend\'s recommendation and haven\'t looked back since.
                              <i class="bx bxs-quote-alt-right quote-icon-right"></i>
                           </p>
                        </div>
                     </div>
                     <!-- End testimonial item -->
                     <div class="swiper-slide">
                        <div class="testimonial-item">
                           <img src="assets/img/testimonials/testimonials-3.jpg" class="testimonial-img" alt="">
                           <h3>Miriam</h3>
                           <h4>Store Owner</h4>
                           <p>
                              <i class="bx bxs-quote-alt-left quote-icon-left"></i>
                              Im impressed with the reliability of the internet connection. No more unexpected disconnections during important video calls. The customer service is also very responsive and helpful, making it a great overall experience.
                              <i class="bx bxs-quote-alt-right quote-icon-right"></i>
                           </p>
                        </div>
                     </div>
                     <!-- End testimonial item -->
                     <div class="swiper-slide">
                        <div class="testimonial-item">
                           <img src="assets/img/testimonials/testimonials-4.jpg" class="testimonial-img" alt="">
                           <h3>Kiveu</h3>
                           <h4>Freelancer</h4>
                           <p>
                              <i class="bx bxs-quote-alt-left quote-icon-left"></i>
                              Being a freelancer, I rely heavily on a stable internet connection for my projects. This internet service has exceeded my expectations with its consistent high speeds and reliability
                              <i class="bx bxs-quote-alt-right quote-icon-right"></i>
                           </p>
                        </div>
                     </div>
                     <!-- End testimonial item -->
                     <div class="swiper-slide">
                        <div class="testimonial-item">
                           <img src="assets/img/testimonials/testimonials-5.jpg" class="testimonial-img" alt="">
                           <h3>John</h3>
                           <h4>Entrepreneur</h4>
                           <p>
                              <i class="bx bxs-quote-alt-left quote-icon-left"></i>
                              As an entrepreneur running an online business, reliable internet is crucial. This service has provided me with uninterrupted connectivity, ensuring my business operations run smoothly.
                              <i class="bx bxs-quote-alt-right quote-icon-right"></i>
                           </p>
                        </div>
                     </div>
                     <!-- End testimonial item -->
                  </div>
                  <div class="swiper-pagination"></div>
               </div>
            </div>
         </section>
         <!-- End Testimonials Section -->';
         $htmlContent .= "<!-- ======= F.A.Q Section ======= -->\n";
         $htmlContent .= "<section id=\"faq\" class=\"faq section-bg\">\n";
         $htmlContent .= "    <div class=\"container\">\n";
         $htmlContent .= "        <div class=\"section-title\" data-aos=\"fade-up\">\n";
         $htmlContent .= "            <h2>F.A.Q</h2>\n";
         $htmlContent .= "            <p>Frequently Asked Questions</p>\n";
         $htmlContent .= "        </div>\n";
         $htmlContent .= "        <div class=\"faq-list\">\n";
         $htmlContent .= "            <ul>\n";
         $htmlContent .= "                <li data-aos=\"fade-up\">\n";
         $htmlContent .= "                    <i class=\"bx bx-help-circle icon-help\"></i> <a data-bs-toggle=\"collapse\" class=\"collapse\" data-bs-target=\"#faq-list-1\">" . htmlspecialchars($settings['frequently_asked_questions_headline1']) . " <i class=\"bx bx-chevron-down icon-show\"></i><i class=\"bx bx-chevron-up icon-close\"></i></a>\n";
         $htmlContent .= "                    <div id=\"faq-list-1\" class=\"collapse show\" data-bs-parent=\".faq-list\">\n";
         $htmlContent .= "                        <p>" . htmlspecialchars($settings['frequently_asked_questions_answer1']) . "</p>\n";
         $htmlContent .= "                    </div>\n";
         $htmlContent .= "                </li>\n";
         $htmlContent .= "                <li data-aos=\"fade-up\" data-aos-delay=\"100\">\n";
         $htmlContent .= "                    <i class=\"bx bx-help-circle icon-help\"></i> <a data-bs-toggle=\"collapse\" data-bs-target=\"#faq-list-2\" class=\"collapsed\">" . htmlspecialchars($settings['frequently_asked_questions_headline2']) . " <i class=\"bx bx-chevron-down icon-show\"></i><i class=\"bx bx-chevron-up icon-close\"></i></a>\n";
         $htmlContent .= "                    <div id=\"faq-list-2\" class=\"collapse\" data-bs-parent=\".faq-list\">\n";
         $htmlContent .= "                        <p>" . htmlspecialchars($settings['frequently_asked_questions_answer2']) . "</p>\n";
         $htmlContent .= "                    </div>\n";
         $htmlContent .= "                </li>\n";
         $htmlContent .= "                <li data-aos=\"fade-up\" data-aos-delay=\"200\">\n";
         $htmlContent .= "                    <i class=\"bx bx-help-circle icon-help\"></i> <a data-bs-toggle=\"collapse\" data-bs-target=\"#faq-list-3\" class=\"collapsed\">" . htmlspecialchars($settings['frequently_asked_questions_headline3']) . " <i class=\"bx bx-chevron-down icon-show\"></i><i class=\"bx bx-chevron-up icon-close\"></i></a>\n";
         $htmlContent .= "                    <div id=\"faq-list-3\" class=\"collapse\" data-bs-parent=\".faq-list\">\n";
         $htmlContent .= "                        <p>" . htmlspecialchars($settings['frequently_asked_questions_answer3']) . "</p>\n";
         $htmlContent .= "                    </div>\n";
         $htmlContent .= "                </li>\n";
         $htmlContent .= "            </ul>\n";
         $htmlContent .= "        </div>\n";
         $htmlContent .= "    </div>\n";
         $htmlContent .= "</section>\n";
         $htmlContent .= "<!-- End F.A.Q Section -->\n";
         $htmlContent .= "</main>\n";
$htmlContent .= "<!-- End #main -->\n";
$htmlContent .= "<a href=\"#\" class=\"back-to-top d-flex align-items-center justify-content-center\"><i class=\"bi bi-arrow-up-short\"></i></a>\n";
$htmlContent .= "<div id=\"preloader\"></div>\n";
$htmlContent .= "<!-- Vendor JS Files -->\n";
$htmlContent .= "<script src=\"assets/vendor/purecounter/purecounter_vanilla.js\"></script>\n";
$htmlContent .= "<script src=\"assets/vendor/aos/aos.js\"></script>\n";
$htmlContent .= "<script src=\"assets/vendor/bootstrap/js/bootstrap.bundle.min.js\"></script>\n";
$htmlContent .= "<script src=\"assets/vendor/glightbox/js/glightbox.min.js\"></script>\n";
$htmlContent .= "<script src=\"assets/vendor/swiper/swiper-bundle.min.js\"></script>\n";
$htmlContent .= "<script src=\"assets/vendor/php-email-form/validate.js\"></script>\n";
$htmlContent .= "<!-- Template Main JS File -->\n";
$htmlContent .= "<script src=\"assets/js/main.js\"></script>\n";
$htmlContent .= "</body>\n";
$htmlContent .= "</html>\n";
         
// ... rest of your existing code to build the page ...
$planStmt->close();
$mysqli->close();
// Check if the download parameter is set
if (isset($_GET['download']) && $_GET['download'] == '1') {
   // Prepare the HTML content for download
   // ... build your HTML content ...

   // Specify the filename for the download
   $filename = "login.html";

   // Send headers to force download
   header('Content-Type: application/octet-stream');
   header('Content-Disposition: attachment; filename='.basename($filename));
   header('Expires: 0');
   header('Cache-Control: must-revalidate');
   header('Pragma: public');
   header('Content-Length: ' . strlen($htmlContent));

   // Output the content
   echo $htmlContent;

   // Prevent any further output
   exit;
}

// Regular page content goes here
// ... HTML and PHP code to display the page ...



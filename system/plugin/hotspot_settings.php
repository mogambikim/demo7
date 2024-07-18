<?php
use PEAR2\Net\RouterOS;
register_menu("Hotspot Settings", true, "hotspot_settings", 'AFTER_SETTINGS', 'ion ion-earth');

$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);

function hotspot_settings() {
    global $ui, $conn;
    _admin();
    $ui->assign('_title', 'Hotspot Dashboard');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);

    // Get the selected router ID from user input
    $routerId = isset($_POST['router_id']) ? trim($_POST['router_id']) : '';

    if (!empty($routerId)) {
        // Update router_id in tbl_appconfig
        $updateRouterIdStmt = $conn->prepare("UPDATE tbl_appconfig SET value = :router_id WHERE setting = 'router_id'");
        $updateRouterIdStmt->execute(['router_id' => $routerId]);

        // Fetch the router name based on the selected router ID
        $routerStmt = $conn->prepare("SELECT name FROM tbl_routers WHERE id = :router_id");
        $routerStmt->execute(['router_id' => $routerId]);
        $router = $routerStmt->fetch(PDO::FETCH_ASSOC);

        if ($router) {
            // Update router_name in tbl_appconfig
            $updateRouterNameStmt = $conn->prepare("UPDATE tbl_appconfig SET value = :router_name WHERE setting = 'router_name'");
            $updateRouterNameStmt->execute(['router_name' => $router['name']]);
        }
    }

    // Fetch the current router ID from the tbl_appconfig table
    $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'router_id'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $routerId = $result ? $result['value'] : '';

    // Fetch the router details from the tbl_routers table based on the router ID
    $stmt = $conn->prepare("SELECT ip_address, username, password FROM tbl_routers WHERE id = :router_id");
    $stmt->bindParam(':router_id', $routerId);
    $stmt->execute();
    $routerDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($routerDetails) {
        $mikrotik_host = $routerDetails['ip_address'];
        $mikrotik_user = $routerDetails['username'];
        $mikrotik_pass = $routerDetails['password'];
    } else {
        // Fallback to default values or handle the case where router details are not found
        $mikrotik_host = '192.168.88.1';
        $mikrotik_user = 'admin';
        $mikrotik_pass = '12345';
    }

    // Explicitly stated values for FAQ settings
    $settings = [];
    $faqSettings = [
        'frequently_asked_questions_headline1',
        'frequently_asked_questions_answer1',
        'frequently_asked_questions_headline2',
        'frequently_asked_questions_answer2',
        'frequently_asked_questions_headline3',
        'frequently_asked_questions_answer3'
    ];

    foreach ($faqSettings as $setting) {
        $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = :setting");
        $stmt->bindParam(':setting', $setting);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $settings[$setting] = $result ? $result['value'] : '';
    }

    // Fetch other settings
    $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'hotspot_title'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $hotspotTitle = $result ? $result['value'] : '';

    $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'description'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $description = $result ? $result['value'] : '';

    $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'phone'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $phone = $result ? $result['value'] : '';

    $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'CompanyName'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $company = $result ? $result['value'] : '';

    // Fetch color scheme
    $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'color_scheme'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $selectedColorScheme = $result ? $result['value'] : 'green';

    $colorSchemes = [
        'green' => [
            'primary' => 'green',
            'secondary' => 'teal',
        ],
        'brown' => [
            'primary' => 'yellow',
            'secondary' => 'orange',
        ],
        'orange' => [
            'primary' => 'orange',
            'secondary' => 'yellow',
        ],
        'red' => [
            'primary' => 'red',
            'secondary' => 'pink',
        ],
        'blue' => [
            'primary' => 'blue',
            'secondary' => 'indigo',
        ],
        'black' => [
            'primary' => 'black',
            'secondary' => 'gray',
        ],
        'yellow' => [
            'primary' => 'yellow',
            'secondary' => 'red',
        ],
        'pink' => [
            'primary' => 'pink',
            'secondary' => 'fuchsia',
        ],
    ];

    $primaryColor = $colorSchemes[$selectedColorScheme]['primary'];
    $secondaryColor = $colorSchemes[$selectedColorScheme]['secondary'];

    // Fetch available plans
    $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'router_name'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $routerName = $result ? $result['value'] : '';

    $planQuery = "SELECT id, name_plan, price, validity, validity_unit FROM tbl_plans WHERE routers = :router_name AND type = 'Hotspot'";
    $planStmt = $conn->prepare($planQuery);
    $planStmt->bindValue(':router_name', $routerName);
    $planStmt->execute();
    $planResult = $planStmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update settings
        $newHotspotTitle = isset($_POST['hotspot_title']) ? trim($_POST['hotspot_title']) : $hotspotTitle;
        $newColorScheme = isset($_POST['color_scheme']) ? $_POST['color_scheme'] : $selectedColorScheme;
        $newFaqHeadline1 = isset($_POST['frequently_asked_questions_headline1']) ? trim($_POST['frequently_asked_questions_headline1']) : $settings['frequently_asked_questions_headline1'];
        $newFaqHeadline2 = isset($_POST['frequently_asked_questions_headline2']) ? trim($_POST['frequently_asked_questions_headline2']) : $settings['frequently_asked_questions_headline2'];
        $newFaqHeadline3 = isset($_POST['frequently_asked_questions_headline3']) ? trim($_POST['frequently_asked_questions_headline3']) : $settings['frequently_asked_questions_headline3'];
        $newFaqAnswer1 = isset($_POST['frequently_asked_questions_answer1']) ? trim($_POST['frequently_asked_questions_answer1']) : $settings['frequently_asked_questions_answer1'];
        $newFaqAnswer2 = isset($_POST['frequently_asked_questions_answer2']) ? trim($_POST['frequently_asked_questions_answer2']) : $settings['frequently_asked_questions_answer2'];
        $newFaqAnswer3 = isset($_POST['frequently_asked_questions_answer3']) ? trim($_POST['frequently_asked_questions_answer3']) : $settings['frequently_asked_questions_answer3'];
        $newDescription = isset($_POST['description']) ? trim($_POST['description']) : $description;

        // Update database
        $updateStmt = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'hotspot_title'");
        $updateStmt->execute([$newHotspotTitle]);

        $updateColorSchemeStmt = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'color_scheme'");
        $updateColorSchemeStmt->execute([$newColorScheme]);

        $updateFaqStmt1 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'frequently_asked_questions_headline1'");
        $updateFaqStmt1->execute([$newFaqHeadline1]);

        $updateFaqStmt2 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'frequently_asked_questions_headline2'");
        $updateFaqStmt2->execute([$newFaqHeadline2]);

        $updateFaqStmt3 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'frequently_asked_questions_headline3'");
        $updateFaqStmt3->execute([$newFaqHeadline3]);

        $updateFaqAnswerStmt1 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'frequently_asked_questions_answer1'");
        $updateFaqAnswerStmt1->execute([$newFaqAnswer1]);

        $updateFaqAnswerStmt2 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'frequently_asked_questions_answer2'");
        $updateFaqAnswerStmt2->execute([$newFaqAnswer2]);

        $updateFaqAnswerStmt3 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'frequently_asked_questions_answer3'");
        $updateFaqAnswerStmt3->execute([$newFaqAnswer3]);

        $updateDescriptionStmt = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'description'");
        $updateDescriptionStmt->execute([$newDescription]);

        // Use updated values
        $hotspotTitle = $newHotspotTitle;
        $selectedColorScheme = $newColorScheme;
        $settings['frequently_asked_questions_headline1'] = $newFaqHeadline1;
        $settings['frequently_asked_questions_headline2'] = $newFaqHeadline2;
        $settings['frequently_asked_questions_headline3'] = $newFaqHeadline3;
        $settings['frequently_asked_questions_answer1'] = $newFaqAnswer1;
        $settings['frequently_asked_questions_answer2'] = $newFaqAnswer2;
        $settings['frequently_asked_questions_answer3'] = $newFaqAnswer3;
        $description = $newDescription;

        $primaryColor = $colorSchemes[$selectedColorScheme]['primary'];
        $secondaryColor = $colorSchemes[$selectedColorScheme]['secondary'];

         // Initialize HTML content variable
         $htmlContent = "<!DOCTYPE html>\n";
         $htmlContent .= "<html lang=\"en\">\n";
         $htmlContent .= "<head>\n";
         $htmlContent .= " <meta charset=\"UTF-8\">\n";
         $htmlContent .= " <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
         $htmlContent .= " <title>" . htmlspecialchars($hotspotTitle) . " Hotspot Template - Index</title>\n";
         $htmlContent .= " <script src=\"https://cdn.tailwindcss.com\"></script>\n";
         $htmlContent .= " <script src=\"https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.0.18/sweetalert2.all.min.js\"></script>\n";
         $htmlContent .= " <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css\">\n";
         $htmlContent .= " <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/glider-js@1.7.7/glider.min.css\" />\n";
         $htmlContent .= " <script src=\"https://cdn.jsdelivr.net/npm/glider-js@1.7.7/glider.min.js\"></script>\n";
         $htmlContent .= " <link rel=\"preconnect\" href=\"https://cdn.jsdelivr.net\">\n";
         $htmlContent .= " <link rel=\"preconnect\" href=\"https://cdnjs.cloudflare.com\" crossorigin>\n";
         $htmlContent .= " <link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\">\n";
         $htmlContent .= "</head>\n";
         
         
         $htmlContent .= "<body class=\"font-sans antialiased text-gray-900\">\n";
         $htmlContent .= "    <!-- Sticky Header -->\n";
         $htmlContent .= "    <header class=\"bg-{$secondaryColor}-900 text-white fixed w-full z-10\">\n";
         $htmlContent .= "        <div class=\"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5\">\n";
         $htmlContent .= "            <div class=\"flex items-center justify-between h-16\">\n";
         $htmlContent .= "                <!-- Logo and title area -->\n";
         $htmlContent .= "                <div class=\"flex items-center\">\n";
         $htmlContent .= "                    <img src=\"logo.png\" alt=\"Your Company Logo\" class=\"h-8 w-8 mr-2\">\n";
         $htmlContent .= "                    <h1 class=\"text-xl font-bold\">" . htmlspecialchars($hotspotTitle) . "</h1>\n";
         $htmlContent .= "                </div>\n";
         $htmlContent .= "                <!-- Navigation Links and Contact Number -->\n";
         $htmlContent .= "                <div class=\"block\">\n";
         $htmlContent .= "                    <div class=\"ml-10 flex items-baseline space-x-4\">\n";
         $htmlContent .= "                        <a href=\"#alreadyHavePackage\" class=\"text-{$secondaryColor}-200 hover:text-white px-3 py-2 rounded-md text-sm font-medium\">Already Paid? Click Here.</a>\n";
         $htmlContent .= "                        <span class=\"text-{$secondaryColor}-200\">" . htmlspecialchars($phone) . "</span>\n";
         $htmlContent .= "                    </div>\n";
         $htmlContent .= "                </div>\n";
         $htmlContent .= "            </div>\n";
         $htmlContent .= "        </div>\n";
         $htmlContent .= "    </header>\n";
         
         
         
         $htmlContent .= "    <!-- Main content -->\n";
         $htmlContent .= "    <main class=\"pt-24\">\n";
         $htmlContent .= "        <section class=\"bg-white\">\n";
         $htmlContent .= "            <div class=\"max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8\">\n";
         $htmlContent .= "                <h2 class=\"text-3xl font-extrabold text-gray-900 mb-6\">" . htmlspecialchars($description) . "</h2>\n";
         $htmlContent .= "                <!-- Pricing Section -->\n";
         $htmlContent .= "                <div class=\"mt-10\">\n";
         $htmlContent .= "                    <div class=\"text-center\">\n";
         $htmlContent .= "                        <h3 class=\"text-2xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-3xl sm:leading-9\">\n";
         $htmlContent .= "                            CHECK OUR PRICING\n";
         $htmlContent .= "                        </h3>\n";
         $htmlContent .= "                        <p class=\"mt-4 max-w-2xl text-xl leading-7 text-gray-500 lg:mx-auto\">\n";
         $htmlContent .= "                            Choose the plan that fits your needs.\n";
         $htmlContent .= "                        </p>\n";
         $htmlContent .= "                    </div>\n";
         $htmlContent .= "                </div>\n";
         $htmlContent .= "            </div>\n";
         $htmlContent .= "        </section>\n";
         $htmlContent .= "    </main>\n";
         
         
         
         $htmlContent .= "<div class=\"mt-10 max-w-7xl mx-auto grid grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-5\">\n";
         
         foreach ($planResult as $plan) {
         
             $htmlContent .= "    <div class=\"flex flex-col rounded-lg shadow-xl overflow-hidden transform transition duration-500 hover:scale-105\">\n";
             $htmlContent .= "        <div class=\"px-4 py-5 bg-gradient-to-tr from-{$primaryColor}-50 to-{$primaryColor}-200 text-center\">\n";
             $htmlContent .= "            <span class=\"inline-flex px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase bg-{$primaryColor}-800 text-{$primaryColor}-50\">\n";
             $htmlContent .=                  htmlspecialchars($plan['name_plan']) . "\n";
             $htmlContent .= "            </span>\n";
             $htmlContent .= "            <div class=\"mt-4 text-4xl leading-none font-extrabold text-{$primaryColor}-800\">\n";
             $htmlContent .= "                <span class=\"text-lg font-medium text-{$primaryColor}-600\">ksh</span>\n";
             $htmlContent .=                  htmlspecialchars($plan['price']) . "\n";
             $htmlContent .= "            </div>\n";
             $htmlContent .= "            <p class=\"mt-2 text-md leading-5 text-{$primaryColor}-700 text-center\">\n";
             $htmlContent .=                  htmlspecialchars($plan['validity']) . " " . htmlspecialchars($plan['validity_unit']) . " Unlimited\n";
             $htmlContent .= "            </p>\n";
             $htmlContent .= "        </div>\n";
             $htmlContent .= "        <div class=\"px-4 pt-4 pb-6 bg-{$primaryColor}-500 text-center\">\n";
             $htmlContent .= "            <a href=\"#\" class=\"inline-block text-{$primaryColor}-800 bg-{$primaryColor}-50 hover:bg-{$primaryColor}-100 focus:outline-none focus:ring-4 focus:ring-{$primaryColor}-500 focus:ring-opacity-50 transform transition duration-150 ease-in-out rounded-lg font-semibold px-3 py-2 text-xs shadow-lg cursor-pointer\"\n";
             $htmlContent .= "               onclick=\"handlePhoneNumberSubmission(this.getAttribute('data-plan-id'), this.getAttribute('data-router-id')); return false;\" data-plan-id=\"" . $plan['id'] . "\" data-router-id=\"" . $routerId . "\">\n";
             $htmlContent .= "                Click Here To Connect\n";
             $htmlContent .= "            </a>\n";
             $htmlContent .= "        </div>\n";
             $htmlContent .= "    </div>\n";
         }
         
         $htmlContent .= "</div>\n";
         
         
         $htmlContent .= "<div id=\"alreadyHavePackage\" class=\"container mx-auto px-4\">\n";
         $htmlContent .= "    <div class=\"max-w-md mx-auto bg-white rounded-lg overflow-hidden md:max-w-lg\">\n";
         $htmlContent .= "        <div class=\"md:flex\">\n";
         $htmlContent .= "            <div class=\"w-full p-5\">\n";
         $htmlContent .= "                <div class=\"text-center\">\n";
         $htmlContent .= "                    <h3 class=\"text-2xl text-gray-900\">Already Have an Active Package?</h3>\n";
         $htmlContent .= "                </div>\n";
         $htmlContent .= "                <form id=\"loginForm\" class=\"form\" name=\"login\" action=\"$(link-login-only)\" method=\"post\" $(if chap-id)onSubmit=\"return doLogin()\"$(endif)>\n";
         $htmlContent .= "                    <input type=\"hidden\" name=\"dst\" value=\"$(link-orig)\" />\n";
         $htmlContent .= "                    <input type=\"hidden\" name=\"popup\" value=\"true\" />\n";
         $htmlContent .= "                    <div class=\"mb-4\">\n";
         $htmlContent .= "                        <label class=\"block text-gray-700 text-sm font-bold mb-2\" for=\"username\">Username</label>\n";
         $htmlContent .= "                        <input id=\"usernameInput\" class=\"shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline\" name=\"username\" type=\"text\" value=\"\" placeholder=\"Username\">\n";
         $htmlContent .= "                    </div>\n";
         $htmlContent .= "                    <div class=\"mb-6\">\n";
         $htmlContent .= "                        <label class=\"block text-gray-700 text-sm font-bold mb-2\" for=\"password\">Password</label>\n";
         $htmlContent .= "                        <input id=\"passwordInput\" class=\"shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline\" name=\"password\" type=\"password\" placeholder=\"******************\">\n";
         $htmlContent .= "                    </div>\n";
         $htmlContent .= "                    <div class=\"flex items-center justify-between\">\n";
         $htmlContent .= "                        <button id=\"submitBtn\" class=\"bg-{$secondaryColor}-500 hover:bg-{$secondaryColor}-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline\" type=\"button\">\n";
         $htmlContent .= "                            Click Here To Connect\n";
         $htmlContent .= "                        </button>\n";
         $htmlContent .= "                    </div>\n";
         $htmlContent .= "                </form>\n";
         $htmlContent .= "            </div>\n";
         $htmlContent .= "        </div>\n";
         $htmlContent .= "    </div>\n";
         $htmlContent .= "</div>\n";
         
         $htmlContent .= "<div class=\"mt-10 text-center\">\n";
         $htmlContent .= "    <a href=\"" . APP_URL . "/index.php?_route=login\" class=\"bg-{$secondaryColor}-500 hover:bg-{$secondaryColor}-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline\">\n";
         $htmlContent .= "        Have a voucher code? Click here\n";
         $htmlContent .= "    </a>\n";
         $htmlContent .= "</div>\n";
         
         
         
         
         
         $htmlContent .= "<!-- Testimonials Section -->\n";
         $htmlContent .= "<div class=\"mt-10 mx-auto px-4 sm:px-6 lg:px-8\">\n";
         $htmlContent .= "    <h3 class=\"text-center text-2xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-3xl sm:leading-9\">\n";
         $htmlContent .= "        What Our Users Say\n";
         $htmlContent .= "    </h3>\n";
         $htmlContent .= "    <div class=\"glider-contain mt-6\">\n";
         $htmlContent .= "        <div class=\"glider\">\n";
         // Testimonial 1
         $htmlContent .= "            <div class=\"bg-white rounded-lg shadow-md overflow-hidden\">\n";
         $htmlContent .= "                <img class=\"w-full h-48 object-cover object-center\" src=\"assets/img/testimonials/testimonials-3.jpg\" alt=\"Testimonial from Otieno Peter\">\n";
         $htmlContent .= "                <div class=\"p-4\">\n";
         $htmlContent .= "                    <div class=\"uppercase tracking-wide text-sm text-indigo-500 font-semibold\">Otieno Peter</div>\n";
         $htmlContent .= "                    <p class=\"mt-2 text-gray-500\">\"Switching to this service has been a game changer for me. The connection is reliable and fast, making my online work seamless and efficient.\"</p>\n";
         $htmlContent .= "                </div>\n";
         $htmlContent .= "            </div>\n";
         // Testimonial 2
         $htmlContent .= "            <div class=\"bg-white rounded-lg shadow-md overflow-hidden\">\n";
         $htmlContent .= "                <img class=\"w-full h-48 object-cover object-center\" src=\"assets/img/testimonials/testimonials-2.jpg\" alt=\"Testimonial from Kiveu\">\n";
         $htmlContent .= "                <div class=\"p-4\">\n";
         $htmlContent .= "                    <div class=\"uppercase tracking-wide text-sm text-indigo-500 font-semibold\">Kiveu</div>\n";
         $htmlContent .= "                    <p class=\"mt-2 text-gray-500\">\"I've experienced unparalleled support and service. The team goes above and beyond to ensure customer satisfaction. Highly recommend!\"</p>\n";
         $htmlContent .= "                </div>\n";
         $htmlContent .= "            </div>\n";
         // Testimonial 3
         $htmlContent .= "            <div class=\"bg-white rounded-lg shadow-md overflow-hidden\">\n";
         $htmlContent .= "                <img class=\"w-full h-48 object-cover object-center\" src=\"assets/img/testimonials/testimonials-1.jpg\" alt=\"Testimonial from Anonymous User\">\n";
         $htmlContent .= "                <div class=\"p-4\">\n";
         $htmlContent .= "                    <div class=\"uppercase tracking-wide text-sm text-indigo-500 font-semibold\">Anonymous User</div>\n";
         $htmlContent .= "                    <p class=\"mt-2 text-gray-500\">\"Their commitment to quality and speed is evident. My internet experience has been fantastic ever since I made the switch.\"</p>\n";
         $htmlContent .= "                </div>\n";
         $htmlContent .= "            </div>\n";
         $htmlContent .= "        </div>\n";
         $htmlContent .= "        <!-- Add Arrows -->\n";
         $htmlContent .= "        <button aria-label=\"Previous\" class=\"glider-prev\">«</button>\n";
         $htmlContent .= "        <button aria-label=\"Next\" class=\"glider-next\">»</button>\n";
         $htmlContent .= "        <div role=\"tablist\" class=\"dots\"></div>\n";
         $htmlContent .= "    </div>\n";
         $htmlContent .= "</div>\n";
         
         // Glider.js script for the Testimonials Section
         $htmlContent .= "<script>\n";
         $htmlContent .= "    new Glider(document.querySelector('.glider'), {\n";
         $htmlContent .= "        slidesToShow: 1,\n";
         $htmlContent .= "        slidesToScroll: 1,\n";
         $htmlContent .= "        draggable: true,\n";
         $htmlContent .= "        dots: '.dots',\n";
         $htmlContent .= "        arrows: {\n";
         $htmlContent .= "            prev: '.glider-prev',\n";
         $htmlContent .= "            next: '.glider-next'\n";
         $htmlContent .= "        },\n";
         $htmlContent .= "        responsive: [\n";
         $htmlContent .= "            {\n";
         $htmlContent .= "                breakpoint: 775,\n";
         $htmlContent .= "                settings: {\n";
         $htmlContent .= "                    slidesToShow: 2,\n";
         $htmlContent .= "                    slidesToScroll: 2,\n";
         $htmlContent .= "                }\n";
         $htmlContent .= "            },\n";
         $htmlContent .= "            {\n";
         $htmlContent .= "                breakpoint: 1024,\n";
         $htmlContent .= "                settings: {\n";
         $htmlContent .= "                    slidesToShow: 3,\n";
         $htmlContent .= "                    slidesToScroll: 3,\n";
         $htmlContent .= "                }\n";
         $htmlContent .= "            }\n";
         $htmlContent .= "        ]\n";
         $htmlContent .= "    });\n";
         $htmlContent .= "</script>\n";
         
         
         
         
         $htmlContent .= "<!-- FAQ Section -->\n";
         $htmlContent .= "<div class=\"mt-10 mx-auto px-4 sm:px-6 lg:px-8\">\n";
         $htmlContent .= "    <div class=\"text-center\">\n";
         $htmlContent .= "        <h3 class=\"text-2xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-3xl sm:leading-9\">\n";
         $htmlContent .= "        FREQUENTLY ASKED QUESTIONS Will Be Here\n";
         $htmlContent .= "        </h3>\n";
         $htmlContent .= "        <p class=\"mt-4 max-w-2xl text-xl leading-7 text-gray-500 lg:mx-auto\">\n";
         $htmlContent .= "            Everything you need to know before getting started.\n";
         $htmlContent .= "        </p>\n";
         $htmlContent .= "    </div>\n";
         $htmlContent .= "    <div class=\"mt-6\">\n";
         $htmlContent .= "        <dl class=\"space-y-6\">\n";
         
         // FAQ 1
         $htmlContent .= "            <div class=\"bg-white rounded-lg shadow-md\">\n";
         $htmlContent .= "                <dt class=\"p-4 cursor-pointer text-lg leading-6 font-medium text-gray-900\" onclick=\"toggleFAQ('faq1')\">" . htmlspecialchars($settings['frequently_asked_questions_headline1']) . "</dt>\n";
         $htmlContent .= "                <dd id=\"faq1\" class=\"p-4 hidden text-base text-gray-500\">" . htmlspecialchars($settings['frequently_asked_questions_answer1']) . "</dd>\n";
         $htmlContent .= "            </div>\n";
         
         // FAQ 2
         $htmlContent .= "            <div class=\"bg-white rounded-lg shadow-md\">\n";
         $htmlContent .= "                <dt class=\"p-4 cursor-pointer text-lg leading-6 font-medium text-gray-900\" onclick=\"toggleFAQ('faq2')\">" . htmlspecialchars($settings['frequently_asked_questions_headline2']) . "</dt>\n";
         $htmlContent .= "                <dd id=\"faq2\" class=\"p-4 hidden text-base text-gray-500\">" . htmlspecialchars($settings['frequently_asked_questions_answer2']) . "</dd>\n";
         $htmlContent .= "            </div>\n";
         
         // FAQ 3
         $htmlContent .= "            <div class=\"bg-white rounded-lg shadow-md\">\n";
         $htmlContent .= "                <dt class=\"p-4 cursor-pointer text-lg leading-6 font-medium text-gray-900\" onclick=\"toggleFAQ('faq3')\">" . htmlspecialchars($settings['frequently_asked_questions_headline3']) . "</dt>\n";
         $htmlContent .= "                <dd id=\"faq3\" class=\"p-4 hidden text-base text-gray-500\">" . htmlspecialchars($settings['frequently_asked_questions_answer3']) . "</dd>\n";
         $htmlContent .= "            </div>\n";
         
         $htmlContent .= "        </dl>\n";
         $htmlContent .= "    </div>\n";
         $htmlContent .= "</div>\n";
         
         
         
         
         $htmlContent .= "<script>\n";
         $htmlContent .= "document.addEventListener('DOMContentLoaded', function() {\n";
         $htmlContent .= "    function autofillLogin() {\n";
         $htmlContent .= "        var phoneNumber = '2547xxxxxxx';\n";
         $htmlContent .= "        var password = '1234';\n";
         $htmlContent .= "        document.querySelector('input[name=\"username\"]').value = phoneNumber;\n";
         $htmlContent .= "        document.querySelector('input[name=\"password\"]').value = password;\n";
         $htmlContent .= "        setTimeout(function() {\n";
         $htmlContent .= "            document.querySelector('button[type=\"submit\"]').click();\n";
         $htmlContent .= "        }, 15000);\n";
         $htmlContent .= "    }\n";
         $htmlContent .= "    autofillLogin();\n";
         $htmlContent .= "});\n";
         $htmlContent .= "</script>\n";
         
         
         
         
         
         $htmlContent .= "<script>\n";
         $htmlContent .= "function toggleFAQ(faqId) {\n";
         $htmlContent .= "    var element = document.getElementById(faqId);\n";
         $htmlContent .= "    if (element.style.display === \"block\") {\n";
         $htmlContent .= "        element.style.display = \"none\";\n";
         $htmlContent .= "    } else {\n";
         $htmlContent .= "        element.style.display = \"block\";\n";
         $htmlContent .= "    }\n";
         $htmlContent .= "}\n";
         $htmlContent .= "</script>\n";
         
         
         
         $htmlContent .= "<form id=\"loginForm\" class=\"form\" name=\"login\" action=\"$(link-login-only)\" method=\"post\" $(if chap-id)onSubmit=\"return doLogin()\"$(endif)>\n";
         $htmlContent .= "    <input type=\"hidden\" name=\"dst\" value=\"$(link-orig)\" />\n";
         $htmlContent .= "    <input type=\"hidden\" name=\"popup\" value=\"true\" />\n";
         $htmlContent .= "    <input type=\"hidden\" name=\"mac\" value=\"$(mac)\" />\n";
         $htmlContent .= "    <!-- Rest of the form code -->\n";
         $htmlContent .= "</form>\n";
         $htmlContent .= "\n";
         $htmlContent .= "<!-- Add a container to display the MAC address -->\n";
         $htmlContent .= "<div id=\"macAddressContainer\" class=\"mt-4\">\n";
         $htmlContent .= "    <p>Your MAC Address: <span id=\"macAddressDisplay\"></span></p>\n";
         $htmlContent .= "</div>\n";
         $htmlContent .= "\n";
         $htmlContent .= "<!-- Add a script to retrieve and display the MAC address -->\n";
         $htmlContent .= "<script>\n";
         $htmlContent .= "    document.addEventListener('DOMContentLoaded', function() {\n";
         $htmlContent .= "        var macAddressInput = document.querySelector('input[name=\"mac\"]');\n";
         $htmlContent .= "        var macAddressDisplay = document.getElementById('macAddressDisplay');\n";
         $htmlContent .= "        \n";
         $htmlContent .= "        if (macAddressInput && macAddressDisplay) {\n";
         $htmlContent .= "            var macAddress = macAddressInput.value;\n";
         $htmlContent .= "            macAddressDisplay.textContent = macAddress;\n";
         $htmlContent .= "        }\n";
         $htmlContent .= "    });\n";
         $htmlContent .= "</script>\n";
         
         
         
         
         
         
         $htmlContent .= "</section>\n";
         $htmlContent .= "</main>\n";
         
         $htmlContent .= "<!-- Footer -->\n";
         $htmlContent .= "<footer class=\"bg-{$secondaryColor}-900 text-white\">\n";
         $htmlContent .= "    <div class=\"max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8\">\n";
         $htmlContent .= "        <div class=\"lg:grid lg:grid-cols-3 lg:gap-8\">\n";
         $htmlContent .= "            <div class=\"lg:col-span-1\">\n";
         $htmlContent .= "                <h2 class=\"text-sm font-semibold uppercase tracking-wider\">\n";
         $htmlContent .= "                    Contact Us\n";
         $htmlContent .= "                </h2>\n";
         $htmlContent .= "                <ul class=\"mt-4 space-y-4\">\n";
         $htmlContent .= "                    <li>\n";
         $htmlContent .= "                        <span class=\"block\">Address</span>\n";
         $htmlContent .= "                    </li>\n";
         $htmlContent .= "                    <li>\n";
         $htmlContent .= "                        <span class=\"block\">Email: contact@" . htmlspecialchars($company) . "</span>\n";
         $htmlContent .= "                    </li>\n";
         $htmlContent .= "                    <li>\n";
         $htmlContent .= "                        <span class=\"block\">Phone: " . htmlspecialchars($phone) . "</span>\n";
         $htmlContent .= "                    </li>\n";
         $htmlContent .= "                </ul>\n";
         $htmlContent .= "            </div>\n";
         
         $htmlContent .= "            <div class=\"lg:col-span-1\">\n";
         $htmlContent .= "                <h2 class=\"text-sm font-semibold uppercase tracking-wider\">\n";
         $htmlContent .= "                    Quick Links\n";
         $htmlContent .= "                </h2>\n";
         $htmlContent .= "                <ul class=\"mt-4 space-y-4\">\n";
         $htmlContent .= "                    <li><a href=\"#\" class=\"hover:underline\">About Us</a></li>\n";
         $htmlContent .= "                    <li><a href=\"#\" class=\"hover:underline\">Our Services</a></li>\n";
         $htmlContent .= "                    <li><a href=\"#\" class=\"hover:underline\">FAQ</a></li>\n";
         $htmlContent .= "                    <li><a href=\"#\" class=\"hover:underline\">Support</a></li>\n";
         $htmlContent .= "                </ul>\n";
         $htmlContent .= "            </div>\n";
         
         $htmlContent .= "            <div class=\"lg:col-span-1\">\n";
         $htmlContent .= "                <h2 class=\"text-sm font-semibold uppercase tracking-wider\">\n";
         $htmlContent .= "                     Follow Us\n";
         $htmlContent .= "                </h2>\n";
         $htmlContent .= "                <div class=\"mt-4 space-x-4\">\n";
         $htmlContent .= "                    <a href=\"#\" class=\"hover:text-gray-400\"><i class=\"fab fa-facebook-f\"></i></a>\n";
         $htmlContent .= "                    <a href=\"#\" class=\"hover:text-gray-400\"><i class=\"fab fa-twitter\"></i></a>\n";
         $htmlContent .= "                    <a href=\"#\" class=\"hover:text-gray-400\"><i class=\"fab fa-instagram\"></i></a>\n";
         $htmlContent .= "                    <a href=\"#\" class=\"hover:text-gray-400\"><i class=\"fab fa-linkedin-in\"></i></a>\n";
         $htmlContent .= "                </div>\n";
         $htmlContent .= "            </div>\n";
         $htmlContent .= "        </div>\n";
         
         $htmlContent .= "        <div class=\"mt-8 border-t border-gray-700 pt-8 md:flex md:items-center md:justify-between\">\n";
         $htmlContent .= "            <div class=\"flex space-x-6 md:order-2\">\n";
         $htmlContent .= "                <a href=\"#\" class=\"text-gray-400 hover:text-gray-300\"><span class=\"sr-only\">Facebook</span><i class=\"fab fa-facebook-f\"></i></a>\n";
         $htmlContent .= "                <a href=\"#\" class=\"text-gray-400 hover:text-gray-300\"><span class=\"sr-only\">Instagram</span><i class=\"fab fa-instagram\"></i></a>\n";
         $htmlContent .= "                <a href=\"#\" class=\"text-gray-400 hover:text-gray-300\"><span class=\"sr-only\">Twitter</span><i class=\"fab fa-twitter\"></i></a>\n";
         $htmlContent .= "                <a href=\"#\" class=\"text-gray-400 hover:text-gray-300\"><span class=\"sr-only\">LinkedIn</span><i class=\"fab fa-linkedin-in\"></i></a>\n";
         $htmlContent .= "            </div>\n";
         $htmlContent .= "<p class=\"mt-8 text-base leading-6 text-gray-400 md:mt-0 md:order-1\">\n";
         $htmlContent .= "                &copy; 2024 FreeIspRadius. All rights reserved.\n";
         $htmlContent .= "            </p>\n";
         $htmlContent .= "        </div>\n";
         $htmlContent .= "    </div>\n";
         $htmlContent .= "</footer>\n";
         
         
         
         $htmlContent .= "<script src=\"https://cdn.jsdelivr.net/npm/sweetalert2@11\"></script>\n";
         $htmlContent .= "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js\"></script>\n";
         $htmlContent .= "<script>\n";
         $htmlContent .= "    function formatPhoneNumber(phoneNumber) {\n";
         $htmlContent .= "        if (phoneNumber.startsWith('+')) {\n";
         $htmlContent .= "            phoneNumber = phoneNumber.substring(1);\n";
         $htmlContent .= "        }\n";
         $htmlContent .= "        if (phoneNumber.startsWith('0')) {\n";
         $htmlContent .= "            phoneNumber = '254' + phoneNumber.substring(1);\n";
         $htmlContent .= "        }\n";
         $htmlContent .= "        if (phoneNumber.match(/^(7|1)/)) {\n";
         $htmlContent .= "            phoneNumber = '254' + phoneNumber;\n";
         $htmlContent .= "        }\n";
         $htmlContent .= "        return phoneNumber;\n";
         $htmlContent .= "    }\n";
         $htmlContent .= "\n";
         $htmlContent .= "    function setCookie(name, value, days) {\n";
         $htmlContent .= "        var expires = \"\";\n";
         $htmlContent .= "        if (days) {\n";
         $htmlContent .= "            var date = new Date();\n";
         $htmlContent .= "            date.setTime(date.getTime() + (days*24*60*60*1000));\n";
         $htmlContent .= "            expires = \"; expires=\" + date.toUTCString();\n";
         $htmlContent .= "        }\n";
         $htmlContent .= "        document.cookie = name + \"=\" + (value || \"\")  + expires + \"; path=/\";\n";
         $htmlContent .= "    }\n";
         $htmlContent .= "\n";
         $htmlContent .= "    function getCookie(name) {\n";
         $htmlContent .= "        var nameEQ = name + \"=\";\n";
         $htmlContent .= "        var ca = document.cookie.split(';');\n";
         $htmlContent .= "        for(var i=0;i < ca.length;i++) {\n";
         $htmlContent .= "            var c = ca[i];\n";
         $htmlContent .= "            while (c.charAt(0)==' ') c = c.substring(1,c.length);\n";
         $htmlContent .= "            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);\n";
         $htmlContent .= "        }\n";
         $htmlContent .= "        return null;\n";
         $htmlContent .= "    }\n";
         $htmlContent .= "\n";
         $htmlContent .= "    function handlePhoneNumberSubmission(planId, routerId) {\n";
         $htmlContent .= "        Swal.fire({\n";
         $htmlContent .= "            title: 'Enter Your Phone Number',\n";
         $htmlContent .= "            input: 'text',\n";
         $htmlContent .= "            inputPlaceholder: 'Your phone number here',\n";
         $htmlContent .= "            inputAttributes: {\n";
         $htmlContent .= "                autocapitalize: 'off'\n";
         $htmlContent .= "            },\n";
         $htmlContent .= "            showCancelButton: true,\n";
         $htmlContent .= "            confirmButtonColor: '#3085d6',\n";
         $htmlContent .= "            cancelButtonColor: '#d33',\n";
         $htmlContent .= "            confirmButtonText: 'Submit',\n";
         $htmlContent .= "            showLoaderOnConfirm: true,\n";
         $htmlContent .= "            backdrop: `\n";
         $htmlContent .= "                rgba(0,0,123,0.4)\n";
         $htmlContent .= "                url(\"https://sweetalert2.github.io/images/nyan-cat.gif\")\n";
         $htmlContent .= "                center left\n";
         $htmlContent .= "                no-repeat\n";
         $htmlContent .= "            `,\n";
         $htmlContent .= "            preConfirm: (phoneNumber) => {\n";
         $htmlContent .= "                var formattedPhoneNumber = formatPhoneNumber(phoneNumber);\n";
         $htmlContent .= "                var macAddress = document.querySelector('input[name=\"mac\"]').value;\n";
         $htmlContent .= "                var lastFourChars = macAddress.slice(-4);\n";
         $htmlContent .= "                var username = formattedPhoneNumber + '-' + lastFourChars;\n";
         $htmlContent .= "                try {\n";
         $htmlContent .= "                    localStorage.setItem('phoneNumber', formattedPhoneNumber);\n";
         $htmlContent .= "                    localStorage.setItem('lastFourChars', lastFourChars);\n";
         $htmlContent .= "                } catch (e) {\n";
         $htmlContent .= "                    setCookie('phoneNumber', formattedPhoneNumber, 1);\n";
         $htmlContent .= "                    setCookie('lastFourChars', lastFourChars, 1);\n";
         $htmlContent .= "                }\n";
         $htmlContent .= "                document.getElementById('usernameInput').value = username;\n";
         $htmlContent .= "                console.log(\"Phone number for autofill:\", formattedPhoneNumber);\n";
         $htmlContent .= "\n";
         $htmlContent .= "                return fetch('" . APP_URL . "/index.php?_route=plugin/CreateHotspotuser&type=grant', {\n";
         $htmlContent .= "                    method: 'POST',\n";
         $htmlContent .= "                    headers: {'Content-Type': 'application/json'},\n";
         $htmlContent .= "                    body: JSON.stringify({phone_number: formattedPhoneNumber, plan_id: planId, router_id: routerId, mac_address: lastFourChars}),\n";
         $htmlContent .= "                })\n";
         $htmlContent .= "                .then(response => {\n";
         $htmlContent .= "                    if (!response.ok) throw new Error('Network response was not ok');\n";
         $htmlContent .= "                    return response.json();\n";
         $htmlContent .= "                })\n";
         $htmlContent .= "                .then(data => {\n";
         $htmlContent .= "                    if (data.status === 'error') throw new Error(data.message);\n";
         $htmlContent .= "                    \n";
         $htmlContent .= "                    // Set the expiration time for the paymentSubmitted flag (2 minutes from now)\n";
         $htmlContent .= "                    var expirationTime = new Date().getTime() + (1 * 60 * 1000);\n";
         $htmlContent .= "                    try {\n";
         $htmlContent .= "                        localStorage.setItem('paymentSubmittedExpiration', expirationTime.toString());\n";
         $htmlContent .= "                        localStorage.setItem('paymentSubmitted', 'true');\n";
         $htmlContent .= "                    } catch (e) {\n";
         $htmlContent .= "                        setCookie('paymentSubmittedExpiration', expirationTime.toString(), 1);\n";
         $htmlContent .= "                        setCookie('paymentSubmitted', 'true', 1);\n";
         $htmlContent .= "                    }\n";
         $htmlContent .= "                    \n";
         $htmlContent .= "                    // Start confirming payment every three seconds\n";
         $htmlContent .= "                    startConfirmingPayment();\n";
         $htmlContent .= "                    \n";
         $htmlContent .= "                    return formattedPhoneNumber;\n";
         $htmlContent .= "                })\n";
         $htmlContent .= "                .catch(error => {\n";
         $htmlContent .= "                    Swal.fire({\n";
         $htmlContent .= "                        icon: 'error',\n";
         $htmlContent .= "                        title: 'Oops...',\n";
         $htmlContent .= "                        text: error.message,\n";
         $htmlContent .= "                    });\n";
         $htmlContent .= "                });\n";
         $htmlContent .= "            },\n";
         $htmlContent .= "            allowOutsideClick: () => !Swal.isLoading()\n";
         $htmlContent .= "        });\n";
         $htmlContent .= "    }\n";
         $htmlContent .= "\n";
         $htmlContent .= "    function startConfirmingPayment() {\n";
         $htmlContent .= "        var loginInterval = setInterval(function() {\n";
         $htmlContent .= "            Swal.fire({\n";
         $htmlContent .= "                title: 'Confirming Payment',\n";
         $htmlContent .= "                html: 'Please wait while we confirm your payment and log you in...',\n";
         $htmlContent .= "                allowOutsideClick: false,\n";
         $htmlContent .= "                allowEscapeKey: false,\n";
         $htmlContent .= "                allowEnterKey: false,\n";
         $htmlContent .= "                showConfirmButton: false,\n";
         $htmlContent .= "                onBeforeOpen: () => {\n";
         $htmlContent .= "                    Swal.showLoading();\n";
         $htmlContent .= "                }\n";
         $htmlContent .= "            });\n";
         $htmlContent .= "            \n";
         $htmlContent .= "            // Attempt to log in by clicking the submit button\n";
         $htmlContent .= "            document.getElementById('submitBtn').click();\n";
         $htmlContent .= "        }, 3000);\n";
         $htmlContent .= "    }\n";
         $htmlContent .= "\n";
         $htmlContent .= "    function refreshData() {\n";
         $htmlContent .= "        function refreshDataInternal() {\n";
         $htmlContent .= "            var usernameInput = document.getElementById('usernameInput');\n";
         $htmlContent .= "            if (usernameInput.value) {\n";
         $htmlContent .= "                var phoneNumber = usernameInput.value.split('-')[0];\n";
         $htmlContent .= "                $.ajax({\n";
         $htmlContent .= "                    url: '" . APP_URL . "/index.php?_route=plugin/CreateHotspotuser&type=verify',\n";
         $htmlContent .= "                    method: \"POST\",\n";
         $htmlContent .= "                    data: {phone_number: phoneNumber},\n";
         $htmlContent .= "                    dataType: \"json\",\n";
         $htmlContent .= "                    success: function(data) {\n";
         $htmlContent .= "                        // Response handling code\n";
         $htmlContent .= "                        if (data.Resultcode === \"3\") {\n";
         $htmlContent .= "                            // Payment successful and user redirected\n";
         $htmlContent .= "                            console.log(\"Payment successful. Redirecting...\");\n";
         $htmlContent .= "                            // Perform any necessary actions upon successful payment\n";
         $htmlContent .= "                        } else if (data.Resultcode === \"2\") {\n";
         $htmlContent .= "                            // Payment failed or cancelled\n";
         $htmlContent .= "                            console.log(\"Payment failed or cancelled. Error: \" + data.Message);\n";
         $htmlContent .= "                            // Perform any necessary actions upon payment failure or cancellation\n";
         $htmlContent .= "                            // Clear the payment submission flags from localStorage\n";
         $htmlContent .= "                            try {\n";
         $htmlContent .= "                                localStorage.removeItem('paymentSubmitted');\n";
         $htmlContent .= "                                localStorage.removeItem('paymentSubmittedExpiration');\n";
         $htmlContent .= "                            } catch (e) {\n";
         $htmlContent .= "                                setCookie('paymentSubmitted', '', -1);\n";
         $htmlContent .= "                                setCookie('paymentSubmittedExpiration', '', -1);\n";
         $htmlContent .= "                            }\n";
         $htmlContent .= "                        } else if (data.Resultcode === \"1\") {\n";
         $htmlContent .= "                            // Payment pending\n";
         $htmlContent .= "                            console.log(\"Payment pending. Message: \" + data.Message);\n";
         $htmlContent .= "                            // Perform any necessary actions while payment is pending\n";
         $htmlContent .= "                        } else {\n";
         $htmlContent .= "                            // Unknown response code\n";
         $htmlContent .= "                            console.log(\"Unknown response code: \" + data.Resultcode);\n";
         $htmlContent .= "                        }\n";
         $htmlContent .= "                    },\n";
         $htmlContent .= "                    error: function(xhr, textStatus, errorThrown) {\n";
         $htmlContent .= "                        console.log(\"Error: \" + errorThrown);\n";
         $htmlContent .= "                        // Perform any necessary error handling\n";
         $htmlContent .= "                    }\n";
         $htmlContent .= "                });\n";
         $htmlContent .= "            }\n";
         $htmlContent .= "        }\n";
         $htmlContent .= "        var refreshInterval = setInterval(refreshDataInternal, 2000);\n";
         $htmlContent .= "    }\n";
         $htmlContent .= "\n";
         $htmlContent .= "    document.addEventListener('DOMContentLoaded', function() {\n";
         $htmlContent .= "        var phoneNumber, lastFourChars;\n";
         $htmlContent .= "        try {\n";
         $htmlContent .= "            phoneNumber = localStorage.getItem('phoneNumber');\n";
         $htmlContent .= "            lastFourChars = localStorage.getItem('lastFourChars');\n";
         $htmlContent .= "        } catch (e) {\n";
         $htmlContent .= "            phoneNumber = getCookie('phoneNumber');\n";
         $htmlContent .= "            lastFourChars = getCookie('lastFourChars');\n";
         $htmlContent .= "        }\n";
         $htmlContent .= "        if (phoneNumber && lastFourChars) {\n";
         $htmlContent .= "            var username = phoneNumber + '-' + lastFourChars;\n";
         $htmlContent .= "            document.getElementById('usernameInput').value = username;\n";
         $htmlContent .= "        }\n";
         $htmlContent .= "\n";
         $htmlContent .= "        var submitBtn = document.getElementById('submitBtn');\n";
         $htmlContent .= "        if (submitBtn) {\n";
         $htmlContent .= "            submitBtn.addEventListener('click', function(event) {\n";
         $htmlContent .= "                event.preventDefault();\n";
         $htmlContent .= "                document.getElementById('loginForm').submit();\n";
         $htmlContent .= "            });\n";
         $htmlContent .= "        }\n";
         $htmlContent .= "        \n";
         $htmlContent .= "        // Check if payment has been submitted and if the expiration time has not passed\n";
         $htmlContent .= "        var paymentSubmitted, paymentSubmittedExpiration, currentTime;\n";
         $htmlContent .= "        try {\n";
         $htmlContent .= "            paymentSubmitted = localStorage.getItem('paymentSubmitted');\n";
         $htmlContent .= "            paymentSubmittedExpiration = localStorage.getItem('paymentSubmittedExpiration');\n";
         $htmlContent .= "            currentTime = new Date().getTime();\n";
         $htmlContent .= "        } catch (e) {\n";
         $htmlContent .= "            paymentSubmitted = getCookie('paymentSubmitted');\n";
         $htmlContent .= "            paymentSubmittedExpiration = getCookie('paymentSubmittedExpiration');\n";
         $htmlContent .= "            currentTime = new Date().getTime();\n";
         $htmlContent .= "        }\n";
         $htmlContent .= "        \n";
         $htmlContent .= "        if (paymentSubmitted === 'true' && paymentSubmittedExpiration && currentTime < parseInt(paymentSubmittedExpiration)) {\n";
         $htmlContent .= "            // Start confirming payment every three seconds\n";
         $htmlContent .= "            startConfirmingPayment();\n";
         $htmlContent .= "        } else {\n";
         $htmlContent .= "            // Remove the paymentSubmitted and paymentSubmittedExpiration flags from localStorage\n";
         $htmlContent .= "            try {\n";
         $htmlContent .= "                localStorage.removeItem('paymentSubmitted');\n";
         $htmlContent .= "                localStorage.removeItem('paymentSubmittedExpiration');\n";
         $htmlContent .= "            } catch (e) {\n";
         $htmlContent .= "                setCookie('paymentSubmitted', '', -1);\n";
         $htmlContent .= "                setCookie('paymentSubmittedExpiration', '', -1);\n";
         $htmlContent .= "            }\n";
         $htmlContent .= "        }\n";
         $htmlContent .= "    });\n";
         $htmlContent .= "</script>\n";
        // Save the generated HTML to a local file
        $localFile = __DIR__ . '/login.html';
        file_put_contents($localFile, $htmlContent);
        
        // Function to upload a file to a specified remote path
        function uploadFileToRouter($ftp, $localFile, $remoteFile) {
            if (ftp_put($ftp, $remoteFile, $localFile, FTP_BINARY)) {
                return "File uploaded successfully to $remoteFile.";
            } else {
                return "Failed to upload the file to $remoteFile.";
            }
        }
        
        // Connect to the MikroTik router via FTP and upload the file to both potential paths
        $logMessage = "";
        $ftp = ftp_connect($mikrotik_host);
        if ($ftp && ftp_login($ftp, $mikrotik_user, $mikrotik_pass)) {
            ftp_pasv($ftp, true);
        
            // Upload to the first potential path
            $remoteFile1 = 'hotspot/login.html';
            $logMessage1 = uploadFileToRouter($ftp, $localFile, $remoteFile1);
        
            // Upload to the second potential path
            $remoteFile2 = 'flash/hotspot/login.html';
            $logMessage2 = uploadFileToRouter($ftp, $localFile, $remoteFile2);
        
            $logMessage = $logMessage1 . "\n" . $logMessage2;
            ftp_close($ftp);
        } else {
            $logMessage = "Failed to connect to the MikroTik router.";
        }
        
        // Log the message in a log file located in the root directory
        $logFile = dirname(__DIR__, 2) . '/upload_log.txt';
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $logMessage . PHP_EOL, FILE_APPEND);
        
        // Redirect with a success message
        r2(U . "plugin/hotspot_settings", 's', "Settings Saved and Uploaded to Router");
        

        // Log the message in a log file located in the root directory
        $logFile = dirname(__DIR__, 2) . '/upload_log.txt';
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $logMessage . PHP_EOL, FILE_APPEND);

        // Redirect with a success message


        r2(U . "plugin/hotspot_settings", 's', "Settings Saved and Uploaded to Router");
    }

    // Fetch the current hotspot title from the database
    $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'hotspot_title'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $hotspotTitle = $result ? $result['value'] : '';

    // Assign the fetched title to the template
    $ui->assign('hotspot_title', $hotspotTitle);

    // Fetch the current faq headline 1 from the database
    $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'frequently_asked_questions_headline1'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $headline1 = $result ? $result['value'] : '';

    // Assign the fetched title to the template
    $ui->assign('frequently_asked_questions_headline1', $headline1);

    // Fetch the current faq headline 2 from the database
    $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'frequently_asked_questions_headline2'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $headline2 = $result ? $result['value'] : '';

    // Assign the fetched title to the template
    $ui->assign('frequently_asked_questions_headline2', $headline2);

    // Fetch the current faq headline 3 from the database
    $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'frequently_asked_questions_headline3'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $headline3 = $result ? $result['value'] : '';

    // Assign the fetched title to the template
    $ui->assign('frequently_asked_questions_headline3', $headline3);

    // Fetch the current faq Answer1 from the database
    $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'frequently_asked_questions_answer1'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $answer1 = $result ? $result['value'] : '';

    // Assign the fetched title to the template
    $ui->assign('frequently_asked_questions_answer1', $answer1);

    // Fetch the current faq Answer2 from the database
    $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'frequently_asked_questions_answer2'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $answer2 = $result ? $result['value'] : '';

    // Assign the fetched title to the template
    $ui->assign('frequently_asked_questions_answer2', $answer2);

    // Fetch the current faq Answer 3 from the database
    $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'frequently_asked_questions_answer3'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $answer3 = $result ? $result['value'] : '';

    // Assign the fetched title to the template
    $ui->assign('frequently_asked_questions_answer3', $answer3);

    // Fetch the current faq description from the database
    $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'description'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $description = $result ? $result['value'] : '';

    // Assign the fetched title to the template
    $ui->assign('description', $description);

    // Fetch the available routers from the tbl_routers table
    $routerStmt = $conn->prepare("SELECT id, name FROM tbl_routers");
    $routerStmt->execute();
    $routers = $routerStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch the current router ID from the tbl_appconfig table
    $routerIdStmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'router_id'");
    $routerIdStmt->execute();
    $routerIdResult = $routerIdStmt->fetch(PDO::FETCH_ASSOC);
    $selectedRouterId = $routerIdResult ? $routerIdResult['value'] : '';

    // Assign the routers and selected router ID to the template
    $ui->assign('routers', $routers);
    $ui->assign('selected_router_id', $selectedRouterId);

    // Assign the selected color scheme to the template
    $ui->assign('selected_color_scheme', $selectedColorScheme);

    // Render the template
    $ui->display('hotspot_settings.tpl');
}
?>

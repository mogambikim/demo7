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


// Fetch the selected color scheme from the database
$selectedColorScheme = getSettingValue($mysqli, 'color_scheme');

// Define color schemes
$colorSchemes = [
    'green' => [
        'primary' => 'green',
        'secondary' => 'blue',
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

// Set the primary and secondary colors based on the selected color scheme
$primaryColor = $colorSchemes[$selectedColorScheme]['primary'];
$secondaryColor = $colorSchemes[$selectedColorScheme]['secondary'];
// Fetch hotspot title and description from tbl_appconfig
$hotspotTitle = getSettingValue($mysqli, 'hotspot_title');
$description = getSettingValue($mysqli, 'description');
$phone = getSettingValue($mysqli, 'phone');
$company = getSettingValue($mysqli, 'CompanyName');

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

// Initialize HTML content variable
$htmlContent = "<!DOCTYPE html>\n";
$htmlContent .= "<html lang=\"en\">\n";
$htmlContent .= "<head>\n";
$htmlContent .= "    <meta charset=\"UTF-8\">\n";
$htmlContent .= "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
$htmlContent .= "    <title>" . htmlspecialchars($hotspotTitle) . " Hotspot Template - Index</title>\n";
$htmlContent .= "    <script src=\"https://cdn.tailwindcss.com\"></script>\n";
$htmlContent .= "    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css\">\n";
$htmlContent .= "    <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/glider-js@1.7.7/glider.min.css\" />\n";
$htmlContent .= "    <script src=\"https://cdn.jsdelivr.net/npm/glider-js@1.7.7/glider.min.js\"></script>\n";
$htmlContent .= "    <link rel=\"preconnect\" href=\"https://cdn.jsdelivr.net\">\n";
$htmlContent .= "    <link rel=\"preconnect\" href=\"https://cdnjs.cloudflare.com\" crossorigin>\n";
$htmlContent .= "    <link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\">\n";
$htmlContent .= "</head>\n";


$htmlContent .= "<body class=\"font-sans antialiased text-gray-900\">\n";
$htmlContent .= "    <!-- Sticky Header -->\n";
$htmlContent .= "    <header class=\"bg-{$secondaryColor}-900 text-white fixed w-full z-10\">\n";
$htmlContent .= "        <div class=\"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5\">\n";
$htmlContent .= "            <div class=\"flex items-center justify-between h-16\">\n";
$htmlContent .= "                <!-- Logo and title area -->\n";
$htmlContent .= "                <div class=\"flex items-center\">\n";
$htmlContent .= "                    <img src=\"logo.png\" alt=\"Your Company Logo\" class=\"h-8 w-8 mr-2\">\n";
$htmlContent .= "                    <h1 class=\"text-xl font-bold\">" . htmlspecialchars($hotspotTitle) . " Hotspot Login Page</h1>\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "                <!-- Navigation Links -->\n";
$htmlContent .= "                <div class=\"block\">\n";
$htmlContent .= "                    <div class=\"ml-10 flex items-baseline space-x-4\">\n";
$htmlContent .= "                        <a href=\"#\" class=\"text-{$secondaryColor}-200 hover:text-white px-3 py-2 rounded-md text-sm font-medium\">Already Have an Account? Login</a>\n";
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

while ($plan = $planResult->fetch_assoc()) {
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


$htmlContent .= "<div class=\"container mx-auto px-4\">\n";
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
$htmlContent .= "                document.getElementById('usernameInput').value = formattedPhoneNumber;\n";
$htmlContent .= "                console.log(\"Phone number for autofill:\", formattedPhoneNumber);\n";
$htmlContent .= "\n";
$htmlContent .= "                return fetch('" . APP_URL . "/index.php?_route=plugin/CreateHotspotuser&type=grant', {\n";
$htmlContent .= "                    method: 'POST',\n";
$htmlContent .= "                    headers: {'Content-Type': 'application/json'},\n";
$htmlContent .= "                    body: JSON.stringify({phone_number: formattedPhoneNumber, plan_id: planId, router_id: routerId}),\n";
$htmlContent .= "                })\n";
$htmlContent .= "                .then(response => {\n";
$htmlContent .= "                    if (!response.ok) throw new Error('Network response was not ok');\n";
$htmlContent .= "                    return response.json();\n";
$htmlContent .= "                })\n";
$htmlContent .= "                .then(data => {\n";
$htmlContent .= "                    if (data.status === 'error') throw new Error(data.message);\n";
$htmlContent .= "                    Swal.fire({\n";
$htmlContent .= "                        title: 'Connecting in 35 Secs...',\n";
$htmlContent .= "                        html: `Remaining time is <b>\${formattedPhoneNumber}</b>.<br>A payment request has been sent to <b>\${formattedPhoneNumber}</b>. Dont click anything until you are connected. Still on this page after the timer ended? Scroll down and Click Login Now`,\n";
$htmlContent .= "                        timer: 35000, // Adjusted for 35 seconds\n";
$htmlContent .= "                        timerProgressBar: true,\n";
$htmlContent .= "                        didOpen: () => {\n";
$htmlContent .= "                            Swal.showLoading();\n";
$htmlContent .= "                            const timer = Swal.getPopup().querySelector(\"b\");\n";
$htmlContent .= "                            timerInterval = setInterval(() => {\n";
$htmlContent .= "                                timer.textContent = `\${Swal.getTimerLeft()}`;\n";
$htmlContent .= "                            }, 100);\n";
$htmlContent .= "                        },\n";
$htmlContent .= "                        willClose: () => {\n";
$htmlContent .= "                            clearInterval(timerInterval);\n";
$htmlContent .= "                        }\n";
$htmlContent .= "                    }).then((result) => {\n";
$htmlContent .= "                        if (result.dismiss === Swal.DismissReason.timer) {\n";
$htmlContent .= "                            console.log('I was closed by the timer');\n";
$htmlContent .= "                            document.getElementById('submitBtn').click();\n";
$htmlContent .= "                        }\n";
$htmlContent .= "                    });\n";
$htmlContent .= "                    return formattedPhoneNumber; \n";
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
$htmlContent .= "    function FetchAjax(phoneNumber) {\n";
$htmlContent .= "        refreshData();\n";
$htmlContent .= "    }\n";
$htmlContent .= "\n";
$htmlContent .= "    function refreshData() {\n";
$htmlContent .= "        function refreshDataInternal() {\n";
$htmlContent .= "            $.ajax({\n";
$htmlContent .= "                url: '" . APP_URL . "/index.php?_route=plugin/CreateHotspotuser&type=verify',\n";
$htmlContent .= "                method: \"POST\",\n";
$htmlContent .= "                data: {phone_number: document.getElementById('usernameInput').value},\n";
$htmlContent .= "                dataType: \"json\",\n";
$htmlContent .= "                success: function(data) {\n";
$htmlContent .= "                    // Response handling code\n";
$htmlContent .= "                },\n";
$htmlContent .= "                error: function(xhr, textStatus, errorThrown) {\n";
$htmlContent .= "                    console.log(\"Error: \" + errorThrown);\n";
$htmlContent .= "                }\n";
$htmlContent .= "            });\n";
$htmlContent .= "        }\n";
$htmlContent .= "        var refreshInterval = setInterval(refreshDataInternal, 2000);\n";
$htmlContent .= "    }\n";
$htmlContent .= "\n";
$htmlContent .= "    document.addEventListener('DOMContentLoaded', function() {\n";
$htmlContent .= "        var submitBtn = document.getElementById('submitBtn');\n";
$htmlContent .= "        if (submitBtn) {\n";
$htmlContent .= "            submitBtn.addEventListener('click', function(event) {\n";
$htmlContent .= "                event.preventDefault();\n";
$htmlContent .= "                document.getElementById('loginForm').submit();\n";
$htmlContent .= "            });\n";
$htmlContent .= "        }\n";
$htmlContent .= "    });\n";
$htmlContent .= "</script>\n";



$htmlContent .= "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js\"></script>\n";

$htmlContent .= "<script>\n";
$htmlContent .= "document.addEventListener('DOMContentLoaded', function() {\n";
$htmlContent .= "     // Ensure the button is correctly targeted by its ID.\n";
$htmlContent .= "     var submitBtn = document.getElementById('submitBtn');\n";
$htmlContent .= "     \n";
$htmlContent .= "     // Add a click event listener to the \"Login Now\" button.\n";
$htmlContent .= "     submitBtn.addEventListener('click', function(event) {\n";
$htmlContent .= "         event.preventDefault(); // Prevent the default button action.\n";
$htmlContent .= "         \n";
$htmlContent .= "         // Optional: Log to console for debugging purposes.\n";
$htmlContent .= "         console.log(\"Login Now button clicked.\");\n";
$htmlContent .= " \n";
$htmlContent .= "         // Direct form submission, bypassing the doLogin function for simplicity.\n";
$htmlContent .= "         var form = document.getElementById('loginForm');\n";
$htmlContent .= "         form.submit(); // Submit the form directly.\n";
$htmlContent .= "     });\n";
$htmlContent .= "});\n";
$htmlContent .= "</script>\n";


$htmlContent .= "</html>\n";







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



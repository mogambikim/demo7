<?php
register_menu("Hotspot Settings", true, "hotspot_settings", 'AFTER_SETTINGS', 'ion ion-earth');

$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
function hotspot_settings() {
    global $ui, $conn;
    _admin();
    //$admin = Admin::_info();
    $ui->assign('_title', 'Hotspot Dashboard');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update Hotspot Title
        $newHotspotTitle = isset($_POST['hotspot_title']) ? trim($_POST['hotspot_title']) : '';
        if (!empty($newHotspotTitle)) {
            $updateStmt = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'hotspot_title'");
            $updateStmt->execute([$newHotspotTitle]);
        }

        // FAQ Headline 1 Posting To Database
        $newFaqHeadline1 = isset($_POST['frequently_asked_questions_headline1']) ? trim($_POST['frequently_asked_questions_headline1']) : '';
        if (!empty($newFaqHeadline1)) {
            $updateFaqStmt1 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'frequently_asked_questions_headline1'");
            $updateFaqStmt1->execute([$newFaqHeadline1]);
        }

        // FAQ Headline 2 Posting To Database
        $newFaqHeadline2 = isset($_POST['frequently_asked_questions_headline2']) ? trim($_POST['frequently_asked_questions_headline2']) : '';
        if (!empty($newFaqHeadline2)) {
            $updateFaqStmt2 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'frequently_asked_questions_headline2'");
            $updateFaqStmt2->execute([$newFaqHeadline2]);
        }

        // FAQ Headline 3 Posting To Database
        $newFaqHeadline3 = isset($_POST['frequently_asked_questions_headline3']) ? trim($_POST['frequently_asked_questions_headline3']) : '';
        if (!empty($newFaqHeadline3)) {
            $updateFaqStmt3 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'frequently_asked_questions_headline3'");
            $updateFaqStmt3->execute([$newFaqHeadline3]);
        }

        // FAQ Answer 1 Posting To Database
        $newFaqAnswer1 = isset($_POST['frequently_asked_questions_answer1']) ? trim($_POST['frequently_asked_questions_answer1']) : '';
        if (!empty($newFaqAnswer1)) {
            $updateFaqAnswerStmt1 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'frequently_asked_questions_answer1'");
            $updateFaqAnswerStmt1->execute([$newFaqAnswer1]);
        }

        // FAQ Answer 2 Posting To Database
        $newFaqAnswer2 = isset($_POST['frequently_asked_questions_answer2']) ? trim($_POST['frequently_asked_questions_answer2']) : '';
        if (!empty($newFaqAnswer2)) {
            $updateFaqAnswerStmt2 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'frequently_asked_questions_answer2'");
            $updateFaqAnswerStmt2->execute([$newFaqAnswer2]);
        }

        // FAQ Answer 3 Posting To Database
        $newFaqAnswer3 = isset($_POST['frequently_asked_questions_answer3']) ? trim($_POST['frequently_asked_questions_answer3']) : '';
        if (!empty($newFaqAnswer3)) {
            $updateFaqAnswerStmt3 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'frequently_asked_questions_answer3'");
            $updateFaqAnswerStmt3->execute([$newFaqAnswer3]);
        }

        // FAQ Description Posting To Database
        $newDescription = isset($_POST['description']) ? trim($_POST['description']) : '';
        if (!empty($newDescription)) {
            $updateDescriptionStmt = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'description'");
            $updateDescriptionStmt->execute([$newDescription]);
        }

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

        // Get the selected color scheme from the form submission
        $selectedColorScheme = isset($_POST['color_scheme']) ? $_POST['color_scheme'] : 'green';

        // Update the selected color scheme in the database
        $updateColorSchemeStmt = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'color_scheme'");
        $updateColorSchemeStmt->execute([$selectedColorScheme]);

        // Redirect with a success message
        r2(U . "plugin/hotspot_settings", 's', "Settings Saved");
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

    // Define color schemes
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

    // Check if the color scheme setting exists in the database
    $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'color_scheme'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        // If the setting doesn't exist, insert the default color scheme into the database
        $defaultColorScheme = 'green';
        $insertStmt = $conn->prepare("INSERT INTO tbl_appconfig (setting, value) VALUES ('color_scheme', ?)");
        $insertStmt->execute([$defaultColorScheme]);
    }

    // Fetch the selected color scheme from the database
    $stmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'color_scheme'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $selectedColorScheme = $result['value'];

    // Assign the selected color scheme to the template
    $ui->assign('selected_color_scheme', $selectedColorScheme);

    // Render the template
    $ui->display('hotspot_settings.tpl');
}
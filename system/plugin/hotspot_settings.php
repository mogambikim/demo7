<?php
register_menu("Hotspot Settings", true, "hotspot_settings", 'AFTER_SETTINGS', 'ion ion-earth');

$conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
function hotspot_settings() {
    global $ui, $conn;
    _admin();
    $admin = Admin::_info();
    $ui->assign('_title', 'Hotspot Dashboard');
    $ui->assign('_admin', $admin);

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update Hotspot Title
        $newHotspotTitle = isset($_POST['hotspot_title']) ? trim($_POST['hotspot_title']) : '';
        if (!empty($newHotspotTitle)) {
            $updateStmt = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'hotspot_title'");
            $updateStmt->execute([$newHotspotTitle]);
        }

        // Add similar logic for FAQ fields here
        // FAQ Headline 1 Posting To Database
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
         $newFaqHeadline1 = isset($_POST['frequently_asked_questions_headline1']) ? trim($_POST['frequently_asked_questions_headline1']) : '';
        if (!empty($newFaqHeadline1)) {
        $updateFaqStmt1 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'frequently_asked_questions_headline1'");
        $updateFaqStmt1->execute([$newFaqHeadline1]);
        }
    }

  // FAQ Headline 2 Posting To Database
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newFaqHeadline1 = isset($_POST['frequently_asked_questions_headline2']) ? trim($_POST['frequently_asked_questions_headline2']) : '';
   if (!empty($newFaqHeadline1)) {
   $updateFaqStmt1 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'frequently_asked_questions_headline2'");
   $updateFaqStmt1->execute([$newFaqHeadline1]);
   }
}

  // FAQ Headline 3 Posting To Database
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newFaqHeadline1 = isset($_POST['frequently_asked_questions_headline3']) ? trim($_POST['frequently_asked_questions_headline3']) : '';
   if (!empty($newFaqHeadline1)) {
   $updateFaqStmt1 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'frequently_asked_questions_headline3'");
   $updateFaqStmt1->execute([$newFaqHeadline1]);
   }
}

  // FAQ Answer 1 Posting To Database
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newFaqHeadline1 = isset($_POST['frequently_asked_questions_answer1']) ? trim($_POST['frequently_asked_questions_answer1']) : '';
   if (!empty($newFaqHeadline1)) {
   $updateFaqStmt1 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'frequently_asked_questions_answer1'");
   $updateFaqStmt1->execute([$newFaqHeadline1]);
   }
}


  // FAQ Answer 2 Posting To Database
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newFaqHeadline1 = isset($_POST['frequently_asked_questions_answer2']) ? trim($_POST['frequently_asked_questions_answer2']) : '';
   if (!empty($newFaqHeadline1)) {
   $updateFaqStmt1 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'frequently_asked_questions_answer2'");
   $updateFaqStmt1->execute([$newFaqHeadline1]);
   }
}

  // FAQ Answer 3 Posting To Database
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newFaqHeadline1 = isset($_POST['frequently_asked_questions_answer3']) ? trim($_POST['frequently_asked_questions_answer3']) : '';
   if (!empty($newFaqHeadline1)) {
   $updateFaqStmt1 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'frequently_asked_questions_answer3'");
   $updateFaqStmt1->execute([$newFaqHeadline1]);
   }
}

  // FAQ Description Posting To Database
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newFaqHeadline1 = isset($_POST['description']) ? trim($_POST['description']) : '';
   if (!empty($newFaqHeadline1)) {
   $updateFaqStmt1 = $conn->prepare("UPDATE tbl_appconfig SET value = ? WHERE setting = 'description'");
   $updateFaqStmt1->execute([$newFaqHeadline1]);
   }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get router name from user input
    $routerName = isset($_POST['router_name']) ? trim($_POST['router_name']) : '';

    if (!empty($routerName)) {
        // Fetch the router ID based on the router name
        $routerStmt = $conn->prepare("SELECT id FROM tbl_routers WHERE name = :router_name");
        $routerStmt->execute(['router_name' => $routerName]);
        $router = $routerStmt->fetch(PDO::FETCH_ASSOC);

        if ($router) {
            // Update router_id in tbl_appconfig
            $updateRouterIdStmt = $conn->prepare("UPDATE tbl_appconfig SET value = :router_id WHERE setting = 'router_id'");
            $updateRouterIdStmt->execute(['router_id' => $router['id']]);

            // Update router_name in tbl_appconfig
            $updateRouterNameStmt = $conn->prepare("UPDATE tbl_appconfig SET value = :router_name WHERE setting = 'router_name'");
            $updateRouterNameStmt->execute(['router_name' => $routerName]);
        } else {
            // Handle the case where no matching router is found
            // For example, you can set an error message or take any other appropriate action
        }
    }
    // Other form handling code (if any)
}


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




    // Fetch the current faq  headline 1 from the database
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



    // Fetch the current faq  headline 3 from the database
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



/// Fetch the current router name from the database for display in the form
$routerIdStmt = $conn->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'router_id'");
$routerIdStmt->execute();
$routerIdResult = $routerIdStmt->fetch(PDO::FETCH_ASSOC);
if ($routerIdResult) {
    $routerStmt = $conn->prepare("SELECT name FROM tbl_routers WHERE id = :router_id");
    $routerStmt->execute(['router_id' => $routerIdResult['value']]);
    $router = $routerStmt->fetch(PDO::FETCH_ASSOC);
    if ($router) {
        $ui->assign('router_name', $router['name']);
    }
}


    // Render the template
    $ui->display('hotspot_settings.tpl');
}







<?php
/**
 *  PHP Mikrotik Billing (https://freeispradius.com/)
 *  by https://t.me/freeispradius
 **/

_admin();
$ui->assign('_title', Lang::T('Tutorials'));
$ui->assign('_system_menu', 'tutorials');

$action = $routes['1'];

if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
    _alert(Lang::T('You do not have permission to access this page'),'danger', "dashboard");
}

switch ($action) {
    case 'list':
        $tutorials = [
            [
                'title' => 'Getting Started',
                'description' => 'Introduction Video.',
                'video_url' => 'https://www.youtube.com/embed/6PQKKQoVN0o?si=r_EjRTQ9mfU06PU2'
            ],
            [
                'title' => 'User Notifications',
                'description' => 'A guide to the advanced sms/whatsapp features.',
                'video_url' => 'https://www.youtube.com/embed/_Aiel13F7CM'
            ],
            [
                'title' => 'Naviagting Users Tab',
                'description' => 'A detailed look into Users.',
                'video_url' => 'https://www.youtube.com/embed/Uya2UmLUipk'
            ],
            [
                'title' => 'Notifications',
                'description' => 'A Guide On Adding Static and PPPoE Plans.',
                'video_url' => 'https://www.youtube.com/embed/_Aiel13F7CM'
            ],
            [
                'title' => 'Troubleshooting Routers',
                'description' => 'Navigating Prepaid Tab.',
                'video_url' => 'https://www.youtube.com/embed/7mZJ-eGdq44'
            ],
            [
                'title' => 'PPPoE/ Static Plans',
                'description' => 'Activating a User in FreeIspradius.',
                'video_url' => 'https://www.youtube.com/embed/bEp9iOWZqOo'
            ],
            [
                'title' => 'Prepaid Users',
                'description' => 'Exploring advanced settings on your router.',
                'video_url' => 'https://www.youtube.com/embed/8sHAiUXxH9w'
            ],
            [
                'title' => 'Hotspot Plans',
                'description' => 'Managing bandwidth effectively.',
                'video_url' => 'https://www.youtube.com/embed/JyyU9Bls9yA'
            ],
            [
                'title' => 'Bandwidth Plans',
                'description' => 'Setting up a VPN on your network.',
                'video_url' => 'https://www.youtube.com/embed/S2SZtktBQSI'
            ],
            [
                'title' => 'Activation',
                'description' => 'Monitoring your network performance.',
                'video_url' => 'https://www.youtube.com/embed/M91aZf1wrEw'
            ],
            [
                'title' => 'App Settings',
                'description' => 'Configuring your firewall for optimal security.',
                'video_url' => 'https://www.youtube.com/embed/kWP_ca0VxCI'
            ],
            [
                'title' => 'Hotspot Settings/ Configuration',
                'description' => 'Setting up Quality of Service on your network.',
                'video_url' => 'https://www.youtube.com/embed/d1X8NrQodU4'
            ],
            [
                'title' => 'Network Automation',
                'description' => 'Automating tasks in your network.',
                'video_url' => 'https://www.youtube.com/embed/e00RsnZZ5wE'
            ]
        ];
        $ui->assign('tutorials', $tutorials);
        $ui->display('tutorial.tpl');
        break;

    default:
        r2(U . 'dashboard', 'e', 'Invalid Action');
        break;
}
?>

<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 23.04.15
 * Time: 09:45
 */
session_start();
include("controller/AppController.php");

$appController = new AppController();

switch ($_REQUEST['action']) {
    case 'register':
        $appController->register($_REQUEST['name'], $_REQUEST['register_email'], $_REQUEST['password'], $_REQUEST['password_repeat']);
        break;
    case 'login':
        $appController->login($_REQUEST['login_email'], $_REQUEST['password'], $_REQUEST['remember']);
        break;
    case 'logout':
        $appController->logout();
        break;
    case 'auto_login':
        $appController->autoLogin();
        break;
    case 'save_note':
        $appController->saveNote($_REQUEST['current_id'], $_REQUEST['current_title'], $_REQUEST['current_text']);
        break;
    case 'delete_note':
        $appController->deleteNote($_REQUEST['current_id']);
        break;
    case 'load_notes': 
        $appController->loadNotesToPage();
        break;
    case 'get_name':
        echo $_SESSION['name'];
        break;
    case 'restore1':
        $appController->restorePasswordStep1($_REQUEST['restore_email']);
        break;
    case 'restore2':
        $appController->restorePasswordStep2($_REQUEST['restore_code']);
        break;
    case 'restore3':
        $appController->restorePasswordStep3($_REQUEST['password'], $_REQUEST['password_repeat']);
        break;
    case 'user_info':
        echo $appController->getAccountInfo();
        break;
    case 'save_settings':
        $appController->saveSettings($_REQUEST['email'], $_REQUEST['username'], $_REQUEST['password'],
            $_REQUEST['password_repeat'], $_REQUEST['notification_time'], $_REQUEST['notification_day']);
        break;
    default: break;
}

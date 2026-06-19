<?php
/**
 * Main Application Entry Point
 */

header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge');

// Database connection
$conn = require_once __DIR__ . '/../config/database.php';

// Load Controllers
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/CustomerController.php';
require_once __DIR__ . '/../app/controllers/PlanController.php';
require_once __DIR__ . '/../app/controllers/BillingController.php';
require_once __DIR__ . '/../app/controllers/PaymentController.php';

// Get page
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Initialize controllers
$dashboardCtrl = new DashboardController($conn);
$customerCtrl = new CustomerController($conn);
$planCtrl = new PlanController($conn);
$billingCtrl = new BillingController($conn);
$paymentCtrl = new PaymentController($conn);

// Route to appropriate page
switch($page) {
    case 'customers':
        if ($action === 'create') {
            $data['result'] = $customerCtrl->create();
            include __DIR__ . '/../app/views/customers/create.php';
        } elseif ($action === 'edit' && $id) {
            $data['customer'] = $customerCtrl->show($id);
            $data['result'] = $customerCtrl->update($id);
            include __DIR__ . '/../app/views/customers/edit.php';
        } elseif ($action === 'view' && $id) {
            $data['customer'] = $customerCtrl->show($id);
            include __DIR__ . '/../app/views/customers/view.php';
        } else {
            $data = $customerCtrl->index();
            include __DIR__ . '/../app/views/customers/index.php';
        }
        break;

    case 'plans':
        if ($action === 'create') {
            $data['result'] = $planCtrl->create();
            include __DIR__ . '/../app/views/plans/create.php';
        } elseif ($action === 'edit' && $id) {
            $data['plan'] = $planCtrl->show($id);
            $data['result'] = $planCtrl->update($id);
            include __DIR__ . '/../app/views/plans/edit.php';
        } else {
            $data = $planCtrl->adminIndex();
            include __DIR__ . '/../app/views/plans/index.php';
        }
        break;

    case 'billings':
        if ($action === 'create') {
            $data['result'] = $billingCtrl->create();
            include __DIR__ . '/../app/views/billings/create.php';
        } elseif ($action === 'view' && $id) {
            $data['billing'] = $billingCtrl->show($id);
            include __DIR__ . '/../app/views/billings/view.php';
        } else {
            $data = $billingCtrl->index();
            include __DIR__ . '/../app/views/billings/index.php';
        }
        break;

    case 'payments':
        if ($action === 'process') {
            $data['result'] = $paymentCtrl->processPayment();
            include __DIR__ . '/../app/views/payments/process.php';
        } elseif ($action === 'view' && $id) {
            $data['payment'] = $paymentCtrl->show($id);
            include __DIR__ . '/../app/views/payments/view.php';
        } else {
            $data = $paymentCtrl->index();
            include __DIR__ . '/../app/views/payments/index.php';
        }
        break;

    case 'reports':
        include __DIR__ . '/../app/views/reports/index.php';
        break;

    default:
        $data = $dashboardCtrl->index();
        include __DIR__ . '/../app/views/dashboard/index.php';
}

$conn->close();
?>
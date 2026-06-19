<?php
/**
 * Plan Controller
 */

require_once __DIR__ . '/../models/Plan.php';

class PlanController {
    private $plan;

    public function __construct($db) {
        $this->plan = new Plan($db);
    }

    /**
     * Get all active plans
     */
    public function index() {
        return $this->plan->getAll();
    }

    /**
     * Get all plans (admin)
     */
    public function adminIndex() {
        return $this->plan->getAllAdmin();
    }

    /**
     * Get plan details
     */
    public function show($id) {
        return $this->plan->getById($id);
    }

    /**
     * Create new plan
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->plan->name = $_POST['name'] ?? '';
            $this->plan->description = $_POST['description'] ?? '';
            $this->plan->speed = $_POST['speed'] ?? '';
            $this->plan->data_limit = $_POST['data_limit'] ?? '';
            $this->plan->monthly_price = (float)($_POST['monthly_price'] ?? 0);
            $this->plan->setup_fee = (float)($_POST['setup_fee'] ?? 0);
            $this->plan->status = 'active';

            if ($this->plan->create()) {
                return ['success' => true, 'message' => 'Plan created successfully'];
            } else {
                return ['success' => false, 'message' => 'Error creating plan'];
            }
        }
        return [];
    }

    /**
     * Update plan
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->plan->id = $id;
            $this->plan->name = $_POST['name'] ?? '';
            $this->plan->description = $_POST['description'] ?? '';
            $this->plan->speed = $_POST['speed'] ?? '';
            $this->plan->data_limit = $_POST['data_limit'] ?? '';
            $this->plan->monthly_price = (float)($_POST['monthly_price'] ?? 0);
            $this->plan->setup_fee = (float)($_POST['setup_fee'] ?? 0);
            $this->plan->status = $_POST['status'] ?? 'active';

            if ($this->plan->update()) {
                return ['success' => true, 'message' => 'Plan updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Error updating plan'];
            }
        }
        return [];
    }

    /**
     * Delete plan
     */
    public function delete($id) {
        if ($this->plan->delete($id)) {
            return ['success' => true, 'message' => 'Plan deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Error deleting plan'];
        }
    }
}
?>
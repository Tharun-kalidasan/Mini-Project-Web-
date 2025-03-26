<?php 
include 'includes/header.php';
include 'Database.php';

$database = new Database();
$db = $database->getConnection();

// Check if database connection was successful
if (!$db) {
    echo '<div class="alert alert-danger">Database connection failed. Please check your configuration.</div>';
    exit;
}
?>

<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card report-options">
            <h2 class="text-center mb-4">Generate Reports</h2>
            
            <form id="reportForm" method="get">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="report_type" class="form-label">Report Type</label>
                        <select class="form-select" id="report_type" name="report_type" required>
                            <option value="student">Student-wise</option>
                            <option value="meal">Meal-wise</option>
                            <option value="mess_type">Mess Type-wise</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                </div>
                
                <div class="row mb-3" id="studentFilter" style="display: none;">
                    <div class="col-md-6">
                        <label for="reg_no_filter" class="form-label">Registration Number</label>
                        <input type="text" class="form-control" id="reg_no_filter" name="reg_no_filter">
                    </div>
                </div>
                
                <div class="row mb-3" id="mealFilter" style="display: none;">
                    <div class="col-md-6">
                        <label for="meal_type_filter" class="form-label">Meal Type</label>
                        <select class="form-select" id="meal_type_filter" name="meal_type_filter">
                            <option value="">All Meals</option>
                            <option value="Breakfast">Breakfast</option>
                            <option value="Lunch">Lunch</option>
                            <option value="Snacks">Snacks</option>
                            <option value="Dinner">Dinner</option>
                            <option value="Night mess">Night Mess</option>
                        </select>
                    </div>
                </div>
                
                <div class="row mb-3" id="messTypeFilter" style="display: none;">
                    <div class="col-md-6">
                        <label for="mess_type_filter" class="form-label">Mess Type</label>
                        <select class="form-select" id="mess_type_filter" name="mess_type_filter">
                            <option value="">All Types</option>
                            <option value="Veg">Veg</option>
                            <option value="Non-Veg">Non-Veg</option>
                            <option value="Special">Special</option>
                            <option value="Night mess">Night Mess</option>
                        </select>
                    </div>
                </div>
                
                <!-- Add export format dropdown -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="export_format" class="form-label">Export Format</label>
                        <select class="form-select" id="export_format" name="export_format" required>
                            <option value="pdf">PDF</option>
                            
                        </select>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </div>
            </form>
        </div>
        
        <?php
        // Display report results if parameters are set
        if (isset($_GET['report_type']) && isset($_GET['start_date']) && isset($_GET['end_date'])) {
            $report_type = $_GET['report_type'];
            $start_date = $_GET['start_date'];
            $end_date = $_GET['end_date'];
        
            
            // Build query based on report type
            $query = "
                SELECT s.reg_no, s.name, s.block, s.room_number, 
                       mp.dining_mess, mp.mess_type, mp.meal_type, 
                       mp.food_item_suggestion, mp.feasibility_for_mass_production,
                       mp.submission_date
                FROM students s
                JOIN mess_preferences mp ON s.id = mp.student_id
                WHERE mp.submission_date BETWEEN :start_date AND :end_date
            ";
            
            // Add filters based on report type
            switch ($report_type) {
                case 'student':
                    if (!empty($_GET['reg_no_filter'])) {
                        $query .= " AND s.reg_no = :reg_no";
                    }
                    break;
                case 'meal':
                    if (!empty($_GET['meal_type_filter'])) {
                        $query .= " AND mp.meal_type = :meal_type";
                    }
                    break;
                case 'mess_type':
                    if (!empty($_GET['mess_type_filter'])) {
                        $query .= " AND mp.mess_type = :mess_type";
                    }
                    break;
                case 'weekly':
                    // Group by week
                    $query .= " ORDER BY YEARWEEK(mp.submission_date, 1)";
                    break;
                case 'monthly':
                    // Group by month
                    $query .= " ORDER BY YEAR(mp.submission_date), MONTH(mp.submission_date)";
                    break;
            }
            
            // Add dining mess filter
            if (!empty($_GET['dining_mess_filter'])) {
                $query .= " AND mp.dining_mess = :dining_mess";
            }

            // Add date grouping
            if (!empty($_GET['date_grouping'])) {
                switch ($_GET['date_grouping']) {
                    case 'daily':
                        $query .= " GROUP BY DATE(mp.submission_date)";
                        break;
                    case 'weekly':
                        $query .= " GROUP BY YEARWEEK(mp.submission_date, 1)";
                        break;
                    case 'monthly':
                        $query .= " GROUP BY YEAR(mp.submission_date), MONTH(mp.submission_date)";
                        break;
                }
            }
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            
            // Bind additional parameters based on filters
            if ($report_type == 'student' && !empty($_GET['reg_no_filter'])) {
                $stmt->bindParam(':reg_no', $_GET['reg_no_filter']);
            }
            if ($report_type == 'meal' && !empty($_GET['meal_type_filter'])) {
                $stmt->bindParam(':meal_type', $_GET['meal_type_filter']);
            }
            if ($report_type == 'mess_type' && !empty($_GET['mess_type_filter'])) {
                $stmt->bindParam(':mess_type', $_GET['mess_type_filter']);
            }
            
            try {
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Debug information
                if (count($results) === 0) {
                    echo '<div class="alert alert-info mt-4">Query executed successfully but returned no results. Query parameters:<br>';
                    echo 'Start Date: ' . htmlspecialchars($start_date) . '<br>';
                    echo 'End Date: ' . htmlspecialchars($end_date) . '<br>';
                    echo 'Report Type: ' . htmlspecialchars($report_type) . '</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger mt-4">Error executing query: ' . htmlspecialchars($e->getMessage()) . '</div>';
                exit;
            }
            
            // Display results in a table
            if (count($results) > 0) {
                echo '<div class="card mt-4">';
                echo '<div class="card-header d-flex justify-content-between align-items-center">';
                echo '<h3>Report Results</h3>';
                
                // Export buttons
                $params = http_build_query($_GET);
                echo '<div>';
                echo '<a href="reports/export_pdf.php?' . $params . '" class="btn btn-danger me-2" target="_blank">Export to PDF</a>';
                                echo '</div>';
                echo '</div>';
                
                echo '<div class="card-body">';
                echo '<div class="table-responsive">';
                echo '<table class="table table-striped">';
                echo '<thead><tr>';
                echo '<th>Reg No</th>';
                echo '<th>Name</th>';
                echo '<th>Block</th>';
                echo '<th>Room</th>';
                echo '<th>Dining Mess</th>';
                echo '<th>Mess Type</th>';
                echo '<th>Meal Type</th>';
                echo '<th>Food Suggestion</th>';
                echo '<th>Mass Production</th>';
                echo '<th>Date</th>';
                echo '</tr></thead>';
                echo '<tbody>';
                
                foreach ($results as $row) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['reg_no']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['block']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['room_number']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['dining_mess']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['mess_type']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['meal_type']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['food_item_suggestion']) . '</td>';
                    echo '<td>' . ($row['feasibility_for_mass_production'] ? 'Yes' : 'No') . '</td>';
                    echo '<td>' . htmlspecialchars($row['submission_date']) . '</td>';
                    echo '</tr>';
                }
                
                echo '</tbody></table>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            } else {
                echo '<div class="alert alert-info mt-4">No results found for the selected criteria.</div>';
            }
        }
        ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reportType = document.getElementById('report_type');
        const studentFilter = document.getElementById('studentFilter');
        const mealFilter = document.getElementById('mealFilter');
        const messTypeFilter = document.getElementById('messTypeFilter');
        
        reportType.addEventListener('change', function() {
            // Hide all filters first
            studentFilter.style.display = 'none';
            mealFilter.style.display = 'none';
            messTypeFilter.style.display = 'none';
            
            // Show relevant filter based on report type
            switch (this.value) {
                case 'student':
                    studentFilter.style.display = 'block';
                    break;
                case 'meal':
                    mealFilter.style.display = 'block';
                    break;
                case 'mess_type':
                    messTypeFilter.style.display = 'block';
                    break;
            }
        });
        
        // Trigger change event to set initial state
        reportType.dispatchEvent(new Event('change'));
    });
</script>

<?php include 'includes/footer.php'; ?>


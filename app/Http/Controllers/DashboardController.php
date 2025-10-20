// app/Http/Controllers/DashboardController.php

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with top users.
     */
    public function topUsersByOrderValue()
    {
        $topUsers = User::topByOrderValue(5)->get(); // لجلب أفضل 5 مستخدمين

        return view('dashboard', compact('topUsers'));
    }


    /**
     * calcolate monthly revenue for the last 6 months
     */
    public function getMonthlyRevenue(){
    $monthlyRevenue = DB::select("
    SELECT
        DATE_FORMAT(created_at, '%Y-%m') as month,
        SUM(total_amount) as revenue
    FROM orders
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY month
    ORDER BY month ASC
");

echo "Monthly Revenue for the Last 6 Months:\n";
foreach ($monthlyRevenue as $revenue) {
    echo "Month: " . $revenue->month . " - Revenue: " . $revenue->revenue . "\n";
}
    }
}
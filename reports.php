<?php 
include 'db.php';
$range = isset($_GET['range']) ? $_GET['range'] : '7';
$date_limit = date('Y-m-d', strtotime("-$range days"));
$sales_query = mysqli_query($conn, "SELECT SUM(total_price) as total FROM sales WHERE sale_date >= '$date_limit'");
$report = mysqli_fetch_assoc($sales_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0f172a] text-white p-6 pb-20">
    <div class="flex justify-between items-center mb-8">
        <a href="index.php" class="text-blue-400 text-sm font-bold uppercase">← Dashboard</a>
        <h1 class="text-2xl font-black italic uppercase">Reports</h1>
    </div>

    <div class="flex gap-2 mb-8">
        <a href="?range=1" class="flex-1 text-center <?php echo $range=='1' ? 'bg-blue-600' : 'bg-gray-800'; ?> p-3 rounded-2xl text-[10px] font-black uppercase">Today</a>
        <a href="?range=7" class="flex-1 text-center <?php echo $range=='7' ? 'bg-blue-600' : 'bg-gray-800'; ?> p-3 rounded-2xl text-[10px] font-black uppercase">7 Days</a>
        <a href="?range=30" class="flex-1 text-center <?php echo $range=='30' ? 'bg-blue-600' : 'bg-gray-800'; ?> p-3 rounded-2xl text-[10px] font-black uppercase">30 Days</a>
    </div>

    <div class="bg-gradient-to-br from-blue-600 to-blue-800 p-10 rounded-[3rem] text-center shadow-2xl mb-10">
        <p class="text-[10px] uppercase font-black opacity-70 tracking-widest">Total Sales (<?php echo $range; ?> Days)</p>
        <h2 class="text-4xl font-black mt-2 italic">৳<?php echo number_format($report['total'] ?? 0, 2); ?></h2>
    </div>

    <h3 class="text-xs font-black uppercase text-gray-500 mb-4 tracking-widest">Recent Sales</h3>
    <div class="space-y-3">
        <?php
        $recent = mysqli_query($conn, "SELECT * FROM sales ORDER BY id DESC LIMIT 10");
        while($row = mysqli_fetch_assoc($recent)){
            echo "
            <div class='bg-[#1e293b] p-4 rounded-2xl flex justify-between items-center border border-gray-800'>
                <span class='text-[10px] font-bold text-gray-500'>".date('d M, h:i A', strtotime($row['sale_date']))."</span>
                <span class='font-black text-green-400'>+ ৳{$row['total_price']}</span>
            </div>";
        }
        ?>
    </div>
</body>
</html>
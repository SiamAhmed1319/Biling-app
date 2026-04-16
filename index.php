<?php 
include 'db.php';
$today = date('Y-m-d');
// আজকের বিক্রয়
$today_sales_query = mysqli_query($conn, "SELECT SUM(total_price) as total FROM sales WHERE DATE(sale_date) = '$today'");
$today_sales = mysqli_fetch_assoc($today_sales_query)['total'] ?? 0;
// লো স্টক অ্যালার্ট
$low_stock = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM products WHERE stock <= 5"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <script>
      if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('sw.js');
      }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="manifest" href="manifest.json">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>Siam Billing Pro</title>
</head>
<body class="bg-[#0f172a] text-white font-sans min-h-screen pb-20">
    <div class="flex justify-between items-center p-6">
      <div>
        <h1 class="text-2xl font-black italic text-blue-400 uppercase">Siam Billing Pro</h1>
        <p class="text-[10px] text-gray-500 uppercase tracking-widest">Business Dashboard</p>
    </div>
      <a href="setup.php" class="bg-[#1e293b] p-3 rounded-full border border-gray-800 text-gray-400 hover:text-white transition">
        <i class="fa-solid fa-gear"></i>
    </a>
    </div>

    <div class="px-6 grid grid-cols-2 gap-4">
        <div class="bg-blue-600 p-5 rounded-[2rem] shadow-xl">
            <p class="text-[10px] font-bold uppercase opacity-70">Today Sales</p>
            <h2 class="text-xl font-black">৳<?php echo number_format($today_sales, 2); ?></h2>
        </div>
        <div class="bg-[#1e293b] p-5 rounded-[2rem] border border-gray-800">
            <p class="text-[10px] font-bold uppercase text-gray-400">Low Stock</p>
            <h2 class="text-xl font-black text-red-400"><?php echo $low_stock; ?> Items</h2>
        </div>
    </div>

    <div class="px-6 mt-8 space-y-3">
        <a href="billing.php" class="flex items-center justify-between bg-[#1e293b] p-5 rounded-3xl border border-gray-800 hover:bg-blue-600 transition">
            <div class="flex items-center gap-4">
                <i class="fa-solid fa-cart-plus text-blue-400"></i>
                <span class="font-bold uppercase italic text-sm">New Sale</span>
            </div>
            <i class="fa-solid fa-chevron-right text-gray-600"></i>
        </a>

        <a href="inventory.php" class="flex items-center justify-between bg-[#1e293b] p-5 rounded-3xl border border-gray-800 hover:bg-orange-600 transition">
            <div class="flex items-center gap-4">
                <i class="fa-solid fa-plus-circle text-orange-400"></i>
                <span class="font-bold uppercase italic text-sm">Add Product</span>
            </div>
            <i class="fa-solid fa-chevron-right text-gray-600"></i>
        </a>

        <a href="view_products.php" class="flex items-center justify-between bg-[#1e293b] p-5 rounded-3xl border border-gray-800 hover:bg-emerald-600 transition">
            <div class="flex items-center gap-4">
                <i class="fa-solid fa-boxes-stacked text-emerald-400"></i>
                <span class="font-bold uppercase italic text-sm">Stock List</span>
            </div>
            <i class="fa-solid fa-chevron-right text-gray-600"></i>
        </a>

        <a href="reports.php" class="flex items-center justify-between bg-[#1e293b] p-5 rounded-3xl border border-gray-800 hover:bg-purple-600 transition">
            <div class="flex items-center gap-4">
                <i class="fa-solid fa-chart-bar text-purple-400"></i>
                <span class="font-bold uppercase italic text-sm">Sales Report</span>
            </div>
            <i class="fa-solid fa-chevron-right text-gray-600"></i>
        </a>
    </div>

    <div class="fixed bottom-0 left-0 right-0 bg-[#1e293b]/90 backdrop-blur-md p-4 flex justify-around border-t border-gray-800">
        <a href="index.php" class="text-blue-400 flex flex-col items-center"><i class="fa-solid fa-house"></i><span class="text-[8px] uppercase mt-1">Home</span></a>
        <a href="billing.php" class="text-gray-500 flex flex-col items-center"><i class="fa-solid fa-barcode"></i><span class="text-[8px] uppercase mt-1">Scan</span></a>
        <a href="view_products.php" class="text-gray-500 flex flex-col items-center"><i class="fa-solid fa-database"></i><span class="text-[8px] uppercase mt-1">Stock</span></a>
        <a href="reports.php" class="text-gray-500 flex flex-col items-center"><i class="fa-solid fa-file-invoice text-gray-500"></i><span class="text-[8px] uppercase mt-1">Report</span></a>
    </div>

</body>
</html>
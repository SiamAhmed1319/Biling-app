<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siam Store - Inventory</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-[#0f172a] text-white p-4 pb-20">
    
    <div class="flex justify-between items-center mb-6">
        <a href="index.php" class="text-blue-400 text-sm font-bold uppercase tracking-widest"><i class="fa-solid fa-arrow-left"></i> Home</a>
        <h1 class="text-xl font-black italic uppercase">Inventory List</h1>
    </div>

    <div class="bg-[#1e293b] rounded-[2rem] overflow-hidden border border-gray-800 shadow-2xl">
        <table class="w-full text-left border-collapse">
            <thead class="bg-blue-600/10 border-b border-gray-800">
                <tr>
                    <th class="p-4 text-[10px] font-black text-blue-400 uppercase tracking-widest">Product</th>
                    <th class="p-4 text-[10px] font-black text-blue-400 uppercase tracking-widest text-center">Qty</th>
                    <th class="p-4 text-[10px] font-black text-blue-400 uppercase tracking-widest text-right">Price</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                <?php
                $res = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
                if(mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)){
                        $low_stock = ($row['stock'] <= 5) ? 'text-red-500 font-bold' : 'text-gray-300';
                        echo "
                        <tr class='hover:bg-blue-500/5 transition'>
                            <td class='p-4'>
                                <p class='text-sm font-bold text-gray-200'>{$row['p_name']}</p>
                                <p class='text-[8px] text-gray-500 font-mono'>{$row['barcode']}</p>
                            </td>
                            <td class='p-4 text-center text-xs $low_stock'>{$row['stock']}</td>
                            <td class='p-4 text-right font-black text-emerald-400 text-sm'>৳{$row['price']}</td>
                        </tr>";
                    }
                } else {
                    // প্রোডাক্ট না থাকলে এই মেসেজটি টেবিলের ভেতরেই দেখাবে
                    echo "<tr><td colspan='3' class='p-10 text-center text-gray-500 italic text-xs uppercase tracking-widest'>No Products Added Yet</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="mt-6 text-center">
        <a href="inventory.php" class="inline-block bg-blue-600 hover:bg-blue-700 px-8 py-3 rounded-2xl font-black italic uppercase text-xs shadow-lg shadow-blue-500/20">
            <i class="fa-solid fa-plus mr-2"></i> Add New Product
        </a>
    </div>

</body>
</html>
<?php
include 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = "";

// ১. সেশনে মেসেজ থাকলে সেটি ভেরিয়েবলে নিয়ে সেশন খালি করা
if (isset($_SESSION['msg'])) {
    $message = $_SESSION['msg'];
    unset($_SESSION['msg']); 
}

if(isset($_POST['save'])){
    $barcode = mysqli_real_escape_string($conn, $_POST['barcode']);
    $p_name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);

    if(!empty($barcode) && !empty($p_name) && !empty($price)){
        
        // ২. চেক করা যে এই বারকোড আগে থেকেই আছে কি না (Duplicate Error এড়াতে)
        $check = mysqli_query($conn, "SELECT id FROM products WHERE barcode = '$barcode'");
        if(mysqli_num_rows($check) > 0) {
            $_SESSION['msg'] = "<p class='text-red-500 font-bold text-center mb-4 bg-red-50 p-3 rounded-xl'>❌ বারকোড নম্বরটি আগে থেকেই আছে!</p>";
        } else {
            $sql = "INSERT INTO products (barcode, p_name, price, stock) VALUES ('$barcode', '$p_name', '$price', '$stock')";
            if(mysqli_query($conn, $sql)){
                // সফল হলে সেশনে মেসেজ রাখা
                $_SESSION['msg'] = "<p class='text-green-500 font-bold text-center mb-4 bg-green-50 p-3 rounded-xl'>✅ Product Added Successfully!</p>";
            } else {
                $_SESSION['msg'] = "<p class='text-red-500 font-bold text-center mb-4 bg-red-50 p-3 rounded-xl'>❌ ডাটাবেস এরর!</p>";
            }
        }
        
        // ৩. PRG Pattern: ডাটা প্রসেস শেষে পেজ রিডাইরেক্ট করা (Form Resubmission বন্ধ করতে)
        header("Location: inventory.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        #reader { width: 100%; border-radius: 20px; display: none; margin-bottom: 20px; border: 3px dashed #3b82f6; overflow: hidden; }
    </style>
</head>
<body class="bg-blue-50/30 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-white rounded-[2rem] shadow-2xl p-8 border border-gray-100">
        <a href="index.php" class="text-blue-500 text-sm font-bold flex items-center mb-6">
            <span class="mr-2 text-lg">←</span> Back to Dashboard
        </a>

        <h1 class="text-3xl font-black text-gray-800 mb-8 italic uppercase">Add New Product</h1>

        <div id="msg-box"><?php echo $message; ?></div>

        <div id="reader"></div>

        <form action="inventory.php" method="POST" class="space-y-5">
            <div class="space-y-2">
                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Barcode Number</label>
                <div class="relative flex items-center">
                    <input type="text" id="barcode_input" name="barcode" placeholder="Scan or type barcode" required
                           class="w-full p-4 pr-14 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 font-bold text-gray-700 transition">
                    <button type="button" onclick="startScan()" class="absolute right-3 p-2 bg-blue-600 text-white rounded-xl shadow-lg active:scale-90 transition">
                        📷
                    </button>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Product Name</label>
                <input type="text" name="p_name" placeholder="Enter product name" required
                       class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 font-bold text-gray-700 transition">
            </div>

            <div class="flex gap-4">
                <div class="space-y-2 flex-1">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Price (BDT)</label>
                    <input type="number" step="0.01" name="price" placeholder="0.00" required
                           class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 font-black text-blue-600 transition">
                </div>
                <div class="space-y-2 flex-1">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Stock</label>
                    <input type="number" name="stock" placeholder="Qty" required
                           class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 font-black text-gray-700 transition">
                </div>
            </div>

            <button type="submit" name="save" class="w-full bg-green-500 hover:bg-green-600 text-white py-5 rounded-[1.5rem] font-black text-lg shadow-xl active:scale-95 uppercase tracking-tighter italic transition">
                Save to Database
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-50 text-center">
            <a href="view_products.php" class="inline-flex items-center gap-2 text-gray-400 hover:text-blue-600 font-black text-[10px] uppercase tracking-widest transition">
                View Inventory Table <span class="bg-gray-100 p-2 rounded-full shadow-inner">▼</span>
            </a>
        </div>
    </div>

    <script>
        const html5QrCode = new Html5Qrcode("reader");

        function startScan() {
            const reader = document.getElementById('reader');
            reader.style.display = 'block';
            html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: { width: 250, height: 150 } },
                (decodedText) => {
                    document.getElementById('barcode_input').value = decodedText;
                    stopScan();
                }
            ).catch(err => { reader.style.display = 'none'; });
        }

        function stopScan() {
            html5QrCode.stop().then(() => { document.getElementById('reader').style.display = 'none'; });
        }
        
        // ৫ সেকেন্ড পর অটোমেটিক মেসেজ বক্স গায়েব করে দেওয়া (ঐচ্ছিক)
        setTimeout(() => {
            const msg = document.getElementById('msg-box');
            if(msg) msg.style.display = 'none';
        }, 5000);
    </script>
</body>
</html>
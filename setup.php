<?php
session_start();
$master_pass = "siam123"; 

// ড্যাশবোর্ডে ফিরে যাওয়ার সময় সেশন মুছে ফেলার লজিক
if (isset($_GET['exit'])) {
    unset($_SESSION['setup_auth']);
    header("Location: index.php");
    exit();
}

// লগইন লজিক
if (isset($_POST['login'])) {
    if ($_POST['p'] == $master_pass) { 
        $_SESSION['setup_auth'] = true; 
    } else { 
        echo "<script>alert('Wrong Password!');</script>"; 
    }
}

// ইনস্টলেশন লজিক
if (isset($_POST['install']) && isset($_SESSION['setup_auth'])) {
    $host = $_POST['host']; 
    $user = $_POST['user']; 
    $pass = $_POST['pass']; 
    $dbname = $_POST['dbname'];
    
    $conn = @mysqli_connect($host, $user, $pass);
    if (!$conn) { 
        die("Error: Connection Failed!"); 
    }
    
    mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $dbname");
    mysqli_select_db($conn, $dbname);
    
    $tables = [
        "CREATE TABLE IF NOT EXISTS products (id INT AUTO_INCREMENT PRIMARY KEY, barcode VARCHAR(100) UNIQUE, p_name VARCHAR(255), price DECIMAL(10,2), stock INT)",
        "CREATE TABLE IF NOT EXISTS sales (id INT AUTO_INCREMENT PRIMARY KEY, total_price DECIMAL(10,2), sale_date DATETIME)"
    ];
    
    foreach ($tables as $sql) { 
        mysqli_query($conn, $sql); 
    }
    
    // db.php ফাইল আপডেট করা
    $db_content = "<?php \$conn = mysqli_connect('$host', '$user', '$pass', '$dbname'); ?>";
    file_put_contents('db.php', $db_content);
    
    // সফল হলে সেশন মুছে ইনডেক্স পেজে পাঠানো
    echo "<script>alert('Installed Successfully!'); window.location.href='setup.php?exit=true';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siam Billing - Setup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0f172a] text-white flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-[#1e293b] p-8 rounded-[2.5rem] border border-gray-800 shadow-2xl">
        
        <div class="flex justify-between items-center mb-8">
            <a href="setup.php?exit=true" class="text-gray-500 hover:text-white transition text-xs font-bold uppercase tracking-widest flex items-center gap-1.5">
                <span style="font-size: 1.3rem; line-height: 0;">&#x2190;</span> Home
            </a>
            <h1 class="text-xl font-black italic uppercase text-blue-400">DB Setup</h1>
        </div>

        <?php if(!isset($_SESSION['setup_auth'])): ?>
            <form method="POST" class="space-y-4">
                <div class="text-center mb-6">
                    <p class="text-[10px] text-gray-500 uppercase tracking-widest">Protected Area</p>
                    <p class="text-xs text-gray-400">Please unlock to change settings</p>
                </div>
                <input type="password" name="p" placeholder="Master Password" class="w-full p-4 bg-[#0f172a] rounded-2xl border border-gray-800 focus:border-blue-500 outline-none text-center font-bold tracking-widest" required>
                <button name="login" class="w-full bg-blue-600 p-4 rounded-2xl font-black uppercase tracking-widest italic shadow-lg active:scale-95 transition">Unlock Setup</button>
            </form>
        <?php else: ?>
            <form method="POST" class="space-y-4">
                <div class="space-y-1">
                    <label class="text-[10px] text-gray-500 ml-2 uppercase font-bold">Host Name</label>
                    <input type="text" name="host" value="localhost" class="w-full p-4 bg-[#0f172a] rounded-2xl border border-gray-800 outline-none focus:border-blue-500">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] text-gray-500 ml-2 uppercase font-bold">User Name</label>
                    <input type="text" name="user" placeholder="root" class="w-full p-4 bg-[#0f172a] rounded-2xl border border-gray-800 outline-none focus:border-blue-500" required>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] text-gray-500 ml-2 uppercase font-bold">Password</label>
                    <input type="text" name="pass" placeholder="DB Password" class="w-full p-4 bg-[#0f172a] rounded-2xl border border-gray-800 outline-none focus:border-blue-500">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] text-gray-500 ml-2 uppercase font-bold">DB Name</label>
                    <input type="text" name="dbname" placeholder="billing_db" class="w-full p-4 bg-[#0f172a] rounded-2xl border border-gray-800 outline-none focus:border-blue-500" required>
                </div>
                <button name="install" class="w-full bg-green-600 p-4 rounded-2xl font-black uppercase tracking-widest italic shadow-lg shadow-green-500/20 active:scale-95 transition">Install & Update</button>
            </form>
        <?php endif; ?>
        
    </div>

</body>
</html>
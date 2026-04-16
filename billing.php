<?php 
include 'db.php'; 

// ডাটাবেসে বিল সেভ করার লজিক (AJAX request handle করা)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data) {
        $total = $data['total'];
        $items = $data['items'];
        
        // ১. sales টেবিলে হিসাব রাখা
        $q1 = mysqli_query($conn, "INSERT INTO sales (total_price, sale_date) VALUES ('$total', NOW())");
        
        if ($q1) {
            // ২. products টেবিল থেকে স্টক কমানো
            foreach ($items as $item) {
                $name = mysqli_real_escape_string($conn, $item['name']);
                mysqli_query($conn, "UPDATE products SET stock = stock - 1 WHERE p_name = '$name'");
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siam POS - Full</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        @media print { .no-print { display: none; } #receipt { width: 100% !important; margin: 0; box-shadow: none; border: none; } }
        #reader { width: 100%; border-radius: 12px; display: none; margin-bottom: 10px; border: 2px solid #3b82f6; }
    </style>
</head>
<body class="bg-gray-100 p-2 pb-20">

    <div class="max-w-md mx-auto no-print">
        <div class="flex justify-between items-center mb-4 bg-white p-3 rounded-xl shadow-sm">
            <button onclick="window.location.href='index.php'" class="text-blue-600 font-bold text-sm">← Back</button>
            <h2 class="font-black text-gray-700 uppercase tracking-tighter">Billing</h2>
            <button onclick="toggleScanner()" id="cam-btn" class="bg-blue-600 text-white px-3 py-1 rounded-lg text-xs font-bold shadow">📷 SCAN</button>
        </div>

        <div id="reader"></div>

        <div class="bg-white p-4 rounded-xl shadow-md border-b-4 border-blue-600 mb-4">
            <div class="space-y-2">
                <input type="text" id="m_name" placeholder="Name or Barcode" class="w-full p-3 bg-gray-50 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <div class="flex gap-2">
                    <input type="number" id="m_price" placeholder="Price (Manual)" class="flex-1 p-3 bg-gray-50 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button onclick="handleManualAdd()" class="bg-blue-600 text-white px-6 rounded-lg font-bold uppercase text-xs">Add</button>
                </div>
            </div>
        </div>
    </div>

    <div id="receipt" class="max-w-md mx-auto bg-white p-6 shadow-lg rounded-xl">
        <div class="text-center mb-4">
            <h1 class="font-black text-2xl uppercase italic tracking-tighter">SIAM STORE</h1>
            <p class="text-[10px] text-gray-400">Official Billing System</p>
        </div>
        
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-gray-400 text-left text-[10px]">
                    <th class="py-2">ITEM</th>
                    <th class="text-center">QTY</th>
                    <th class="text-right">PRICE</th>
                    <th class="text-right no-print">DEL</th>
                </tr>
            </thead>
            <tbody id="bill-items"></tbody>
        </table>

        <div class="mt-6 pt-4 border-t-2 border-dashed flex justify-between items-center font-black">
            <span class="text-gray-500 text-[10px]">TOTAL AMOUNT:</span>
            <span class="text-xl text-blue-700 tracking-tight">৳ <span id="total-val">0.00</span></span>
        </div>
    </div>

    <div class="max-w-md mx-auto no-print mt-6 space-y-3">
        <div class="flex gap-2">
            <button onclick="window.print()" class="flex-1 bg-blue-600 text-white py-4 rounded-xl font-bold uppercase text-xs">Print Bill</button>
            <button onclick="saveBill()" class="flex-1 bg-green-600 text-white py-4 rounded-xl font-bold uppercase text-xs shadow-md">Just Save</button>
        </div>
        <button onclick="location.reload()" class="w-full bg-gray-400 text-white py-3 rounded-xl font-bold uppercase text-[10px]">Next Customer</button>
    </div>

    <script>
        let total = 0;
        let scannerOn = false;
        const html5QrCode = new Html5Qrcode("reader");

        function handleManualAdd() {
            const nameInput = document.getElementById('m_name');
            const priceInput = document.getElementById('m_price');
            if (nameInput.value.trim() === "") return;

            if (priceInput.value !== "") {
                addItem(nameInput.value, priceInput.value);
                nameInput.value = ''; priceInput.value = '';
            } else {
                fetchProduct(nameInput.value);
                nameInput.value = '';
            }
        }

        function addItem(name, price) {
            const list = document.getElementById('bill-items');
            const rowId = 'row-' + Math.random().toString(36).substr(2, 9);
            const tr = document.createElement('tr');
            tr.id = rowId;
            tr.className = "border-b border-gray-50";
            tr.innerHTML = `
                <td class="py-3 uppercase text-[11px] font-semibold text-gray-700">${name}</td>
                <td class="text-center text-gray-500">1</td>
                <td class="text-right font-black text-gray-800">৳${parseFloat(price).toFixed(2)}</td>
                <td class="text-right no-print"><button onclick="removeItem('${rowId}', ${price})" class="text-red-500 font-bold ml-2">×</button></td>
            `;
            list.appendChild(tr);
            total += parseFloat(price);
            document.getElementById('total-val').innerText = total.toFixed(2);
        }

        function removeItem(id, price) {
            document.getElementById(id).remove();
            total -= price;
            document.getElementById('total-val').innerText = Math.abs(total).toFixed(2);
        }

        function fetchProduct(q) {
            fetch(`get_product.php?barcode=${q}`)
            .then(res => res.json())
            .then(data => {
                if(data.success) addItem(data.name, data.price);
                else alert("Product not found! Enter price manually.");
            });
        }

        function saveBill() {
            if (total <= 0) return alert("Add items first!");
            const items = [];
            document.querySelectorAll('#bill-items tr').forEach(row => {
                items.push({ name: row.cells[0].innerText });
            });

            fetch('billing.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ total: total, items: items })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Sale Recorded & Stock Updated!');
                    location.reload();
                }
            });
        }

        function toggleScanner() {
            const r = document.getElementById('reader');
            if(!scannerOn) {
                r.style.display = 'block';
                html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, (text) => {
                    fetchProduct(text); toggleScanner();
                });
                scannerOn = true;
            } else {
                html5QrCode.stop().then(() => { r.style.display = 'none'; scannerOn = false; });
            }
        }
    </script>
</body>
</html>
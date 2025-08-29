<!DOCTYPE html>
<html lang="id">
    <head>
        <title>Penjualan Telur</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    </head>
    <body class="bg-gray-100 p-8">
        <div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-6">📝 Input Penjualan</h1>
    
            @if(session('success'))
                <div class="bg-green-200 text-green-800 px-4 py-2 rounded mb-3">
                    {{ session('success') }}
                </div>
            @endif
    <style>
        /* tombol produk & mode */
        .choice-btn {
            border: 2px solid #007bff;
            border-radius: 12px;
            padding: 12px 20px;
            min-width: 120px;
            text-align: center;
            cursor: pointer;
            background: white;
            color: #007bff;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }

        .choice-btn:hover {
            background: #007bff;
            color: white;
            transform: scale(1.05);
        }

        .choice-btn.selected {
            background: #007bff;
            color: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .choice-list {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 15px;
        }

        /* counter jumlah */
        .counter {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .counter button {
            border: 2px solid #28a745;
            background: white;
            color: #28a745;
            font-weight: bold;
            font-size: 18px;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .counter button:hover {
            background: #28a745;
            color: white;
        }

        .counter input {
            width: 80px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        /* kolom harga & total */
        .price-box {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 10px 15px;
            margin-bottom: 15px;
        }

        .price-box label {
            font-weight: bold;
            color: #333;
        }

        .price-box span {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }

        /* tombol simpan */
        .btn-save {
            border: 2px solid #28a745;
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            font-weight: bold;
            font-size: 18px;
            padding: 12px 25px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-save:hover {
            background: linear-gradient(45deg, #20c997, #28a745);
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
    </style>
    <form action="{{ route('penjualan.store') }}" method="POST">
        @csrf

        <label class="form-label">Pilih Produk</label>
        <div class="choice-list">
            @foreach($produks as $p)
                <button type="button" 
                    class="produk-btn px-4 py-2 border rounded-lg hover:bg-blue-100"
                    data-id="{{ $p->id }}"
                    data-harga-kg="{{ $p->harga_per_kg }}"
                    data-harga-peti="{{ $p->harga_per_peti }}">
                    {{ $p->nama_produk }}
                </button>
            @endforeach
        </div>
        <input type="hidden" name="produk_id" id="produk_id">

        <!-- Mode -->
        <h3 class="mb-2 font-semibold">Mode Penjualan</h3>
        <div class="flex gap-2 mb-4">
            <button type="button" class="mode-btn px-4 py-2 border rounded-lg hover:bg-blue-100" data-mode="kg">Per Kg</button>
            <button type="button" class="mode-btn px-4 py-2 border rounded-lg hover:bg-blue-100" data-mode="peti">Per Peti</button>
        </div>
        <input type="hidden" name="mode" id="mode">

        <!-- Jumlah -->
        <h3 class="mb-2 font-semibold">Jumlah</h3>
        <div class="flex items-center gap-2 mb-4">
            <button type="button" id="decrement" class="px-3 py-1 border rounded">-</button>
            <span id="jumlahDisplay">1</span>
            <button type="button" id="increment" class="px-3 py-1 border rounded">+</button>
        </div>
        <input type="hidden" name="jumlah" id="jumlah" value="1">

        <!-- Harga -->
        <div class="mb-2">
            <label class="font-semibold">Harga Satuan:</label>
            <p id="hargaDisplay" class="text-blue-600">Rp 0</p>
            <input type="hidden" name="harga_satuan" id="harga_satuan">
        </div>

        <!-- Total -->
        <div class="mb-4">
            <label class="font-semibold">Total Pembelian:</label>
            <p id="totalDisplay" class="text-green-600 font-bold">Rp 0</p>
            <input type="hidden" name="total" id="total">
        </div>

        <button type="submit" class="w-full py-2 border rounded-lg bg-blue-500 text-white hover:bg-blue-600">Simpan</button>
    </form>

    <script>
        let selectedProduk = null;
        let selectedMode = null;
        let jumlah = 1;

        const produkButtons = document.querySelectorAll('.produk-btn');
        const modeButtons = document.querySelectorAll('.mode-btn');
        const jumlahDisplay = document.getElementById('jumlahDisplay');
        const hargaDisplay = document.getElementById('hargaDisplay');
        const totalDisplay = document.getElementById('totalDisplay');

        const produkIdInput = document.getElementById('produk_id');
        const modeInput = document.getElementById('mode');
        const jumlahInput = document.getElementById('jumlah');
        const hargaInput = document.getElementById('harga_satuan');
        const totalInput = document.getElementById('total');

        // pilih produk
        produkButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                produkButtons.forEach(b => b.classList.remove('bg-blue-500','text-white'));
                this.classList.add('bg-blue-500','text-white');

                selectedProduk = {
                    id: this.dataset.id,
                    hargaKg: this.dataset.hargaKg,
                    hargaPeti: this.dataset.hargaPeti
                };
                produkIdInput.value = selectedProduk.id;
                updateHarga();
            });
        });

        // pilih mode
        modeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                modeButtons.forEach(b => b.classList.remove('bg-blue-500','text-white'));
                this.classList.add('bg-blue-500','text-white');

                selectedMode = this.dataset.mode;
                modeInput.value = selectedMode;
                updateHarga();
            });
        });

        // jumlah
        document.getElementById('increment').addEventListener('click', () => {
            jumlah++;
            jumlahDisplay.textContent = jumlah;
            jumlahInput.value = jumlah;
            updateHarga();
        });

        document.getElementById('decrement').addEventListener('click', () => {
            if(jumlah > 1) {
                jumlah--;
                jumlahDisplay.textContent = jumlah;
                jumlahInput.value = jumlah;
                updateHarga();
            }
        });

        // fungsi update harga & total
        function updateHarga() {
            if (!selectedProduk || !selectedMode) return;

            let harga = (selectedMode === 'kg') ? selectedProduk.hargaKg : selectedProduk.hargaPeti;
            let total = harga * jumlah;

            hargaDisplay.textContent = "Rp " + harga.toLocaleString();
            totalDisplay.textContent = "Rp " + total.toLocaleString();

            hargaInput.value = harga;
            totalInput.value = total;
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Penjualan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6 bg-gray-100">
    <style>
        /* tombol produk & mode */
        .choice-btn {
            border: 2px solid #007bff;
            border-radius: 12px;
            padding: 12px 20px;
            min-width: 120px;
            text-align: center;
            cursor: pointer;
            background: white;
            color: #007bff;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }

        .choice-btn:hover {
            background: #007bff;
            color: white;
            transform: scale(1.05);
        }

        .choice-btn.selected {
            background: #007bff;
            color: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .choice-list {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 15px;
        }

        /* counter jumlah */
        .counter {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .counter button {
            border: 2px solid #28a745;
            background: white;
            color: #28a745;
            font-weight: bold;
            font-size: 18px;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .counter button:hover {
            background: #28a745;
            color: white;
        }

        .counter input {
            width: 80px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        /* kolom harga & total */
        .price-box {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 10px 15px;
            margin-bottom: 15px;
        }

        .price-box label {
            font-weight: bold;
            color: #333;
        }

        .price-box span {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }

        /* tombol simpan */
        .btn-save {
            border: 2px solid #28a745;
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            font-weight: bold;
            font-size: 18px;
            padding: 12px 25px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-save:hover {
            background: linear-gradient(45deg, #20c997, #28a745);
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
    </style>
    <form action="{{ route('penjualan.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md max-w-md mx-auto">
        @csrf

        <!-- Produk -->
        <h3 class="mb-2 font-semibold">Pilih Produk</h3>
        <div class="flex gap-2 mb-4">
            @foreach($produks as $p)
                <button type="button" 
                    class="produk-btn px-4 py-2 border rounded-lg hover:bg-blue-100"
                    data-id="{{ $p->id }}"
                    data-harga-kg="{{ $p->harga_per_kg }}"
                    data-harga-peti="{{ $p->harga_per_peti }}">
                    {{ $p->nama_produk }}
                </button>
            @endforeach
        </div>
        <input type="hidden" name="produk_id" id="produk_id">

        <!-- Mode -->
        <h3 class="mb-2 font-semibold">Mode Penjualan</h3>
        <div class="flex gap-2 mb-4">
            <button type="button" class="mode-btn px-4 py-2 border rounded-lg hover:bg-blue-100" data-mode="kg">Per Kg</button>
            <button type="button" class="mode-btn px-4 py-2 border rounded-lg hover:bg-blue-100" data-mode="peti">Per Peti</button>
        </div>
        <input type="hidden" name="mode" id="mode">

        <!-- Jumlah -->
        <h3 class="mb-2 font-semibold">Jumlah</h3>
        <div class="flex items-center gap-2 mb-4">
            <button type="button" id="decrement" class="px-3 py-1 border rounded">-</button>
            <span id="jumlahDisplay">1</span>
            <button type="button" id="increment" class="px-3 py-1 border rounded">+</button>
        </div>
        <input type="hidden" name="jumlah" id="jumlah" value="1">

        <!-- Harga -->
        <div class="mb-2">
            <label class="font-semibold">Harga Satuan:</label>
            <p id="hargaDisplay" class="text-blue-600">Rp 0</p>
            <input type="hidden" name="harga_satuan" id="harga_satuan">
        </div>

        <!-- Total -->
        <div class="mb-4">
            <label class="font-semibold">Total Pembelian:</label>
            <p id="totalDisplay" class="text-green-600 font-bold">Rp 0</p>
            <input type="hidden" name="total" id="total">
        </div>

        <button type="submit" class="w-full py-2 border rounded-lg bg-blue-500 text-white hover:bg-blue-600">Simpan</button>
    </form>

    <script>
        let selectedProduk = null;
        let selectedMode = null;
        let jumlah = 1;

        const produkButtons = document.querySelectorAll('.produk-btn');
        const modeButtons = document.querySelectorAll('.mode-btn');
        const jumlahDisplay = document.getElementById('jumlahDisplay');
        const hargaDisplay = document.getElementById('hargaDisplay');
        const totalDisplay = document.getElementById('totalDisplay');

        const produkIdInput = document.getElementById('produk_id');
        const modeInput = document.getElementById('mode');
        const jumlahInput = document.getElementById('jumlah');
        const hargaInput = document.getElementById('harga_satuan');
        const totalInput = document.getElementById('total');

        // pilih produk
        produkButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                produkButtons.forEach(b => b.classList.remove('bg-blue-500','text-white'));
                this.classList.add('bg-blue-500','text-white');

                selectedProduk = {
                    id: this.dataset.id,
                    hargaKg: this.dataset.hargaKg,
                    hargaPeti: this.dataset.hargaPeti
                };
                produkIdInput.value = selectedProduk.id;
                updateHarga();
            });
        });

        // pilih mode
        modeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                modeButtons.forEach(b => b.classList.remove('bg-blue-500','text-white'));
                this.classList.add('bg-blue-500','text-white');

                selectedMode = this.dataset.mode;
                modeInput.value = selectedMode;
                updateHarga();
            });
        });

        // jumlah
        document.getElementById('increment').addEventListener('click', () => {
            jumlah++;
            jumlahDisplay.textContent = jumlah;
            jumlahInput.value = jumlah;
            updateHarga();
        });

        document.getElementById('decrement').addEventListener('click', () => {
            if(jumlah > 1) {
                jumlah--;
                jumlahDisplay.textContent = jumlah;
                jumlahInput.value = jumlah;
                updateHarga();
            }
        });

        // fungsi update harga & total
        function updateHarga() {
            if (!selectedProduk || !selectedMode) return;

            let harga = (selectedMode === 'kg') ? selectedProduk.hargaKg : selectedProduk.hargaPeti;
            let total = harga * jumlah;

            hargaDisplay.textContent = "Rp " + harga.toLocaleString();
            totalDisplay.textContent = "Rp " + total.toLocaleString();

            hargaInput.value = harga;
            totalInput.value = total;
        }
    </script>
</body>
</html>
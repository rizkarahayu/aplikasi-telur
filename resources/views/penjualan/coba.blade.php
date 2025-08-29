<!DOCTYPE html>
<html>
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

    <!-- Pilih Produk -->
    <label class="form-label">Pilih Produk</label>
    <div class="choice-list">
        @foreach($produks as $produk)
            <div class="choice-btn produk-btn" 
                 data-id="{{ $produk->id }}" 
                 data-harga-kg="{{ $produk->harga_kg }}" 
                 data-harga-peti="{{ $produk->harga_peti }}">
                {{ $produk->nama_produk }}
            </div>
        @endforeach
    </div>
    
    <input type="hidden" name="produk_id" id="produk_id" required>

    <!-- Pilih Mode -->
    <label class="form-label">Mode Penjualan</label>
    <div class="choice-list">
        <div class="choice-btn mode-btn" data-mode="kg">Per Kg</div>
        <div class="choice-btn mode-btn" data-mode="peti">Per Peti</div>
    </div>
    <input type="hidden" name="mode" id="mode" required>

    <!-- Jumlah -->
    <label class="form-label">Jumlah</label>
    <div class="counter">
        <button type="button" id="minus">-</button>
        <input type="number" name="jumlah" id="jumlah" value="1" min="1" readonly>
        <button type="button" id="plus">+</button>
    </div>

    <!-- Harga Satuan -->
    <div class="price-box">
        <label>Harga Satuan: </label>
        <span id="harga_satuan_display">Rp 0</span>
        <input type="hidden" name="harga_satuan" id="harga_satuan">
    </div>

    <!-- Total -->
    <div class="price-box">
        <label>Total Pembelian: </label>
        <span id="total_display">Rp 0</span>
        <input type="hidden" name="total" id="total">
    </div>

    <button type="submit" class="btn-save">💾 Simpan Penjualan</button>
</form>

<script>
    let selectedHargaKg = 0;
    let selectedHargaPeti = 0;

    // Produk pilih
    document.querySelectorAll('.produk-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('produk_id').value = this.dataset.id;
            selectedHargaKg = this.dataset.hargaKg;
            selectedHargaPeti = this.dataset.hargaPeti;
            document.querySelectorAll('.produk-btn').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            updateHarga();
        });
    });

    // Mode pilih
    document.querySelectorAll('.mode-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('mode').value = this.dataset.mode;
            document.querySelectorAll('.mode-btn').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            updateHarga();
        });
    });

    // Counter jumlah
    let jumlahInput = document.getElementById('jumlah');
    document.getElementById('minus').addEventListener('click', () => {
        if (parseInt(jumlahInput.value) > 1) {
            jumlahInput.value = parseInt(jumlahInput.value) - 1;
            updateHarga();
        }
    });
    document.getElementById('plus').addEventListener('click', () => {
        jumlahInput.value = parseInt(jumlahInput.value) + 1;
        updateHarga();
    });

        function updateHarga() {
            document.querySelectorAll('.produk-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    console.log(this.dataset.hargaKg);   // <-- harusnya isi dari harga_kg
                    console.log(this.dataset.hargaPeti); // <-- harusnya isi dari harga_peti
                });
            });
            let mode = document.getElementById('mode').value;
            let jumlah = parseInt(jumlahInput.value) || 0;
            let harga = 0;

            if (mode === "kg") {
                harga = selectedHargaKg;
            } else if (mode === "peti") {
                harga = selectedHargaPeti;
            }
            

            let total = jumlah * harga;
            console.log(harga);
            console.log(total);

            document.getElementById('harga_satuan').value = harga;
            document.getElementById('total').value = total;

            document.getElementById('harga_satuan_display').innerText = "Rp " + harga.toLocaleString();
            document.getElementById('total_display').innerText = "Rp " + total.toLocaleString();
        }

    </script>   
    </div>

    <div class="max-w-4xl mx-auto mt-10">
        <h2 class="text-xl font-semibold mb-3">📊 Riwayat Penjualan</h2>
        <table class="table-auto w-full bg-white shadow rounded">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Produk</th>
                    <th class="px-4 py-2">Mode</th>
                    <th class="px-4 py-2">Jumlah</th>
                    <th class="px-4 py-2">Harga Satuan</th>
                    <th class="px-4 py-2">Total</th>
                    <th class="px-4 py-2">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualans as $penjualan)
                <tr>
                    <td class="border px-4 py-2">{{ $penjualan->id }}</td>
                    <td class="border px-4 py-2">{{ $penjualan->produk->nama_produk }}</td>
                    <td class="border px-4 py-2">{{ strtoupper($penjualan->mode) }}</td>
                    <td class="border px-4 py-2">{{ $penjualan->jumlah }}</td>
                    <td class="border px-4 py-2">Rp {{ number_format($penjualan->harga_satuan, 0, ',', '.') }}</td>
                    <td class="border px-4 py-2">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
                    <td class="border px-4 py-2">{{ $penjualan->created_at->format('d-m-Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
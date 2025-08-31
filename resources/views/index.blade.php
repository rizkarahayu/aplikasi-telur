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

        <form action="{{ route('penjualan.store') }}" method="POST">
            @csrf

            <!-- Produk -->
            <h3 class="mb-2 font-semibold">Pilih Produk</h3>
            <div class="flex gap-2 mb-4 flex-wrap">
                @foreach($produks as $p)
                    <button type="button" 
                        class="produk-btn px-4 py-2 border rounded-lg 
                               bg-white shadow-sm 
                               hover:bg-gradient-to-r hover:from-blue-400 hover:to-blue-600 
                               hover:text-black hover:shadow-md 
                               active:scale-95 active:shadow-inner
                               transition-all duration-300 ease-in-out"
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
            <div class="flex gap-2 mb-4 flex-wrap">
                <button type="button" 
                    class="mode-btn px-4 py-2 border rounded-lg 
                           bg-white shadow-sm 
                           hover:bg-gradient-to-r hover:from-green-400 hover:to-green-600 
                           hover:text-white hover:shadow-md 
                           active:scale-95 active:shadow-inner
                           transition-all duration-300 ease-in-out"
                    data-mode="kg">
                    Per Kg
                </button>
                <button type="button" 
                    class="mode-btn px-4 py-2 border rounded-lg 
                           bg-white shadow-sm 
                           hover:bg-gradient-to-r hover:from-yellow-400 hover:to-yellow-600 
                           hover:text-white hover:shadow-md 
                           active:scale-95 active:shadow-inner
                           transition-all duration-300 ease-in-out"
                    data-mode="peti">
                    Per Peti
                </button>
            </div>
            <input type="hidden" name="mode" id="mode">

            <!-- Jumlah -->
            <h3 class="mb-2 font-semibold">Jumlah</h3>
            <div class="flex items-center gap-2 mb-4">
                <button type="button" id="decrement" 
                    class="px-3 py-1 border rounded-lg 
                           bg-gray-100 shadow-sm 
                           hover:bg-gradient-to-r hover:from-red-400 hover:to-red-600 
                           hover:text-white hover:shadow-md 
                           active:scale-95 active:shadow-inner
                           transition-all duration-300 ease-in-out">-</button>
                <span id="jumlahDisplay" class="font-semibold">1</span>
                <button type="button" id="increment" 
                    class="px-3 py-1 border rounded-lg 
                           bg-gray-100 shadow-sm 
                           hover:bg-gradient-to-r hover:from-blue-400 hover:to-blue-600 
                           hover:text-white hover:shadow-md 
                           active:scale-95 active:shadow-inner
                           transition-all duration-300 ease-in-out">+</button>
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

            <!-- Tombol Simpan -->
            <button type="submit" 
                class="w-full py-2 rounded-lg 
                       bg-gradient-to-r from-blue-500 to-blue-700 
                       text-white font-semibold shadow-md 
                       hover:from-blue-600 hover:to-blue-800 
                       active:scale-95 active:shadow-inner
                       transition-all duration-300 ease-in-out">
                💾 Simpan
            </button>
        </form>

        <!-- Script -->
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

                hargaDisplay.textContent = "Rp " + parseInt(harga).toLocaleString();
                totalDisplay.textContent = "Rp " + parseInt(total).toLocaleString();

                hargaInput.value = harga;
                totalInput.value = total;
            }
        </script>
    </div>

    <!-- Riwayat -->
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

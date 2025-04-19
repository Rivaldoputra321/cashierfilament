<x-filament::page>
    <div>
        <input type="text" wire:model.defer="productCode" wire:keydown.enter="addProduct" placeholder="Scan / Masukkan Kode Produk">
        <button wire:click="addProduct">➤</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th><th>Kode</th><th>Nama</th><th>Harga</th><th>Jumlah</th><th>Diskon</th><th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cart as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item['code'] }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td>Rp. {{ number_format($item['price']) }}</td>
                    <td>{{ $item['qty'] }}</td>
                    <td>{{ $item['diskon'] }}</td>
                    <td>Rp. {{ number_format($item['subtotal']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div>
        <h2>Bayar: Rp. {{ number_format($bayar) }}</h2>
    </div>

    <div>
        <label>Kode Member</label>
        <input type="text" wire:model.defer="memberCode" wire:keydown.enter="scanMember">
        <button wire:click="scanMember">➤</button>
        @if($member)
            <p>{{ $member->name }}</p>
        @endif

        <label>Diskon</label>
        <input type="number" wire:model="diskon" wire:input="calculateTotal">

        <label>Diterima</label>
        <input type="number" wire:model="diterima" wire:input="calculateKembali">

        <label>Kembali</label>
        <input type="text" readonly value="Rp. {{ number_format($kembali) }}">
    </div>

    <button wire:click="saveTransaction">💾 Simpan Transaksi</button>
</x-filament::page>

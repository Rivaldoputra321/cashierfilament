<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Left Panel - Product Input and Cart -->
        <div class="md:col-span-2 space-y-4">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex space-x-2">
                    <div class="flex-grow">
                        <label for="productCode" class="block text-sm font-medium text-gray-700">Kode Produk</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" wire:model="productCode" id="productCode" 
                                class="focus:ring-primary-500 focus:border-primary-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300"
                                placeholder="Masukkan kode produk">
                        </div>
                    </div>
                    <div class="flex items-end">
                        <button type="button" wire:click="searchProduct"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex items-end">
                        <button type="button" wire:click="showProductModal" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Table Modal -->
            @if($showProductTable)
            <div class="fixed inset-0 z-30 flex items-center justify-center overflow-auto bg-black bg-opacity-50">
                <div class="bg-white rounded-lg shadow p-4 max-w-3xl w-full">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Pilih Produk</h3>
                        <button wire:click="$set('showProductTable', false)" class="text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    <div class="mb-4">
                        <input type="text" wire:model.debounce.300ms="productSearch" class="focus:ring-primary-500 focus:border-primary-500 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Cari produk...">
                    </div>
                    <div class="overflow-x-auto max-h-64 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($productList as $product)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->kd_product }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp. {{ number_format($product->harga, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->stok }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="selectProduct('{{ $product->kd_product }}')" class="text-primary-600 hover:text-primary-900">Pilih</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Tidak ada produk ditemukan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diskon</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($cartItems as $index => $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['code'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $item['name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp. {{ number_format($item['price'], 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center space-x-2">
                                        <button type="button" wire:click="updateQuantity({{ $index }}, 'decrease')"
                                            class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <span>{{ $item['quantity'] }}</span>
                                        <button type="button" wire:click="updateQuantity({{ $index }}, 'increase')"
                                            class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp. {{ number_format($item['discount_value'] * $item['quantity'], 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">Rp. {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button type="button" wire:click="removeItem({{ $index }})"
                                        class="text-red-600 hover:text-red-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Keranjang kosong</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-blue-600 rounded-lg shadow p-6">
                <div class="text-center text-black text-3xl font-bold">
                    Bayar: Rp. {{ number_format($total, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- Right Panel - Payment Info -->
        <div class="space-y-4">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="space-y-4">
                    <div>
                        <label for="totalAmount" class="block text-sm font-medium text-gray-700">Total</label>
                        <div class="mt-1">
                            <input type="text" id="totalAmount" disabled value="Rp. {{ number_format($total, 0, ',', '.') }}"
                                class="bg-gray-100 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md text-gray-900 font-medium">
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center">
                            <label for="memberCode" class="block text-sm font-medium text-gray-700">Member</label>
                            <button type="button" wire:click="showMemberModal" 
                                class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Lihat Member
                            </button>
                        </div>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" wire:model="memberCode" id="memberCode"
                                class="focus:ring-primary-500 focus:border-primary-500 flex-1 block w-full rounded-l-md sm:text-sm border-gray-300"
                                placeholder="ID Member">
                            <button type="button" wire:click="searchMember"
                                class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 rounded-r-md hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        @if($selectedMember)
                            <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded-md">
                                <p class="text-sm text-green-800 font-medium">{{ $selectedMember->name }}</p>
                                <p class="text-xs text-green-600">Tier: {{ $selectedMember->tier }} | Points: {{ $selectedMember->points }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Member Table Modal -->
                    @if($showMemberTable)
                    <div class="fixed inset-0 z-30 flex items-center justify-center overflow-auto bg-black bg-opacity-50">
                        <div class="bg-white rounded-lg shadow p-4 max-w-3xl w-full">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Pilih Member</h3>
                                <button wire:click="$set('showMemberTable', false)" class="text-gray-500 hover:text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div class="mb-4">
                                <input type="text" wire:model.debounce.300ms="memberSearch" class="focus:ring-primary-500 focus:border-primary-500 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Cari member...">
                            </div>
                            <div class="overflow-x-auto max-h-64 overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tier</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($memberList as $member)
                                        <tr>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $member->id }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $member->name }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $member->tier }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $member->points }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button wire:click="selectMember('{{ $member->id }}')" class="text-primary-600 hover:text-primary-900">Pilih</button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Tidak ada member ditemukan</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div>
                        <label for="discount" class="block text-sm font-medium text-gray-700">Diskon</label>
                        <div class="mt-1">
                            <input type="text" id="discount" disabled value="Rp. {{ number_format($discount, 0, ',', '.') }}"
                                class="bg-gray-100 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md text-gray-900 font-medium">
                        </div>
                    </div>

                    <div>
                        <label for="paymentAmount" class="block text-sm font-medium text-gray-700">Diterima</label>
                        <div class="mt-1">
                            <input type="number" wire:model.lazy="paymentAmount" id="paymentAmount" wire:change="calculateChange"
                                class="focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md text-gray-900">
                        </div>
                    </div>

                    <div>
                        <label for="kembalian" class="block text-sm font-medium text-gray-700">Kembali</label>
                        <div class="mt-1">
                            <input type="text" id="kembalian" disabled value="Rp. {{ number_format($changeAmount, 0, ',', '.') }}"
                                class="bg-gray-100 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md text-gray-900 font-medium">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <button type="button" wire:click="saveTransaction"
                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Simpan Transaksi
                </button>

                <!-- Add this somewhere in your form UI -->

            </div>
        </div>
    </div>
</x-filament-panels::page>
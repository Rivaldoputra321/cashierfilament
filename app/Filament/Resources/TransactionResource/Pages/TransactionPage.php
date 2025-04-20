<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use Exception;
use Filament\Forms;
use App\Models\sale;
use App\Models\member;
use App\Models\product;
use Livewire\Component;
use Filament\Forms\Form;
use App\Models\DetailSale;
use Livewire\Attributes\On;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Filament\Resources\TransactionResource;

class TransactionPage extends Page
{
    protected static string $resource = TransactionResource::class;

    protected static string $view = 'filament.resources.transaction-resource.pages.transaction-page';

    public $productCode = '';
    public $memberCode = '';
    public $cartItems = [];
    public $total = 0;
    public $discount = 0;
    public $paymentAmount = 0;
    public $changeAmount = 0;
    public $selectedMember = null;
    
    // Add new properties for modal tables
    public $showProductTable = false;
    public $showMemberTable = false;
    public $productSearch = '';
    public $memberSearch = '';
    public $productList = [];
    public $memberList = [];

    public function mount()
    {
        $this->cartItems = session()->get('cart_items', []);
        $this->calculateTotal();
    }
    
    // Method to handle product search in modal
    public function updatedProductSearch()
    {
        $this->loadProducts();
    }
    
    // Method to handle member search in modal
    public function updatedMemberSearch()
    {
        $this->loadMembers();
    }
    
    // Method to load products for the modal table
    public function loadProducts()
    {
        if (empty($this->productSearch)) {
            $this->productList = product::where('stok', '>', 0)
                ->orderBy('name')
                ->limit(10)
                ->get();
        } else {
            $this->productList = product::where('stok', '>', 0)
                ->where(function($query) {
                    $query->where('name', 'like', '%' . $this->productSearch . '%')
                        ->orWhere('kd_product', 'like', '%' . $this->productSearch . '%');
                })
                ->orderBy('name')
                ->limit(25)
                ->get();
        }
    }
    
    // Method to load members for the modal table
    public function loadMembers()
    {
        if (empty($this->memberSearch)) {
            $this->memberList = member::orderBy('name')
                ->limit(10)
                ->get();
        } else {
            $this->memberList = member::where('name', 'like', '%' . $this->memberSearch . '%')
                ->orWhere('id', 'like', '%' . $this->memberSearch . '%')
                ->orderBy('name')
                ->limit(25)
                ->get();
        }
    }
    
    // Method to handle showing the product table
    public function showProductModal()
    {
        $this->showProductTable = true;
        $this->loadProducts();
    }
    
    // Method to handle showing the member table
    public function showMemberModal()
    {
        $this->showMemberTable = true;
        $this->loadMembers();
    }
    
    // Method to select a product from the modal table
    public function selectProduct($productCode)
    {
        $this->productCode = $productCode;
        $this->showProductTable = false;
        $this->searchProduct();
    }
    
    // Method to select a member from the modal table
    public function selectMember($memberId)
    {
        $this->memberCode = $memberId;
        $this->showMemberTable = false;
        $this->searchMember();
    }

    public function calculateTotal()
    {
        $this->total = 0;
        $this->discount = 0;
        
        foreach ($this->cartItems as $item) {
            $this->total += $item['subtotal'];
            // Sum up all discount values
            $this->discount += $item['discount_value'] * $item['quantity'];
        }
        
        $this->calculateChange();
    }
    
    public function calculateChange()
    {
        $this->changeAmount = $this->paymentAmount - $this->total;
        if ($this->changeAmount < 0) {
            $this->changeAmount = 0;
        }
    }

    public function searchProduct()
    {
        if (empty($this->productCode)) {
            return;
        }

        $product = product::where('kd_product', $this->productCode)->first();
        
        if (!$product) {
            Notification::make()
                ->title('Produk tidak ditemukan')
                ->danger()
                ->send();
            return;
        }

        if ($product->stok <= 0) {
            Notification::make()
                ->title('Stok produk habis')
                ->danger()
                ->send();
            return;
        }

        // Cari produk dalam keranjang
        $productIndex = -1;
        foreach ($this->cartItems as $index => $item) {
            if ($item['id'] == $product->id) {
                $productIndex = $index;
                break;
            }
        }
        
        // Jika produk sudah ada di keranjang, tambah jumlahnya
        if ($productIndex >= 0) {
            $this->cartItems[$productIndex]['quantity']++;
            
            // Recalculate subtotal (akan diperbaiki diskon nanti)
            $this->cartItems[$productIndex]['subtotal'] = $this->cartItems[$productIndex]['quantity'] * 
                ($this->cartItems[$productIndex]['price'] - $this->cartItems[$productIndex]['discount_value']);
        } else {
            // Jika produk baru, tambahkan ke keranjang dengan diskon 0 (akan diupdate)
            $this->cartItems[] = [
                'id' => $product->id,
                'code' => $product->kd_product,
                'name' => $product->name,
                'price' => $product->harga,
                'discount_value' => 0,
                'discount_id' => null,
                'quantity' => 1,
                'subtotal' => $product->harga
            ];
        }
        
        // Simpan ke session dulu
        session()->put('cart_items', $this->cartItems);
        
        // Panggil method untuk menghitung diskon untuk semua item
        $this->calculateAllDiscounts();
        
        $this->productCode = '';
        $this->calculateTotal();
        
        Notification::make()
            ->title('Produk ditambahkan ke keranjang')
            ->success()
            ->send();
    }

    // Method untuk menghitung diskon untuk semua item
    public function calculateAllDiscounts()
    {
        $now = now();
        
        foreach ($this->cartItems as $index => $item) {
            $product = product::find($item['id']);
            if (!$product) continue;
            
            // Reset discount for this item
            $discountValue = 0;
            $discountId = null;
            $discountPercentage = 0;
            $bestDiscount = null;
            $highestPriority = 0; // 2 = product, 1 = category
            
            // STEP 1: Check product discount (highest priority)
            $productDiscount = DB::table('discount_products')
                ->join('discounts', 'discounts.id', '=', 'discount_products.discount_id')
                ->where('discount_products.product_id', $product->id)
                ->where('discounts.type', 'product')
                ->where('discounts.start_date', '<=', $now)
                ->where('discounts.end_date', '>=', $now)
                ->first();
            
            if ($productDiscount) {
                $isApplicable = true;
                
                // Check member-only discount
                if ($productDiscount->is_member_only == 1) {
                    if (!$this->selectedMember) {
                        $isApplicable = false;
                    } else {
                        // Parse member tiers properly
                        $memberTiersStr = $productDiscount->member_tiers;
                        $memberTiers = [];
                        
                        if (!empty($memberTiersStr)) {
                            // Handle both string and array formats
                            if (is_string($memberTiersStr)) {
                                $memberTiers = array_map('trim', array_map('strtolower', explode(',', $memberTiersStr)));
                            } else if (is_array($memberTiersStr)) {
                                $memberTiers = array_map('strtolower', $memberTiersStr);
                            }
                            
                            $userTier = strtolower(trim($this->selectedMember->tier));
                            
                            if (!in_array($userTier, $memberTiers)) {
                                $isApplicable = false;
                            }
                        }
                    }
                }
                
                // Check minimum quantity
                if ($isApplicable && $item['quantity'] >= ($productDiscount->min_quantity ?? 1)) {
                    $bestDiscount = $productDiscount;
                    $highestPriority = 2; // Product discount has highest priority
                }
            }
            
            // STEP 2: Check category discount (lower priority)
            if ($highestPriority < 2) { // Only check if no product discount was applied
                $categoryDiscount = DB::table('category_products')
                    ->join('discount_categories', 'category_products.category_id', '=', 'discount_categories.category_id')
                    ->join('discounts', 'discounts.id', '=', 'discount_categories.discount_id')
                    ->where('category_products.product_id', $product->id)
                    ->where('discounts.type', 'category')
                    ->where('discounts.start_date', '<=', $now)
                    ->where('discounts.end_date', '>=', $now)
                    ->first();
                
                if ($categoryDiscount) {
                    $isApplicable = true;
                    
                    // Check member-only discount
                    if ($categoryDiscount->is_member_only == 1) {
                        if (!$this->selectedMember) {
                            $isApplicable = false;
                        } else {
                            // Parse member tiers properly
                            $memberTiersStr = $categoryDiscount->member_tiers;
                            $memberTiers = [];
                            
                            if (!empty($memberTiersStr)) {
                                // Handle both string and array formats
                                if (is_string($memberTiersStr)) {
                                    $memberTiers = array_map('trim', array_map('strtolower', explode(',', $memberTiersStr)));
                                } else if (is_array($memberTiersStr)) {
                                    $memberTiers = array_map('strtolower', $memberTiersStr);
                                }
                                
                                $userTier = strtolower(trim($this->selectedMember->tier));
                                
                                if (!in_array($userTier, $memberTiers)) {
                                    $isApplicable = false;
                                }
                            }
                        }
                    }
                    
                    // Check minimum quantity
                    if ($isApplicable && $item['quantity'] >= ($categoryDiscount->min_quantity ?? 1)) {
                        $bestDiscount = $categoryDiscount;
                        $highestPriority = 1; // Category discount has lower priority
                    }
                }
            }
            
            // STEP 3: Apply the selected discount
            if ($bestDiscount) {
                $discountPercentage = $bestDiscount->discount_percentage;
                $discountId = $bestDiscount->discount_id; // Fixed: Access the correct field
            }
    
            // STEP 4: Calculate discount value per unit
            if ($discountPercentage > 0) {
                $discountValue = ($discountPercentage / 100) * $product->harga;
            }
    
            // Save the discount value per unit (not total)
            $this->cartItems[$index]['discount_value'] = $discountValue;
            $this->cartItems[$index]['discount_id'] = $discountId;
            
            // Recalculate subtotal with discount
            $finalPrice = $product->harga - $discountValue;
            $this->cartItems[$index]['subtotal'] = $item['quantity'] * $finalPrice;
        }
        
        // Save changes to session
        session()->put('cart_items', $this->cartItems);
    }
    
    public function searchMember()
    {
        if (empty($this->memberCode)) {
            Notification::make()
                ->title('Kode member kosong')
                ->danger()
                ->send();
            return;
        }

        $member = member::find($this->memberCode);

        if (!$member) {
            Notification::make()
                ->title('Member tidak ditemukan')
                ->danger()
                ->send();
            return;
        }

        $this->selectedMember = $member;

        Notification::make()
            ->title("Member '{$member->name}' dipilih")
            ->success()
            ->send();
    }

    
    public function updateQuantity($index, $action)
    {
        if ($action === 'increase') {
            $this->cartItems[$index]['quantity']++;
        } else {
            if ($this->cartItems[$index]['quantity'] > 1) {
                $this->cartItems[$index]['quantity']--;
            } else {
                $this->removeItem($index);
                return;
            }
        }
        
        // Simpan perubahan quantity
        session()->put('cart_items', $this->cartItems);
        
        // Hitung ulang diskon (karena quantity berubah, bisa mempengaruhi min_quantity)
        $this->calculateAllDiscounts();
        $this->calculateTotal();
    }
    
    public function removeItem($index)
    {
        array_splice($this->cartItems, $index, 1);
        session()->put('cart_items', $this->cartItems);
        $this->calculateTotal();
    }
    
    public function saveTransaction()
    {
        if (empty($this->cartItems)) {
            Notification::make()
                ->title('Keranjang kosong')
                ->danger()
                ->send();
            return;
        }
        
        if ($this->paymentAmount < $this->total) {
            Notification::make()
                ->title('Jumlah pembayaran kurang')
                ->danger()
                ->send();
            return;
        }
        
        try {
            DB::beginTransaction();
            
            // Calculate total discount from all item discounts
            $totalDiscount = 0;
            foreach ($this->cartItems as $item) {
                $totalDiscount += $item['discount_value'] * $item['quantity'];
            }
            
            // Create sale record
            $sale = new sale();
            $sale->user_id = auth()->id();
            $sale->member_id = $this->selectedMember ? $this->selectedMember->id : null;
            $sale->total_amount = $this->total;
            $sale->discount_total = $totalDiscount; // Set the total discount
            $sale->paid_amount = $this->paymentAmount;
            $sale->change_amount = $this->changeAmount;
            $sale->earned_points = floor($this->total / 10000); // For example, 1 point for every 10,000
            $sale->save();
            
            // Create sale details
            foreach ($this->cartItems as $item) {
                $product = product::find($item['id']);
                
                if ($product->stok < $item['quantity']) {
                    throw new Halt('Stok produk ' . $product->name . ' tidak mencukupi');
                }
                
                $detailSale = new DetailSale();
                $detailSale->sales_id = $sale->id;
                $detailSale->product_id = $item['id'];
                $detailSale->quantity = $item['quantity'];
                $detailSale->price = $item['price'];
                $detailSale->discount_id = $item['discount_id'];
                $detailSale->discount_value = $item['discount_value'] * $item['quantity']; // Total discount for this line item
                $detailSale->subtotal = $item['subtotal'];
                $detailSale->save();
                
                // Update product stok
                $product->stok -= $item['quantity'];
                $product->save();
            }
            
            // Update member points and last transaction date if applicable
            if ($this->selectedMember) {
                $this->selectedMember->points += $sale->earned_points;
                $this->selectedMember->last_transaction_date = now();
                $this->selectedMember->save();
            }
            
            DB::commit();
            
            // Clear cart
            $this->cartItems = [];
            session()->forget('cart_items');
            $this->productCode = '';
            $this->memberCode = '';
            $this->selectedMember = null;
            $this->total = 0;
            $this->discount = 0;
            $this->paymentAmount = 0;
            $this->changeAmount = 0;
            
            Notification::make()
                ->title('Transaksi berhasil disimpan')
                ->success()
                ->send();
                
        } catch (Halt $e) {
            DB::rollBack();
            Notification::make()
                ->title($e->getMessage())
                ->danger()
                ->send();
        } catch (Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
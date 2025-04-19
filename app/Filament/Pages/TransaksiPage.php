<?php
namespace App\Filament\Pages;
use Filament\Pages\Page;
use App\Models\Product;
use App\Models\Member;
use App\Models\Sale;
use App\Models\DetailSale;

class TransaksiPage extends Page
{
    protected static string $view = 'filament.pages.transaksi-penjualan';

    public $productCode = '';
    public $memberCode = '';
    public $member = null;
    public $cart = [];
    public $total = 0;
    public $diskon = 0;
    public $bayar = 0;
    public $diterima = 0;
    public $kembali = 0;

    public function addProduct()
    {
        $product = Product::where('code', $this->productCode)->first();

        if ($product) {
            $existing = collect($this->cart)->firstWhere('id', $product->id);

            if ($existing) {
                foreach ($this->cart as &$item) {
                    if ($item['id'] === $product->id) {
                        $item['qty'] += 1;
                        $item['subtotal'] = $item['qty'] * $item['price'];
                        break;
                    }
                }
            } else {
                $this->cart[] = [
                    'id' => $product->id,
                    'code' => $product->code,
                    'name' => $product->name,
                    'price' => $product->price,
                    'qty' => 1,
                    'diskon' => 0,
                    'subtotal' => $product->price,
                ];
            }

            $this->productCode = '';
            $this->calculateTotal();
        }
    }

    public function scanMember()
    {
        $this->member = Member::where('code', $this->memberCode)->first();
    }

    public function calculateTotal()
    {
        $this->total = collect($this->cart)->sum('subtotal');
        $this->bayar = $this->total - $this->diskon;
        $this->calculateKembali();
    }

    public function calculateKembali()
    {
        $this->kembali = $this->diterima - $this->bayar;
    }

    public function saveTransaction()
    {
        \DB::transaction(function () {
            $sale = Sale::create([
                'member_id' => $this->member?->id,
                'total_amount' => $this->total,
                'earned_points' => floor($this->total / 10000),
            ]);

            foreach ($this->cart as $item) {
                DetailSale::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                $product = Product::find($item['id']);
                $product->decrement('stock', $item['qty']);
            }

            if ($this->member) {
                $this->member->increment('current_points', $sale->earned_points);
            }
        });

        $this->reset([
            'productCode',
            'memberCode',
            'member',
            'cart',
            'total',
            'diskon',
            'bayar',
            'diterima',
            'kembali',
        ]);

        session()->flash('success', 'Transaksi berhasil disimpan!');
    }
}

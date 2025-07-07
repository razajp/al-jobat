<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\PaymentProgram;
use App\Models\PhysicalQuantity;
use App\Models\Shipment;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function getCategoryData(Request $request)
    {
        switch ($request->category) {
            case 'supplier':
                $suppliers = Supplier::whereHas('user', function ($query) {
                    $query->where('status', 'active');
                })->select('id', 'supplier_name')->get()->makeHidden('creator', 'categories');

                foreach ($suppliers as $supplier) {
                    $supplier['balance'] = 0;
                    $supplier['balance'] = number_format($supplier['balance'], 1, '.', ',');
                }

                return $suppliers;
                break;
            
            case 'customer':
                $customers = Customer::with('city:id,title')->whereHas('user', function ($query) {
                    $query->where('status', 'active');
                })->select('id', 'customer_name', 'city_id')->get()->makeHidden('creator');

                return $customers;
                break;
            
            case 'self_account':
                $selfAccount = BankAccount::with('subCategory', 'bank')->where('category', 'self')->get();
                return $selfAccount;
                break;
            
            default:
                return "Not Found";
                break;
        }
    }

    public function changeDataLayout(Request $request)
    {
        $previousRoute = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();

        $authUser = Auth::user();

        $layout = [];
    
        if (!empty($authUser->layout)) {
            // Parse the existing layout from JSON
            $layout = json_decode($authUser->layout, true);
        }

        $newLayout = $request->layout == 'grid' ? 'table' : 'grid';
    
        // Update the layout for the specified page
        $layout[$previousRoute] = $newLayout;
    
        // Save the updated layout back to the user
        $authUser->layout = json_encode($layout);

        $authUser->save();
    
        return response()->json([
            "status" => "updated",
            "updatedLayout" => $newLayout
        ]);
    }

    protected function getAuthLayout($routeName, $default = 'grid')
    {
        $layout = Auth::user()->layout ?? '';

        if (!empty($layout)) {
            $layout = json_decode($layout, true);
            return $layout[$routeName] ?? $default;
        }

        return $default;
    }
    
    protected function checkRole($roles)
    {
        if (!in_array(Auth::user()->role, $roles)) {
            return false;
        }
        
        return true;
    }

    public function getOrderDetails(Order $order, Request $request)
    {
        $validator = Validator::make($request->all(), [
            "order_no" => "required|exists:orders,order_no",
        ]);
        
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()->first()]);
        }

        $order = Order::with('customer.city')->where("order_no", $request->order_no)->first();
        $order->articles = json_decode($order->articles);

        if (!$request->boolean('only_order')) {
            $orderedArticles = $order->articles;

            $orderedArticles = array_filter($orderedArticles, function ($orderedArticle) {
                $article = Article::find($orderedArticle->id);
                $orderedArticle->article = $article;
            
                $totalPhysicalStockPackets = PhysicalQuantity::where("article_id", $article->id)->sum('packets');
                $orderedArticle->total_quantity_in_packets = 0;
                
                if ($totalPhysicalStockPackets > 0 && $article->pcs_per_packet > 0) {
                    $avalaibelPhysicalQuantity = $article->sold_quantity > 0 ? $totalPhysicalStockPackets - ($article->sold_quantity / $article->pcs_per_packet) : $totalPhysicalStockPackets;
                    $orderedPackets = $orderedArticle->ordered_quantity / $article->pcs_per_packet;
                    $pendingPackets = isset($orderedArticle->invoice_quantity) ? $orderedPackets - ($orderedArticle->invoice_quantity / $article->pcs_per_packet) : $orderedPackets;

                    $orderedArticle->total_quantity_in_packets = floor(min($pendingPackets, $avalaibelPhysicalQuantity));
                }
            
                return $orderedArticle->total_quantity_in_packets;
            });

            $order->articles = array_values($orderedArticles);
        }

        if (count($order->articles) == 0) {
            $order = ['error' => 'data not found'];
        }

        return response()->json($order);
    }
    public function getProgramDetails(Request $request) {
        $validator = Validator::make($request->all(), [
            "program_no" => "required|exists:payment_programs,program_no",
        ]);
        
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()->first()]);
        }

        $paymentProgram = PaymentProgram::with('customer', 'subCategory', 'order')->where("program_no", $request->program_no)->where('customer_id', $request->customer_id)->first();

        if ($paymentProgram->sub_category_type == "App\Models\BankAccount") {
            $paymentProgram->load('subCategory.bank');
        }

        $bankAccount = BankAccount::with('bank', 'subCategory')->where('sub_category_type', $paymentProgram->sub_category_type)->where('sub_category_id', $paymentProgram->sub_category_id)->get();

        if (count($bankAccount) > 0) {
            $paymentProgram->bank_accounts = $bankAccount;
        }

        return response()->json([
            'status' => 'success',
            'data' => $paymentProgram,
        ]);
    }

    public function setInvoiceType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "invoice_type" => "required|in:order,shipment",
        ]);
        
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()->first()]);
        }

        $user = Auth::user();
        $user->invoice_type = $request->invoice_type;
        $user->save();

        session()->flash('success', 'Invoice type updated.');

        return response()->json([
            'status' => 'success',
            'message' => 'Invoice type set as default.',
        ]);
    }
    public function getShipmentDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "shipment_no" => "required|exists:shipments,shipment_no",
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()->first()]);
        }

        // Get shipment by number
        $shipment = Shipment::where('shipment_no', $request->shipment_no)->first();

        if (!$shipment) {
            return response()->json(['error' => 'Shipment not found']);
        }
        
        // Get articles associated with shipment
        $shipment->articles = $shipment->getArticles();
        
        // Only continue if not filtering by only_order
        $validArticles = [];

        foreach ($shipment->articles as $articleData) {
            $article = $articleData['article'];

            if (!$article) continue;

            // Total stock from PhysicalQuantity
            $totalPackets = PhysicalQuantity::where("article_id", $article['id'])->sum("packets");

        // return response()->json([
        //     'status' => 'success',
        //     'shipment' => $shipment,
        // ]);

            // Available quantity calculation
            $availablePackets = $article['sold_quantity'] > 0
                ? $totalPackets - ($article['sold_quantity'] / $article['pcs_per_packet'])
                : $totalPackets;

            $availableStock = max(0, floor($availablePackets * $article['pcs_per_packet'])); // convert packets to pcs
            $articleData['article'] = $article;
            $articleData['available_stock'] = $availableStock;

            // Required shipment quantity (in pcs)
            $requiredShipmentQty = $articleData['shipment_quantity'];

            // Check if available stock is enough
            if ($availableStock < $requiredShipmentQty) {
                return response()->json(['error' => 'Stock is less than shipment quantity for article: ' . $article['article_no']]);
            }

            $validArticles[] = $articleData;
        }

        // Replace articles with valid filtered ones
        $shipment->articles = $validArticles;

        if (count($shipment->articles) === 0) {
            return response()->json(['error' => 'No articles found for this shipment']);
        }
        
        $Allcustomers = Customer::with(['invoices.shipment', 'user', 'city'])->whereIn('category', ['regular', 'site'])->whereHas('user', function ($query) {
            $query->where('status', 'active');
        })->when($shipment->city === 'karachi', function ($query) {
            $query->whereHas('city', function ($q) {
                $q->where('title', 'Karachi');
            });
        })
        ->when($shipment->city === 'other', function ($query) {
            $query->whereHas('city', function ($q) {
                $q->where('title', '!=', 'Karachi');
            });
        })
        // For 'all', no additional shipment.city condition
        ->get();

        $Customers = $Allcustomers->filter(function ($customer) use ($shipment) {
            // Check if any of the customer's invoices match the shipment number
            return !$customer->invoices->contains(function ($invoice) use ($shipment) {
                return 
                $invoice->shipment_no == $shipment->shipment_no ||
                ($invoice->shipment && $invoice->shipment->date == $shipment->date);
            });
        })->values()->toArray();

        return response()->json([
            'status' => 'success',
            'shipment' => $shipment,
            'customers' => $Customers,
        ]);
    }
}

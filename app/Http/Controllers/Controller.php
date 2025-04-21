<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\Order;
use App\Models\PaymentProgram;
use App\Models\PhysicalQuantity;
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
                $suppliers = Supplier::with('user')->whereHas('user', function ($query) {
                    $query->where('status', 'active');
                })->get();

                foreach ($suppliers as $supplier) {
                    $supplier['balance'] = 0;
                    $supplier['balance'] = number_format($supplier['balance'], 1, '.', ',');
                }

                return $suppliers;
                break;
            
            case 'customer':
                $customers = Customer::with('user')->whereHas('user', function ($query) {
                    $query->where('status', 'active');
                })->get();

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
    
        return redirect()->back()->with('success', 'Layout updated successfully.');
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

    public function getDetailsForInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "order_no" => "required|exists:orders,order_no",
        ]);
        
        if ($validator->fails()) {
            return response()->json(["error" => $validator->errors()->first()]);
        }

        $order = Order::with('customer')->where("order_no", $request->order_no)->first();
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
}

<?php

namespace App\Http\Controllers;

use App\Breakage;
use App\Goods;
use App\GoodsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Carbon;
use DB;

class BreakageController extends Controller
{
    public function index()
    {
        $breakages = Breakage::with('employee', 'goods')->get();
        $goods = Goods::get();
        return view('breakage.index', compact('breakages', 'goods'));
    }

    public function logGoods($breakage, $status = "OUT"){
        $latest_log = GoodsLog::getLatestLog($breakage->goods_id);
        $log = new GoodsLog();
        $log->goods_id = $breakage->goods_id;
        $log->status = $status;
        $log->date = Carbon::now()->format('Y-m-d H:i:s');
        $log->qty = $breakage->qty;
        if($status=="OUT"){
            $log->post_amount = ($latest_log->post_amount ?? 0) - $breakage->qty;
        } else {
            $log->post_amount = ($latest_log->post_amount ?? 0) + $breakage->qty;
        }
        $log->price = 0;
        $log->source = "Damage";
        $log->logable_id = $breakage->id;
        $log->logable_type = 'App\Breakage';
        $log->note = $breakage->reason;
        $log->created_at = Carbon::now()->format('Y-m-d H:i:s');
        $log->updated_at = Carbon::now()->format('Y-m-d H:i:s');
        $log->save();
    }

    public function store(Request $request)
    { 
        $input = $request->all();
        $input['created_by'] = Auth::user()->id;
        
        try{
            DB::beginTransaction();
            $breakage = Breakage::create($input);

            $this->logGoods($breakage, "OUT");

            DB::commit();
            $notif = [
                "type" => "success",
                "message" => "New breakage has been saved!"
            ];
        }catch(\Exception $e){
            DB::rollback();
            $notif = [
                "type" => "failed",
                "message" => "Failed to create new breakage!"
            ];
            dd($e);
        }
        return redirect()->route('breakage.index')->with($notif['type'], $notif['message']);
    }

    public function edit(Breakage $breakage){
        return view('breakage.edit', compact('breakage'));
    }

    public function update(Request $request, Breakage $breakage)
    {
        $input = $request->all();
        $input['created_by'] = Auth::user()->id;
        try{
            DB::beginTransaction();

            $breakage->goods_id = $input['_goods_id'];
            $breakage->qty = $input['_qty'];
            $breakage->reason = $input['_reason'];
            $this->logGoods($breakage, "IN");

            $breakage->update($input);
            $this->logGoods($breakage, "OUT");

            DB::commit();
            $notif = [
                "type" => "success",
                "message" => "'Breakage data has been updated!"
            ];
        }catch(\Exception $e){
            DB::rollback();
            $notif = [
                "type" => "failed",
                "message" => "Failed to update breakage!"
            ];
            dd($e);
        }
        return redirect()->route('breakage.index')->with($notif['type'], $notif['message']);
    }

    public function destroy(Breakage $breakage)
    {
        try{
            DB::beginTransaction();

            $this->logGoods($breakage, "IN");

            $breakage->delete();

            DB::commit();
            $notif = [
                "type" => "success",
                "message" => "'Breakage data has been deleted!"
            ];
        }catch(\Exception $e){
            DB::rollback();
            $notif = [
                "type" => "failed",
                "message" => "Failed to deleted breakage!"
            ];
            dd($e);
        }
        return redirect()->route('breakage.index')->with($notif['type'], $notif['message']);
    }
}

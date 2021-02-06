<?php

namespace App\Http\Controllers\API\Plan;

use App\Helpers\PlanHelper;
use App\Model\Plan;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Plan\StorePlanRequest;
use App\Http\Requests\Plan\UpdatePlanRequest;
use App\Http\Requests\Plan\DestroyPlanRequest;


class PlanController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        PlanHelper::need('plan_access');

        return $this->success('',['plans' => Plan::select('*')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePlanRequest $request)
    {
        $create = Plan::create([
            'amount' => $request->amount,
            'title' => $request->title,
            'description' => $request->description
        ]);
        if ($create === null){
            return $this->fail(trans('plan.create_error'));
        }
        else{
            return $this->success(trans('plan.create_success'));
        }

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePlanRequest $request)
    {

        $data = Plan::find($request->id);
        $update = $data->update([
            'amount' => $request->amount,
            'title' => $request->title,
            'description' => $request->description
        ]);
        if ($update === null){
            return $this->fail(trans('plan.update_error'));
        }
        else{
            return $this->success(trans('plan.update_success'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DestroyPlanRequest $request)
    {
        $plan = Plan::where('id',$request->only('id'))->first();
        if (!$plan){
            return $this->fail(trans('plan.delete_error'));
        }
        else{
            $plan->delete();
            return $this->success(trans('plan.delete_success'));
        }
    }
}

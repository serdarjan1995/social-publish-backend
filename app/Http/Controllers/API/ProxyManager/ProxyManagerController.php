<?php

namespace App\Http\Controllers\API\ProxyManager;

use App\Helpers\RoleHelper;
use App\Http\Controllers\ApiController;
use App\Http\Requests\ProxyManager\StoreRequest;
use App\Model\ProxyManager;
use Illuminate\Http\Request;

class ProxyManagerController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        RoleHelper::need('proxy_management');

        $get_proxy      =   ProxyManager::all();
        foreach ($get_proxy as $data) {
            $data['proxy_location']     =   [
                'name' => $data['proxy_location_name'],
                'code' => $data['proxy_location_code'],
            ];
            $data['selected'] = false;
        }
        return $this->success("success", ['get_proxy' => $get_proxy]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        RoleHelper::need('proxy_management');
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Request
     */
    public function store(StoreRequest $request)
    {
        RoleHelper::need('proxy_management');

        $proxy_location         =   $request->proxy_location;
        $proxy_location_name    =   $proxy_location['name'];
        $proxy_location_code    =   $proxy_location['code'];

        $proxy_create           =   ProxyManager::create([
            'proxy_name'            =>  $request->proxy_name,
            'proxy_location_name'   =>  $proxy_location_name,
            'proxy_location_code'   =>  $proxy_location_code,
            'proxy_limit'           =>  $request->proxy_limit,
            'status'                =>  $request->status,
        ]);
        return $proxy_create;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\ProxyManager  $proxyManager
     * @return \Illuminate\Http\Response
     */
    public function show(ProxyManager $proxyManager)
    {
        RoleHelper::need('proxy_management');
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\ProxyManager  $proxyManager
     * @return \Illuminate\Http\Response
     */
    public function edit(ProxyManager $proxyManager)
    {
        RoleHelper::need('proxy_management');
        //
    }

    public function statuschange(Request $request) {
        RoleHelper::need('proxy_management');

        if (ProxyManager::where('id', $request->id)) {
            $update_proxy = ProxyManager::where('id', $request->id)->update([
                'status' => $request->status
            ]);
            if ($update_proxy) {
                return $this->success('Update success', ['data' => $request]);
            } else {
                $this->fail('Database sql failed');
            }
        } else {
            return $this->fail('Proxy id not found');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\ProxyManager  $proxyManager
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StoreRequest $request)
    {
        RoleHelper::need('proxy_management');

        if (ProxyManager::where('id', $request->id)) {
            $update_proxy       =   ProxyManager::where('id', $request->id)->update([
                'proxy_name' => $request->proxy_name,
                'proxy_location_code' => $request->proxy_location['code'],
                'proxy_location_name' => $request->proxy_location['name'],
                'proxy_limit' => $request->proxy_limit,
                'status' => $request->status,
            ]);

            if ($update_proxy) {
                return $this->success('Update success', ['data' => $update_proxy]);
            } else {
                $this->fail('Database sql failed');
            }
        } else {
            return $this->fail('Proxy id not found');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\ProxyManager  $proxyManager
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProxyManager $proxyManager)
    {
        RoleHelper::need('proxy_management');

        //
    }
}

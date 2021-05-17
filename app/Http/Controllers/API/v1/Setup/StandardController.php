<?php

namespace App\Http\Controllers\API\v1\Setup;

use App\Models\Standard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class StandardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Return Standard::paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255|unique:standards',
            'hierachy' => 'required|integer|max:999|min:0|unique:standards'
        ]);

        return Standard::create([
            'name' => $request->name,
            'hierachy' => $request->hierachy,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Standard  $standard
     * @return \Illuminate\Http\Response
     */
    public function show(Standard $standard)
    {
        return $standard;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Standard  $standard
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Standard $standard)
    {
        $this->validate($request, [
            'name' => [
                'required', 'max:255',
                Rule::unique('standards')->ignore($standard)
            ],

            'hierachy' => [
                'required', 'integer', 'max:999', 'min:0',
                Rule::unique('standards')->ignore($standard)
            ]
        ]);

        $standard->update([
            'name' => $request->name,
            'hierachy' => $request->hierachy,
        ]);

        return $standard;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Standard  $standard
     * @return \Illuminate\Http\Response
     */
    public function destroy(Standard $standard)
    {
        $standard->delete();
        return response('', 204);
    }
}
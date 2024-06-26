<?php

namespace App\Http\Controllers\API\v1\Fee;

use Exception;
use App\Models\Student;
use App\Models\Chargeable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Jobs\AttachStudentToChargeableJob;

class ChargeableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Chargeable::paginate();
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
            'name' => 'required|max:255|string',
            'description' => 'required|max:255|string',
            'is_mandatory' => 'required|boolean',
            'assign_students' => 'required|boolean',
            'amount' => 'required|integer|min:0|max:9999999999',
            'tax_rate' => [
                'required', 'integer',
                Rule::in([0, 5, 12, 18, 28])
            ]
        ]);

        $amount_in_cent = ($request->amount) * 100;
        $gross_amount_in_cent = (($request->amount) * 100) * ((($request->tax_rate) / 100) + 1);

        $chargeable = Chargeable::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_mandatory' => $request->is_mandatory,
            'amount_in_cent' => $amount_in_cent,
            'tax_rate' => $request->tax_rate,
            'gross_amount_in_cent' => $gross_amount_in_cent
        ]);

        if ($request->boolean('is_mandatory') || $request->boolean('assign_students')) {
            $students = Student::all()->modelKeys();
            AttachStudentToChargeableJob::dispatch($chargeable, $students);
        }

        return $chargeable;
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Chargeable  $chargeable
     * @return \Illuminate\Http\Response
     */
    public function show(Chargeable $chargeable)
    {
        return $chargeable->load(['fees']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        return Chargeable::get();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Chargeable  $chargeable
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chargeable $chargeable)
    {
        $this->validate($request, [
            'name' => 'required|max:255|string',
            'description' => 'required|max:255|string',
            'is_mandatory' => 'required|boolean',
            'assign_students' => Rule::requiredIf(!$request->boolean('is_mandatory')),
            'amount' => 'required|integer|min:0|max:9999999999',
            'tax_rate' => [
                'required', 'integer',
                Rule::in([0, 5, 12, 18, 28])
            ]
        ]);

        $amount_in_cent = ($request->amount) * 100;
        $gross_amount_in_cent = (($request->amount) * 100) * ((($request->tax_rate) / 100) + 1);

        $chargeable->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_mandatory' => $request->is_mandatory,
            'amount_in_cent' => $amount_in_cent,
            'tax_rate' => $request->tax_rate,
            'gross_amount_in_cent' => $gross_amount_in_cent
        ]);

        $students = [];
        if ($request->boolean('is_mandatory') || $request->boolean('assign_students')) {
            $students = Student::all()->modelKeys();
        }
        AttachStudentToChargeableJob::dispatch($chargeable, $students);

        return $chargeable;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Chargeable  $chargeable
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chargeable $chargeable)
    {
        try {
            $chargeable->delete();
        } catch (Exception $ex) {
            return response([
                'header' => 'Dependency error',
                'message' => 'Other resources depend on this record.'
            ], 418);
        }

        return response('', 204);
    }
}

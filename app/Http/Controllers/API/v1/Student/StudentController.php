<?php

namespace App\Http\Controllers\API\v1\Student;

use Exception;
use App\Models\User;
use App\Models\Student;
use App\Models\Standard;
use Illuminate\Http\Request;
use App\Models\SectionStandard;
use App\Http\Controllers\Controller;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'search_string' => 'max:255',
            'standard_id' => 'nullable|exists:standards,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $standard_id = $request->input('standard_id') ?: '%%';
        $section_id = $request->input('section_id') ?: '%%';

        return User::role('student')
            ->whereHas('userDetail', function ($query) use ($request) {
                // $query->where('name', 'ILIKE', '%' . $request->search_string . '%'); // For PostgreSQL
                $query->where('name', 'LIKE', '%' . $request->search_string . '%'); // For MySQL
            })
            ->whereHas('student', function ($query) use ($standard_id, $section_id) {
                // $query->whereHas('sectionStandard', function ($query) use ($standard_id, $section_id) {
                //     $query->where('standard_id', 'ILIKE', $standard_id)
                //         ->where('section_id', 'ILIKE', $section_id);
                // }); // For PostgreSQL

                $query->whereHas('sectionStandard', function ($query) use ($standard_id, $section_id) {
                    $query->where('standard_id', 'LIKE', $standard_id)
                        ->where('section_id', 'LIKE', $section_id);
                }); // For MySQL
            })
            ->select('users.*')->whereHas('student')
            ->join('students', 'students.user_id', '=', 'users.id')
            ->orderBy('students.section_standard_id')->orderBy('students.roll_no')
            ->with([
                'student',
                'userDetail',
                'roles:id,name',
                'student.sectionStandard.section',
                'student.sectionStandard.standard',
            ])
            ->paginate();
    }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(Request $request)
    // {
    //     $this->validate($request, [
    //         'user_id' => 'required|min:1|exists:users,id',
    //         'section_standard_id' => 'required|min:1|exists:section_standard,id',
    //         'roll_no' => 'required|max:255',
    //     ]);

    //     $studentExist = Student::where('user_id', $request->user_id)->first();

    //     if($studentExist){
    //         return response([
    //             'header' => 'Already exists',
    //             'message' => 'Student is already allocated.'
    //         ], 418);
    //     }

    //     $student = Student::create($request->only(['user_id', 'section_standard_id', 'roll_no']));

    //     $subjects = $student->sectionStandard->standard->subjects()->get()->modelKeys();

    //     $student->subjects()->sync($subjects);

    //     return $student;
    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student)
    {
        $this->validate($request, [
            'section_id' => 'required|min:1|exists:sections,id',
            'standard_id' => 'required|min:1|exists:standards,id',
            'roll_no' => 'required|max:255',
        ]);

        $section_standard_id = SectionStandard::where('section_id', $request->section_id)->where('standard_id', $request->standard_id)->firstOrFail()->id;

        $student->update([
            'section_standard_id' => $section_standard_id,
            'roll_no' => $request->roll_no,
        ]);

        $subjects = Standard::find($request->standard_id)->subjects()->get()->modelKeys();
        $student->subjects()->sync($subjects);

        return $student;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        try {
            $student->subjects()->sync([]);
            $student->delete();
        } catch (Exception $ex) {
            return response([
                'header' => 'Dependency error',
                'message' => 'Other resources depend on this record.'
            ], 418);
        }

        return response('', 204);
    }
}

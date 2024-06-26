<?php

namespace App\Http\Controllers\API\v1\Homework;

use Auth;
use Exception;
use App\Models\Session;
use App\Models\Subject;
use App\Models\Homework;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeworkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('director')) {
            return Homework::with('SectionStandard.section:id,name', 'SectionStandard.standard:id,name', 'subject:id,name', 'chapter:id,name', 'creator:id', 'creator.userDetail:user_id,name')->orderBy('updated_at', 'desc')->paginate();
        } else if($user->hasRole('teacher')) {
            $subject_ids = $user->teacher->subjects->pluck('id');
            return Homework::whereIn('subject_id', $subject_ids)->with('SectionStandard.section:id,name', 'SectionStandard.standard:id,name', 'subject:id,name', 'chapter:id,name', 'creator:id', 'creator.userDetail:user_id,name')->whereIn('subject_id', $subject_ids)->orderBy('updated_at', 'desc')->paginate();
        }else{
            return $user->studentWithTrashed->homeworks()->with('SectionStandard.section:id,name', 'SectionStandard.standard:id,name', 'subject:id,name', 'chapter:id,name', 'creator:id', 'creator.userDetail:user_id,name')->orderBy('updated_at', 'desc')->paginate();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('director') !== true && $user->hasRole('teacher') !== true) {
            return response([
                'header' => 'Forbidden',
                'message' => 'Please Logout and Login again.'
            ], 401);
        }

        $this->validate($request, [
            'section_standard_id' => 'required|exists:section_standard,id',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'description' => 'required|max:255',
            'homework_from_date' => 'required|date',
            'homework_to_date' => 'required|date',
        ]);

        $session = Session::where('is_active', true)->firstOrFail();

        Homework::create([
            'session_id' => $session->id,
            'section_standard_id' => $request->section_standard_id,
            'subject_id' => $request->subject_id,
            'chapter_id' => $request->chapter_id,
            'description' => $request->description,
            'created_by' => $user->id,
            'homework_from_date' => $request->homework_from_date,
            'homework_to_date' => $request->homework_to_date,
        ]);

        $subject = Subject::find($request->subject_id);
        $students = $subject->students()->where('section_standard_id', $request->section_standard_id)->get();
        $subject->students()->sync($students);

        return response([
            'header' => 'Success',
            'message' => 'Homework created successfully.'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Homework  $homework
     * @return \Illuminate\Http\Response
     */
    public function show(Homework $homework, Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Homework  $homework
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Homework $homework)
    {
        $user = Auth::user();
        if ($user->hasRole('director') !== true && $user->hasRole('teacher') !== true) {
            return response([
                'header' => 'Forbidden',
                'message' => 'Please Logout and Login again.'
            ], 401);
        }

        if($user->hasRole('teacher') !== true && $homework->created_by != $user->id){
            return response([
                'header' => 'Forbidden',
                'message' => 'You are not authorized to update this homework.'
            ], 403);
        }

        $this->validate($request, [
            'homework_from_date' => 'required|date',
            'homework_to_date' => 'required|date',
            'description' => 'required|max:255',
        ]);

        $homework->update([
            'description' => $request->description,
            'created_by' => $user->id,
            'homework_from_date' => $request->homework_from_date,
            'homework_to_date' => $request->homework_to_date,
        ]);

        return response([
            'header' => 'Success',
            'message' => 'Homework updated successfully.'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Homework  $homework
     * @return \Illuminate\Http\Response
     */
    public function destroy(Homework $homework)
    {
        try {
            $homework->delete();
        } catch (Exception $ex) {
            return response([
                'header' => 'Dependency error',
                'message' => 'Other resources depend on this record.'
            ], 418);
        }

        return response('Deleted', 204);
    }
}

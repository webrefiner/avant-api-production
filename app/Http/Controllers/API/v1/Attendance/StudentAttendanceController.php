<?php

namespace App\Http\Controllers\API\v1\Attendance;


use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StudentAttendanceController extends Controller
{
    public function index(Request $request){
        $user = Auth::user();

        if ($user->hasRole('student') !== true) {
            return response([
                'header' => 'Forbidden',
                'message' => 'Please Logout and Login again.'
            ], 401);
        }

        $this->validate($request, [
            'attendance_date' => 'required|date',
        ]);
        
        $from = Carbon::create($request->attendance_date)->firstOfMonth();
        $to = Carbon::create($request->attendance_date)->endOfMonth();

        $attendances = Attendance::where('user_id', $user->id)->whereBetween('attendance_date', [$from, $to])->with(['sectionStandard.section', 'sectionStandard.standard', 'updator.userDetail', 'session'])->paginate(31);

        return $attendances;
    }
}

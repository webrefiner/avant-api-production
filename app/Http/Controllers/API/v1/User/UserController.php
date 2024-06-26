<?php

namespace App\Http\Controllers\API\v1\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user->select(['id', 'username', 'email'])->with('userDetail');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if (Auth::user()->id !== $user->id && Auth::user()->hasRole('director') !== true) {
            return response([
                'header' => 'Forbidden',
                'message' => 'Please Logout and Login again.'
            ], 401);
        }

        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users')->ignore($user)
            ],
            'phone' => 'required|max:255',
            'phone_alternate' => 'nullable|max:255',
            'dob' => 'nullable|date',
            'gender_id' => 'nullable|exists:genders,id',
            'language_id' => 'nullable|exists:languages,id',
            'religion_id' => 'nullable|exists:religions,id',
            'caste_id' => 'nullable|exists:castes,id',
            'blood_group_id' => 'nullable|exists:blood_groups,id',
            'address' => 'nullable|max:255',
            'pincode' => 'nullable|max:255',
            'fathers_name' => 'nullable|max:255',
            'mothers_name' => 'nullable|max:255',
            'pan_no' => 'nullable|max:255',
            'passport_no' => 'nullable|max:255',
            'voter_id' => 'nullable|max:255',
            'aadhar_no' => 'nullable|max:255',
            'dl_no' => 'nullable|max:255',
            'password' => 'nullable|min:8|max:24',
            'takenImageBase64' => 'nullable',
        ]);

        $user->update([
            'email' => $request->email,
        ]);

        if (!empty($request->password)) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->userDetail()->update($request->only([
            'name', 'phone', 'phone_alternate', 'dob', 'gender_id', 'language_id', 'religion_id', 'caste_id', 'blood_group_id', 'address', 'pincode', 'fathers_name', 'mothers_name', 'pan_no', 'passport_no', 'voter_id', 'aadhar_no', 'dl_no'
        ]));

        if (!empty($request->takenImageBase64)) {
            $base64_image = $request->takenImageBase64;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
                $data = substr($base64_image, strpos($base64_image, ',') + 1);
                $data = base64_decode($data);

                $image_name = 'profile_pictures/' . $user->id . '_' . time() . '.jpeg';
                Storage::disk('public')->put($image_name, $data);

                if($user->profilePicture()->exists()){
                    $url = $user->profilePicture->url;
                    Storage::disk('public')->delete($url);
                    $user->profilePicture()->update(['url' => $image_name]);
                }else{
                    $user->profilePicture()->create(['url' => $image_name]);
                }
            }
        }

        $profile = $user->where('id', $user->id)->with('userDetail', 'profilePicture')->first();

        $response = [
            'user' => $profile,
        ];

        return response($response, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}

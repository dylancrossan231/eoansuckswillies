<?php

namespace App\Http\Controllers;
use Auth;
use Hash;
use Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function _construct()
    {
      $this->middleware('auth');
    }

    public function index()
    {
      return view('user.profile');
    }

    public function update(Request $request)
    {
      $rules = [
          'name' => "required|string|min:2|max:191",
          'email'  => 'required|email|min:5|max:1000',
          'password' => "required|string|min:2|max:191",
          'image'  => 'required|image|max:1999',
      ];

      $request->validate($rules);


      $user = Auth::user();
      $user->name = $request->name;
      $user->email = $request->email;

      if($request->hasFile('image')){
        //get image fileaw
        $image =$request->image;
        //get just extension.
        $ext = $image->getClientOriginalExtension();
        //make a unique name .
        $filename = uniqid().'.'.$ext;
        //upload image.
        $image->storeAs('public/images', $filename);
        //delete previous image.
        Storage::delete("public/images/{$user->image}");
        //this column has a default value so you dont need to empty it.
        $user->image = $filename;

      }
      if($request->password){
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect()
        ->route('profile.index')
        ->with('status','Your profile has not been updated');
      }
    }
}

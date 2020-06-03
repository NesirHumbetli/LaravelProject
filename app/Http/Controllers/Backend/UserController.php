<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['user'] = User::all()->sortBy('user_must');
        return view('backend.users.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|Min:6'
        ]);

        if ($request->hasFile('user_file')) {

            $request->validate([
                'user_file' => 'required|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $file_name = uniqid() . '.' . $request->user_file->getClientOriginalExtension();
            $request->user_file->move(public_path('images/users'), $file_name);
        } else {
            $file_name = null;
        }


        $users = User::insert(
            [
                'name' => $request->name,
                'email' => $request->email,
                'user_file' => $file_name,
                'password' => Hash::make($request->password),
                'user_status' => $request->user_status,
            ]
        );

        if ($users) {
            return redirect(route('user.index'))->with('success', 'Əlavə Edildi');
        }
        return back()->with('error', 'Uğusuz Əməliyyat');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $users = User::where('id', $id)->first();
        return view('backend.users.edit')->with('users', $users);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);


        if ($request->hasFile('user_file')) {
            $request->validate([
                'user_file' => 'required|image|mimes:jpg,jpeg,png|Max:2048'
            ]);

            if (strlen($request->password) > 0) {
                $request->validate(['password' => 'required|Min:6']);

                $file_name = uniqid() . '.' . $request->user_file->getClientOriginalExtension();
                $request->user_file->move(public_path('images/users'), $file_name);

                $user = User::where('id', $id)->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'user_file' => $file_name,
                    'user_status' => $request->user_status,
                ]);

                if ($user) {
                    $path = 'images/users/' . $request->old_file;
                    if (file_exists($path)) {
                        @unlink(public_path($path));
                    }

                    return back()->with('success', 'Redaktə Edildi');
                } else {
                    return back()->with('error', 'Uğursuz Əməliyyat');
                }
            } else {


                $file_name = uniqid() . '.' . $request->user_file->getClientOriginalExtension();
                $request->user_file->move(public_path('images/users'), $file_name);

                $user = User::where('id', $id)->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'user_file' => $file_name,
                    'user_status' => $request->user_status,
                ]);

                if ($user) {
                    $path = 'images/users/' . $request->old_file;
                    if (file_exists($path)) {
                        @unlink(public_path($path));
                    }

                    return back()->with('success', 'Redaktə Edildi');
                } else {
                    return back()->with('error', 'Uğursuz Əməliyyat');
                }
            }
        } else {

            if (strlen($request->password) > 0) {

                $request->validate([
                    'password' => 'required|Min:6'
                ]);

                $user = User::where('id', $id)->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'user_status' => $request->user_status,
                ]);

                if ($user) {
                    return back()->with('success', 'Redaktə Edildi');
                } else {
                    return back()->with('error', 'Uğursuz Əməliyyat');
                }
            } else {
                $user = User::where('id', $id)->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'user_status' => $request->user_status,
                ]);

                if ($user) {
                    return back()->with('success', 'Redaktə Edildi');
                } else {
                    return back()->with('error', 'Uğursuz Əməliyyat');
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find(intval($id));
        if ($user->delete()) {
            @unlink(public_path('images/users/' . $user->user_file));
            echo true;
        }
        echo false;
    }

    public function sortable()
    {
        foreach ($_POST['item'] as $key => $value) {
            $users = User::find(intval($value));
            $users->user_must = intval($key);
            $users->save();
        }

        echo true;
    }
}

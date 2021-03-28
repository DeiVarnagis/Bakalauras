<?php

namespace App\Http\Controllers;

use App\Helpers\CollectionHelper;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function index()
    {
        $search = request("search");
        if (Str::length($search) > 0) {
            $users = User::where('name', 'like', "%" . $search . "%")->orWhere('email', 'like', "%" . $search . "%")->get();
        } else {
            $users = User::all();
        }

        return response()->json(["data" => CollectionHelper::paginateWithoutKey($users, 8)], 200);
    }

    public function allUsers()
    {
        return User::where('id', '!=', auth()->user()->id)->get();
    }

    public function show()
    {
        $user = User::find(request('id'));
        if ($user != null) {
            return  response()->json($user, 200);
        }
        return response()->json(["error" => 'Vartotojas nerastas'], 404);
    }

    public function update()
    {
        $user = User::find(request('id'));
        if ($user != null) {

            $this->validateEdit($user->id);
            $path = $user->src;
            if (request()->hasFile('src')) {

                if (Storage::disk('public')->exists($user->src)) {
                    Storage::disk('public')->delete($user->src);
                }
                $path = request()->file('src')->store('profileImages', 'public');
            }

            $user->update([
                'name' => request('name'),
                'surname' => request('surname'),
                'address' => request('address'),
                'email' => request('email'),
                'phoneNumber' => request('phoneNumber'),
                'birth' => request('birth'),
                'src' => $path
            ]);

            return response()->json(["data" => $user], 200);
        }
        return response()->json(["error" => "Vartotojas nerastas"], 404);
    }

    public function destroy()
    {
        $user = User::find(request('id'));
        if ($user != null) {

            if (auth()->user()->admin) {
                if (Storage::disk('public')->exists($user->src)) {
                    Storage::disk('public')->delete($user->src);
                }

                $user->destroy(request('id'));
                return response()->json(["data" => $user], 204);
            }
            return response()->json(["error" => "Unauthenticated"], 404);
        }
        return response()->json(["error" => "Vartotojas nerastas"], 404);
    }

    protected function validateEdit($id)
    {
        return request()->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:users,email,$id",
            'phoneNumber' => "string|nullable|max:255",
            'address' => "string|nullable|max:255",
            'birth' => "nullable|date|date_format:Y-m-d",
            'src' => 'nullable',

        ]);
    }
}

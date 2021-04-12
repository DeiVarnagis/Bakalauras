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
            $users = User::where('name', 'like', "%" . $search . "%")->orWhere('email', 'like', "%" . $search . "%")->orWhere('surname', 'like', "%" . $search . "%")->get();
        } else {
            $users = User::all();
        }

        return response()->json(["data" => CollectionHelper::paginateWithoutKey($users, 8)], 200);
    }

    public function allUsers()
    {
        return response()->json(User::all(), 200);
    }

    public function usersCount()
    {
        return response()->json(User::all()->count(), 200);
    }

    public function show()
    {
        $user = User::find(request('id'));
        if ($user == null) {
            return response()->json(["error" => 'Vartotojas nerastas'], 404);
        }
        return  response()->json($user, 200);
    }

    public function update()
    {
        $user = User::find(request('id'));
        if ($user == null) {

            return response()->json(["error" => "Vartotojas nerastas"], 404);
        }

        $path = $user->src;
        if (request()->hasFile('src')) {

            $this->deleteFileIfExist($user->src);
            $path = request()->file('src')->store('profileImages', 'public');
        }

        $user->update(array_merge($this->validateEdit($user->id), ['src' => $path]));

        return response()->json(["data" => $user], 200);
    }

    public function destroy()
    {
        $user = User::find(request('id'));
        if ($user == null) {
            return response()->json(["error" => "Vartotojas nerastas"], 404);
        }

        if (auth()->user()->admin) {
            $this->deleteFileIfExist($user->src);
            $user->destroy(request('id'));
            return response()->json(["data" => $user], 204);
        }
        return response()->json(["error" => "Unauthenticated"], 404);
    }

    protected function validateEdit($id)
    {
        return request()->validate([
            'name' => 'required|string|max:125',
            'surname' => 'required|string|max:125',
            'email' => "required|string|email|max:125|unique:users,email,$id",
            'phoneNumber' => "string|nullable|max:125",
            'address' => "string|nullable|max:125",
            'birth' => "nullable|date|date_format:Y-m-d",
            'src' => 'nullable',

        ]);
    }

    protected function deleteFileIfExist($src)
    {
        if (Storage::disk('public')->exists($src)) {
            Storage::disk('public')->delete($src);
        }
    }
}

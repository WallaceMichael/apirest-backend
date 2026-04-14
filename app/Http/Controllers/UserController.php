<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use Hash;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Symfony\Component\Console\Helper\TreeHelper;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $currentPage = $request->get('current_page') ?? 1;
        $regsPerPage = 3;

        $skip = ($currentPage - 1) * $regsPerPage;

        $users = User::skip($skip)->take($regsPerPage)->OrderByDesc('id')->get();

        return response()->json($users->toResourceCollection(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        try {
            $user = new User();
            $user->fill($data);
            $user->password = Hash::make(123);
            $user->save();
            return response()->json($user->toResource(), 201);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Falha ao inserir usuário!'
            ], 400);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        try {
            $user = User::findOrFail($id);
            return response()->json($user->toResource(), 200);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Falha ao encontrar o usuário!'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {

        $data = $request->validated();

        try {
            $user = User::findOrFail($id);
            $user->update($data);

            return response()->json($user->toResource(), 200);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Falha ao atualizar usuário!'
            ], 400);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        try {

            $removed = User::destroy($id);
            if(!$removed){
                throw new \Exception();
            }

            return response()->json(null, 204);

        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Falha ao deletar usuário!'
            ], 400);
        }
    }
}

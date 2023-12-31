<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Api\ApiMessages;

class UserController extends Controller
{
    private $user; 
    
    public function __construct(User $user){
        $this->user = $user;
    }
    /**
     
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = $this->user->paginate(10);
        return response()->json($users, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $data = $request->all();

        if(!$request->has('password') || !$request->get('password')){
            $message = new ApiMessages('É necessário informar uma senha para o usuário');
            return response()->json($message->getMessage(), 401);
        }

        Validator::make($data, [
            'phone'         => 'required',
            'mobile_phone'  => 'required'
        ])->validate();

        try {
            $data['password'] = bcrypt($data['password']);
            $user = $this->user->create($data);
            $user->profile()->create([
                'phone'         => $data['phone'],
                'mobile_phone'  => $data['mobile_phone'],
            ]);

            return response()->json([
                'data' => [
                    'msg' => 'Usuário cadastrado com sucesso'
                ]
            ]);
        } catch(\Exception $e){
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try{
            $user = $this->user->with('profile')->findOrFail($id);
            $user->profile->social_networks = unserialize($user->profile->social_networks);
            return response()->json([
                'data' => $user
            ]);
        } catch(\Exception $e){
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id)
    {
        //
        $data = $request->all();

        if($request->has('password') && $request->get('password')){
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        Validator::make($data, [
            'profile.phone'         => 'required',
            'profile.mobile_phone'  => 'required'
        ])->validate();


        try{
            $user = $this->user->findOrFail($id);
            $user->update($data);
            $profile = $data['profile'];
            $profile['social_networks'] = serialize($profile['social_networks']);

            $user->profile()->update($profile);

            return response()->json([
                'data' => [
                    'msg' => 'Usuário atualizado com sucesso'
                ]
            ], 200);
        } catch(\Exception $e){
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $user = $this->user->findOrFail($id);
            $user->delete();
            return response()->json([
                'data' => [
                    'msg' => 'Usuário excluído com sucesso'
                ]
            ], 200);
        } catch(\Exception $e){
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }
}

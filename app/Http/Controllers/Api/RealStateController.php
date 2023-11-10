<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RealStateRequest;
use App\Models\RealState;
use App\Api\ApiMessages;

class RealStateController extends Controller
{
    private $realState;
    //
    public function __construct(RealState $realState){
    	$this->realState = $realState;
    }

    public function index(){
        $realStates = auth('api')->user()->real_state();

    	return response()->json($realStates->paginate(10), 200);
    }
    
    public function store(RealStateRequest $request){
    	$data = $request->all();
        $data['user_id'] = auth('api')->user()->id;
        
    	try{
    		
    		$realState = $this->realState->create($data);

            if(isset($data['categories']) && count($data['categories'])){
                $realState->categories()->sync($data['categories']);
            }
            $images = $data['images'];
            
            if($images){
                $imagesUploaded = [];
                foreach($images as $image){
                    $path = $image->store('images', 'public');
                    $imagesUploaded[] = ['photo' => $path, 'is_thumb' => false];
                }
                $realState->photos()->createMany($imagesUploaded);
            }

    		return response()->json([
    			'data' => [
    				'msg' => 'Imóvel cadastrado com sucesso'
    			]
    		]);
    	}catch(\Exception $e){
    		$message = new ApiMessages($e->getMessage());
    		return response()->json($message->getMessage(), 401);
    	}
    	
    }

    public function update($id, RealStateRequest $request){
    	$data = $request->all();

    	try{
    		
    		$realState = auth('api')->user()->real_state()->findOrFail($id);
    		$realState->update($data);

            if(isset($data['categories']) && count($data['categories'])){
                $realState->categories()->sync($data['categories']);
            }

            $images = $data['images'];
            
            if($images){
                $imagesUploaded = [];
                foreach($images as $image){
                    $path = $image->store('images', 'public');
                    $imagesUploaded[] = ['photo' => $path, 'is_thumb' => false];
                }
                $realState->photos()->createMany($imagesUploaded);
            }
            
    		return response()->json([
    			'data' => [
    				'msg' => 'Imóvel atualizado com sucesso'
    			]
    		]);
    	}catch(\Exception $e){
    		$message = new ApiMessages($e->getMessage());
    		return response()->json($message->getMessage(), 401);
    	}
    }
    public function destroy($id){
    	try{
    		
    		$realState = auth('api')->user()->real_state()->findOrFail($id);
    		$realState->delete();

    		return response()->json([
    			'data' => [
    				'msg' => 'Imóvel removido com sucesso'
    			]
    		]);
    	}catch(\Exception $e){
    		$message = new ApiMessages($e->getMessage());
    		return response()->json($message->getMessage(), 401);
    	}
    }

    public function show($id){
    	try{
    		
    		$realState = auth('api')->user()->real_state()->with('photos')->findOrFail($id)->makeHidden('thumb');

    		return response()->json([
    			'data' => $realState
    		]);
    	}catch(\Exception $e){
    		$message = new ApiMessages($e->getMessage());
    		return response()->json($message->getMessage(), 401);
    	}
    }
}


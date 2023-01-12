<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserApiController extends Controller
{
    // get api for fetch users
    public function showUser($id=null){
        if($id==''){
            $users= User::get();
            return response()->json(['users'=>$users],200);
        }else{
            $users= User::find($id);
            return response()->json(['users'=>$users],200);
        }
    }

    // post api for add single user
    public function addUser(Request $request){
        if($request->isMethod('post')){
            $data=$request->all();
            //return $data;

            $rules=[
                'name'=>'required',
                'email'=>'required|email|unique:users',
                'password'=>'required',
            ];

            $customMsg=[
                'name.required'=>'Name is Required',
                'email.required'=>'Email is Required',
                'email.email'=>'Email must be a Valid Email',
                'password.required'=>'Password is Required',
            ];

            $validator=Validator::make($data,$rules,$customMsg);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }

            $user= new User();
            $user->name=$data['name'];
            $user->email=$data['email'];
            $user->password=bcrypt($data['password']);
            $user->save();
            $message='User Successfully Added';
            return response()->json(['message'=>$message],201);
        }
    }

    // post api for add multiple user
    public function addMultipleUser(Request $request){
        if($request->isMethod('post')){
            $data=$request->all();
            //return $data;

            $rules=[
                'users.*.name'=>'required',
                'users.*.email'=>'required|email|unique:users',
                'users.*.password'=>'required',
            ];

            $customMsg=[
                'users.*.name.required'=>'Name is Required',
                'users.*.email.required'=>'Email is Required',
                'users.*.email.email'=>'Email must be a Valid Email',
                'users.*.password.required'=>'Password is Required',
            ];

            $validator=Validator::make($data,$rules,$customMsg);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }

            foreach($data['users'] as $adduser){

                $user= new User();
                $user->name=$adduser['name'];
                $user->email=$adduser['email'];
                $user->password=bcrypt($adduser['password']);
                $user->save();
                $message='User Successfully Added'; 
            }
            return response()->json(['message'=>$message],201);
        }
    }

    // put api for update user details
    public function updateUserDetails(Request $request,$id){
        if($request->isMethod('put')){
            $data=$request->all();
            //return $data;

            $rules=[
                'name'=>'required',
                'password'=>'required'
            ];

            $customMsg=[
                'name.required'=>'Name is Required',
                'password.required'=>'Password is Required'
            ];

            $validator=Validator::make($data,$rules,$customMsg);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }

            $user= User::findOrFail($id);
            $user->name=$data['name'];
            $user->password=bcrypt($data['password']);
            $user->update();
            $message='User Successfully Updated';
            return response()->json(['message'=>$message],202);
        }
    }

    // patch api for update single record 
    public function updateSingleRecord(Request $request,$id){
        if($request->isMethod('patch')){
            $data=$request->all();
            //return $data;

            $rules=[
                'name'=>'required'
            ];

            $customMsg=[
                'name.required'=>'Name is Required'
            ];

            $validator=Validator::make($data,$rules,$customMsg);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }

            $user= User::findOrFail($id);
            $user->name=$data['name'];
            $user->update();
            $message='User Successfully Updated';
            return response()->json(['message'=>$message],202);
        }
    }

    // delete api for delete single user
    public function deleteUser($id=null){
        User::findOrFail($id)->delete();
        $message= 'User Successfully Deleted';
        return response()->json(['message'=>$message],200);
    }

    //delete api for delete single user with json
    public function deleteUserJson(Request $request){
        if($request->isMethod('delete')){
            $data=$request->all();
            User::where('id',$data['id'])->delete();
            $message= 'User Successfully Deleted';
            return response()->json(['message'=>$message],200);
        }
    }

    // delete api for delete multiple user
    public function deleteMultipleUser($ids){
        $ids=explode(',',$ids);
        User::whereIn('id',$ids)->delete();
        $message= 'User Successfully Deleted';
        return response()->json(['message'=>$message],200);
    }

    // delete api for delete multiple user with json
    public function deleteMultipleUserJson(Request $request){
        $header= $request->header('Authorization');
        if($header==''){
            $message= 'Authorization is Required';
            return response()->json(['message'=>$message],422);
        }else{
            if($header=='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6InNoYXBuYSIsImlhdCI6MTUxNjIzOTAyMn0.H1K61bn7XWxGJ4ri0-QwjDA1WM9VT-lXw1Pj2_Dn35M'){
                if($request->isMethod('delete')){
                    $data=$request->all();
                    User::whereIn('id',$data['ids'])->delete();
                    $message= 'User Successfully Deleted';
                    return response()->json(['message'=>$message],200);
                }
            }else{
                $message= 'Authorization does not match';
                return response()->json(['message'=>$message],422);
            }
        }

        
    }

    // register api user passport
    public function registerUserUsingPassport(Request $request){
        if($request->isMethod('post')){
            $data=$request->all();
            //return $data;

            $rules=[
                'name'=>'required',
                'email'=>'required|email|unique:users',
                'password'=>'required',
            ];

            $customMsg=[
                'name.required'=>'Name is Required',
                'email.required'=>'Email is Required',
                'email.email'=>'Email must be a Valid Email',
                'password.required'=>'Password is Required',
            ];

            $validator=Validator::make($data,$rules,$customMsg);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }

            $user= new User();
            $user->name=$data['name'];
            $user->email=$data['email'];
            $user->password=bcrypt($data['password']);
            $user->save();

            if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])){
                $user=User::where('email',$data['email'])->first();
                $access_token=$user->createToken($data['email'])->accessToken;
                User::where('email',$data['email'])->update(['access_token'=>$access_token]);
                $message='User Successfully Registered';
                return response()->json(['message'=>$message,'access_token'=>$access_token],201);
            }else{
                $message='Opps! Something went wrong';
                return response()->json(['message'=>$message],422);
            }

        }
    }

    // login api user passport
    public function loginUserUsingPassport(Request $request){
        if($request->isMethod('post')){
            $data=$request->all();
            //return $data;

            $rules=[
                'email'=>'required|email|exists:users',
                'password'=>'required',
            ];

            $customMsg=[
                'email.required'=>'Email is Required',
                'email.email'=>'Email must be a Valid Email',
                'email.exists'=>'Email does not exists',
                'password.required'=>'Password is Required',
            ];

            $validator=Validator::make($data,$rules,$customMsg);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }

            if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])){
                $user=User::where('email',$data['email'])->first();
                $access_token=$user->createToken($data['email'])->accessToken;
                User::where('email',$data['email'])->update(['access_token'=>$access_token]);
                $message='User Successfully Login';
                return response()->json(['message'=>$message,'access_token'=>$access_token],201);
            }else{
                $message='Invalid email or password';
                return response()->json(['message'=>$message],422);
            }

        }
    }
}

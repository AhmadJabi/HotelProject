<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public $successStatus = 200;
    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(){
        if(Auth::attempt(['name' => request('name'), 'password' => request('password')])){
            $user = Auth::user();
            $success['id'] =  $user->id;
            $success['name'] =  $user->name;
            $success['access_token'] =  $user->createToken('MyApp')-> accessToken;
            $success['expiry_date'] =  date('Y-m-d H:i:s', strtotime('+1 years'));
            return response()->json(['success' => $success], $this-> successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            //'email' => 'email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['email']=$input['name'].'@gmail.com';
        $user = User::create($input);
        //$success['token'] =  $user->createToken('MyApp')-> accessToken;
        $success['name'] =  $user->name;
        $success['id'] =  $user->id;
        return response()->json(['success'=>$success], $this-> successStatus);
    }
    /**
     * createHotel api
     *
     * @return \Illuminate\Http\Response
     */

     public function createHotel(Request $request){

         try {
             $name = $request->input('name');
             $rate = $request->input('rate');
             DB::insert('insert into hotels (name, rate) values (?, ?)', [$name, $rate]);
             $success['id'] =  (DB::select('select id from hotels where name = ?', [$name]));
             $success['name'] =  $name;
             $success['rate'] =  $rate;

             return response()->json(['success'=>$success], $this-> successStatus);
         } catch (\Illuminate\Database\QueryException $ex) {
             return response()->json(['error'=>$ex->getMessage()], 401);
         }


     }

     public function getHotel(){
         try {


             $success =  (DB::select('select id, name, rate from hotels'));

             return response()->json(['success'=>$success], $this-> successStatus);
         } catch (\Illuminate\Database\QueryException $ex) {
             return response()->json(['error'=>$ex->getMessage()], 401);
         }
     }

    public function getHotelById(Request $request){
        try {
            $id = $request->input('id');

            $success['hotel'] =  (DB::select('select id, name, rate from hotels where id = ? ',[$id]));
            if(!$success['hotel']){
                return response()->json(['error'=>'Error'], 401);
            }
            $success['room']=(DB::select('select id, code from rooms where hotel_id = ? ',[$id]));
            return response()->json(['success'=>$success], $this-> successStatus);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['error'=>$ex->getMessage()], 401);
        }
    }

    public function createRoom(Request $request){

        try {
            $code = $request->input('code');
            $hotel_id = $request->input('hotel_id');
            DB::insert('insert into rooms (code, hotel_id) values (?, ?)', [$code, $hotel_id]);
            $success['id'] =  (DB::select('select id from rooms where code = ?', [$code]));
            $success['code'] =  $code;
            $success['hotel id'] =  $hotel_id;

            return response()->json(['success'=>$success], $this-> successStatus);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['error'=>$ex->getMessage()], 401);
        }


    }

    public function getRoomsByHotelId(Request $request){
        try {
            $id = $request->input('id');
            $success =  (DB::select('select id, code, hotel_id from rooms where hotel_id = ? ',[$id]));
            if(!$success){
                return response()->json(['error'=>'Error'], 401);
            }

            foreach ($success as $data) {
                if( ( DB::select('select room_id from reservations where room_id = ? ',[$data->id]) ) ){
                    $data->status='not available';
                } else{
                    $data->status="available";
                }

            }
            return response()->json(['success'=>$success], $this-> successStatus);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['error'=>$ex->getMessage()], 401);
        }
    }

    public function reserveRoom(Request $request){
        try {
            $UserId = Auth::user()->id;
            $id = $request->input('id');
            $success =  (DB::select('select id, room_id, user_id,from_date from reservations where user_id = ?  and room_id = ? ',[$UserId, $id]));
            if(!$success){
                return response()->json(['error'=>'no user found'], 401);
            }else{
                return response()->json(['success'=>$success], $this-> successStatus);
            }

        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['error'=>$ex->getMessage()], 401);
        }
    }

    public function releaseRoom(Request $request){
        try {
            $UserId = Auth::user()->id;
            $id = $request->input('id');
            $success =  (DB::select('select id, room_id, user_id,from_date, to_date from reservations where user_id = ?  and room_id = ? ',[$UserId, $id]));
            if(!$success){
                return response()->json(['error'=>'no user found'], 401);
            }else{
                return response()->json(['success'=>$success], $this-> successStatus);
            }

        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['error'=>$ex->getMessage()], 401);
        }
    }
}
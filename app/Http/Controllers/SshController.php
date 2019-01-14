<?php

namespace App\Http\Controllers;

use App\Key;
use App\Server;
use Illuminate\Http\Request;

class SshController extends Controller
{
    public static $protected = true;
    
    public function index(){
        $keys = Key::where('user_id',\Auth::id())->get();
        $servers = Server::all();
        foreach ($keys as $key){
            $key->server_name = $servers->where('_id',$key->server_id)->first()->name;
        }
        return view('keys.index',[
            "keys" => $keys,
            "servers" => $servers
        ]);
    }

    public function add(Request $request){
        $data = $request->all();
        $server = Server::where('_id',request('server_id'))->first();
        $key = new Key($data);
        $key->user_id = \Auth::id();
        $key->save();
        $output = Key::init(request('username'),request('password'),
            $server->ip_address,$server->port,\Auth::id());
        return [
            "result" => 200,
            "log" => $output
        ];
    }
}
